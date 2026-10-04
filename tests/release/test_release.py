import copy
import hashlib
from io import BytesIO
import json
from pathlib import Path
import subprocess
import sys
import tempfile
import unittest
from unittest.mock import patch
from urllib.error import HTTPError


sys.path.insert(0, str(Path(__file__).resolve().parents[2] / "tools"))
import release


class FakeGitHub:
    def __init__(self, root_commit, entries):
        self.tags = {(release.ROOT_REPOSITORY, "3.2.0"): root_commit}
        self.existing = {(release.ROOT_REPOSITORY, "3.2.0"): {
            "draft": False, "prerelease": False,
            "body": "## 🐛 Fixes\n\n- Fix raxos behavior.\n",
            "assets": [{"name": release.ASSET_NAME, "state": "uploaded"}],
        }}
        self.history = {}
        self.comparisons = {}
        self.runs = [{"id": 10, "head_sha": root_commit, "head_branch": "main", "event": "push", "status": "completed", "conclusion": "success"}]
        self.writes = []
        self.failure = None
        for entry in entries.values():
            self.tags[entry["repository"], "3.1.0"] = entry["base_commit"]

    def tag_commit(self, repository, tag):
        return self.tags.get((repository, tag))

    def releases(self, repository):
        own = [dict(body, tag_name=tag, published_at="2026-10-03T12:00:00Z") for (repo, tag), body in self.existing.items() if repo == repository]
        return own + self.history.get(repository, [])

    def request(self, method, path, payload=None, optional=False):
        if method == "POST":
            self.writes.append((path, copy.deepcopy(payload)))
            if path == self.failure:
                self.failure = None
                raise release.ReleaseError("Temporary API failure")
            repository = path.split("/", 3)[1:3]
            repository = "/".join(repository)
            if path.endswith("/git/refs"):
                self.tags[repository, payload["ref"].removeprefix("refs/tags/")] = payload["sha"]
            else:
                self.existing[repository, payload["tag_name"]] = copy.deepcopy(payload)
            return {"id": len(self.writes)}
        if "/actions/workflows/" in path:
            return {"workflow_runs": self.runs}
        if "/compare/" in path:
            return {"status": self.comparisons.get(path, "ahead")}
        repository, tag = path.removeprefix("repos/").split("/releases/tags/")
        return self.existing.get((repository, tag))


class ReleaseFixture(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory(prefix="raxos-release-")
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name) / "repository"
        self.root.mkdir()
        self.directory = Path(self.temp.name) / "drafts"
        self.directory.mkdir(parents=True)
        self.bundle = self.directory / release.ASSET_NAME
        self.entries = {
            "cache": {"repository": "basmilius/raxos-cache", "commit": "a" * 40, "base": "3.1.0", "base_commit": "1" * 40},
            "router": {"repository": "basmilius/raxos-router", "commit": "b" * 40, "base": "3.1.0", "base_commit": "2" * 40},
        }
        self.manifest = {"schema": 1, "tag": "3.2.0", "prerelease": False, "notes": "## 🐛 Fixes\n\n- Fix raxos behavior.\n", "libraries": self.entries}
        for name in ("raxos", "cache", "router"):
            (self.directory / f"{name}.md").write_text(f"## 🐛 Fixes\n\n- Fix {name} behavior.\n")
        (self.root / ".gitmodules").write_text("".join(f'[submodule "{path}"]\n    path = {path}\n    url = https://github.com/{entry["repository"]}.git\n' for path, entry in self.entries.items()))
        self.git("init", "-b", "main")
        self.git("config", "user.name", "Release test")
        self.git("config", "user.email", "release-test@example.invalid")
        self.git("add", ".gitmodules")
        for path, entry in self.entries.items():
            self.git("update-index", "--add", "--cacheinfo", f"160000,{entry['commit']},{path}")
        self.git("-c", "commit.gpgsign=false", "commit", "-m", "Release fixture")
        self.commit = release.git(self.root, "rev-parse", "HEAD")
        self.manifest["root_commit"] = self.commit
        for path, entry in self.entries.items():
            entry["notes"] = (self.directory / f"{path}.md").read_text()
        self.write_manifest()
        self.api = FakeGitHub(self.commit, self.entries)
        self.sleep = patch.object(release.time, "sleep")
        self.sleep.start()
        self.addCleanup(self.sleep.stop)

    def git(self, *args):
        subprocess.run(["git", "-C", str(self.root), *args], capture_output=True, check=True)

    def write_manifest(self):
        (self.directory / "manifest.json").write_text(json.dumps(self.manifest))
        self.bundle.write_text(json.dumps(self.manifest))

    def preflight(self):
        return release.preflight(self.root, self.bundle, "3.2.0", self.api, self.api)

    def test_manifest_uses_pinned_commits_and_includes_all_libraries(self):
        self.assertEqual(self.manifest, release.load_plan(self.root, self.bundle, "3.2.0", "HEAD"))
        self.entries["router"]["commit"] = "c" * 40
        self.write_manifest()
        with self.assertRaisesRegex(release.ReleaseError, "pinned commit"):
            release.load_plan(self.root, self.bundle, "3.2.0", "HEAD")

    def test_missing_or_extra_library_blocks_publication(self):
        for change in ("missing", "extra"):
            with self.subTest(change=change):
                plan = copy.deepcopy(self.manifest)
                if change == "missing":
                    del plan["libraries"]["router"]
                else:
                    plan["libraries"]["unknown"] = self.entries["router"]
                self.bundle.write_text(json.dumps(plan))
                with self.assertRaisesRegex(release.ReleaseError, "every submodule"):
                    self.preflight()
                self.assertEqual([], self.api.writes)

    def test_unexpected_submodule_owner_is_rejected(self):
        config = self.root / ".gitmodules"
        config.write_text(config.read_text().replace("github.com/basmilius/", "github.com/other/"))
        self.git("add", ".gitmodules")
        self.git("-c", "commit.gpgsign=false", "commit", "-m", "Unexpected owner")
        with self.assertRaisesRegex(release.ReleaseError, "Unexpected submodule"):
            release.libraries(self.root, "HEAD")

    def test_mismatched_gitlink_and_declaration_is_rejected(self):
        self.git("update-index", "--force-remove", "cache")
        self.git("-c", "commit.gpgsign=false", "commit", "-m", "Missing gitlink")
        with self.assertRaisesRegex(release.ReleaseError, "gitlinks"):
            release.libraries(self.root, "HEAD")

    def test_latest_exact_main_ci_run_must_be_successful(self):
        for runs in ([], [dict(self.api.runs[0], conclusion="failure")], [dict(self.api.runs[0], status="in_progress")], [dict(self.api.runs[0], head_sha="c" * 40)], [dict(self.api.runs[0], event="pull_request")], [self.api.runs[0], dict(self.api.runs[0], id=11, conclusion="failure")]):
            with self.subTest(runs=runs):
                self.api.runs = runs
                with self.assertRaisesRegex(release.ReleaseError, "Tests workflow"):
                    self.preflight()
                self.assertEqual([], self.api.writes)

    def test_root_release_must_match_checkout_and_prerelease_flag(self):
        self.api.tags[release.ROOT_REPOSITORY, "3.2.0"] = "c" * 40
        with self.assertRaisesRegex(release.ReleaseError, "Checkout"):
            self.preflight()
        self.api.tags[release.ROOT_REPOSITORY, "3.2.0"] = self.commit
        for flag in ("draft", "prerelease"):
            with self.subTest(flag=flag):
                self.api.existing[release.ROOT_REPOSITORY, "3.2.0"][flag] = True
                with self.assertRaisesRegex(release.ReleaseError, "root release"):
                    self.preflight()
                self.api.existing[release.ROOT_REPOSITORY, "3.2.0"][flag] = False

    def test_unpushed_library_or_divergent_base_blocks_all_writes(self):
        for path in (f"repos/basmilius/raxos-router/compare/{'b' * 40}...main", f"repos/basmilius/raxos-router/compare/{'2' * 40}...{'b' * 40}"):
            with self.subTest(path=path):
                self.api.comparisons = {path: "diverged"}
                with self.assertRaisesRegex(release.ReleaseError, "ancestor"):
                    self.preflight()
                self.assertEqual([], self.api.writes)

    def test_existing_wrong_tag_or_changed_base_is_never_overwritten(self):
        for tag in ("3.2.0", "3.1.0"):
            with self.subTest(tag=tag):
                self.api.tags["basmilius/raxos-router", tag] = "f" * 40
                with self.assertRaises(release.ReleaseError):
                    self.preflight()
                self.assertEqual([], self.api.writes)
                self.api.tags.pop(("basmilius/raxos-router", "3.2.0"), None)

    def test_newer_published_base_requires_updated_changelog(self):
        self.api.history["basmilius/raxos-router"] = [{"tag_name": "3.1.1", "draft": False, "prerelease": False, "published_at": "2026-10-01T00:00:00Z"}]
        with self.assertRaisesRegex(release.ReleaseError, "latest published release"):
            self.preflight()

    def test_dry_run_creates_nothing_and_uses_repo_specific_notes(self):
        prepared = self.preflight()
        release.publish(prepared, self.api, True)
        self.assertEqual([], self.api.writes)
        self.assertEqual(["dry-run", "dry-run"], [item["status"] for item in prepared])
        self.assertIn("Fix cache behavior", prepared[0]["payload"]["body"])
        self.assertNotIn("Fix router behavior", prepared[0]["payload"]["body"])
        self.assertIn("raxos-cache/compare/3.1.0...3.2.0", prepared[0]["payload"]["body"])

    def test_publish_creates_exact_tags_and_releases_once(self):
        prepared = self.preflight()
        release.publish(prepared, self.api, False)
        self.assertEqual(4, len(self.api.writes))
        for item in prepared:
            self.assertEqual(item["commit"], self.api.tags[item["repository"], "3.2.0"])
            self.assertEqual("true", item["payload"]["make_latest"])
        release.publish(self.preflight(), self.api, False)
        self.assertEqual(4, len(self.api.writes))

    def test_partial_failure_resumes_without_republishing_completed_library(self):
        self.api.failure = "repos/basmilius/raxos-router/releases"
        prepared = self.preflight()
        with self.assertRaisesRegex(release.ReleaseError, "Temporary API"):
            release.publish(prepared, self.api, False)
        self.assertEqual(["published", "pending"], [item["status"] for item in prepared])
        release.publish(self.preflight(), self.api, False)
        release_paths = [path for path, _ in self.api.writes if path.endswith("/releases")]
        self.assertEqual(1, release_paths.count("repos/basmilius/raxos-cache/releases"))
        self.assertEqual(2, release_paths.count("repos/basmilius/raxos-router/releases"))

    def test_tag_created_between_preflight_and_publish_is_verified(self):
        prepared = self.preflight()
        self.api.tags["basmilius/raxos-cache", "3.2.0"] = "f" * 40
        with self.assertRaisesRegex(release.ReleaseError, "different commit"):
            release.publish(prepared, self.api, False)
        self.assertEqual([], self.api.writes)

    def test_existing_draft_or_different_notes_block_retry(self):
        prepared = self.preflight()
        self.api.tags["basmilius/raxos-cache", "3.2.0"] = "a" * 40
        for mutation in ({"draft": True}, {"body": "Other notes"}, {"prerelease": True}, {"name": "Other title"}):
            with self.subTest(mutation=mutation):
                self.api.existing["basmilius/raxos-cache", "3.2.0"] = dict(prepared[0]["payload"], **mutation)
                with self.assertRaisesRegex(release.ReleaseError, "differs"):
                    self.preflight()

    def test_prerelease_never_becomes_latest_and_has_banner(self):
        target = "3.2.0-beta.1"
        self.manifest.update(tag=target, prerelease=True)
        self.write_manifest()
        self.api.tags[release.ROOT_REPOSITORY, target] = self.commit
        self.api.existing[release.ROOT_REPOSITORY, target] = dict(self.api.existing[release.ROOT_REPOSITORY, "3.2.0"], prerelease=True)
        prepared = release.preflight(self.root, self.bundle, target, self.api, self.api)
        self.assertEqual("false", prepared[0]["payload"]["make_latest"])
        self.assertTrue(prepared[0]["payload"]["body"].startswith("🧪 This is a pre-release."))

    def test_progress_artifact_has_notes_and_partial_statuses(self):
        prepared = self.preflight()
        prepared[0]["status"] = "published"
        output = self.root / "output" / "plan.json"
        release.record(prepared, output)
        self.assertEqual(prepared, json.loads(output.read_text()))

    def test_missing_or_unfinished_release_asset_blocks_all_writes(self):
        root_release = self.api.existing[release.ROOT_REPOSITORY, "3.2.0"]
        for assets in ([], [{"name": release.ASSET_NAME, "state": "starter"}], [root_release["assets"][0]] * 2):
            with self.subTest(assets=assets):
                root_release["assets"] = assets
                with self.assertRaisesRegex(release.ReleaseError, "uploaded"):
                    self.preflight()
                self.assertEqual([], self.api.writes)

    def test_downloaded_bundle_must_match_asset_digest(self):
        asset = self.api.existing[release.ROOT_REPOSITORY, "3.2.0"]["assets"][0]
        asset["digest"] = "sha256:" + hashlib.sha256(self.bundle.read_bytes()).hexdigest()
        self.preflight()
        self.bundle.write_text(self.bundle.read_text() + "\n")
        with self.assertRaisesRegex(release.ReleaseError, "digest"):
            self.preflight()

    def test_changed_root_commit_or_root_notes_are_rejected(self):
        self.manifest["root_commit"] = "f" * 40
        self.write_manifest()
        with self.assertRaisesRegex(release.ReleaseError, "selected root commit"):
            self.preflight()
        self.manifest["root_commit"] = self.commit
        self.manifest["notes"] = "## 🐛 Fixes\n\n- Other root notes.\n"
        self.write_manifest()
        with self.assertRaisesRegex(release.ReleaseError, "Root release notes differ"):
            self.preflight()

    def test_bundle_builds_reviewed_notes_outside_checkout(self):
        (self.directory / "cache.md").write_text("## ⚡ Performance\n\n- Batch cache invalidation.\n")
        bundle = release.build_bundle(self.root, self.directory, "3.2.0", "HEAD")
        result = release.load_plan(self.root, bundle, "3.2.0", "HEAD")
        self.assertIn("Batch cache invalidation", result["libraries"]["cache"]["notes"])
        self.assertFalse((self.root / "releases").exists())

    def test_draft_notes_and_workspace_scratch_directories_are_rejected(self):
        (self.directory / "cache.md").write_text("DRAFT\n\n## 🐛 Fixes\n")
        with self.assertRaisesRegex(release.ReleaseError, "draft text"):
            release.build_bundle(self.root, self.directory, "3.2.0", "HEAD")
        with self.assertRaisesRegex(release.ReleaseError, "outside the repository"):
            release.require_scratch(self.root, self.root / "releases")

    def test_real_gitmodules_url_variants_and_numeric_names_are_supported(self):
        config = self.root / ".gitmodules"
        content = config.read_text().replace("https://github.com/basmilius/raxos-cache.git", "https://github.com/basmilius/raxos-cache")
        content = content.replace("https://github.com/basmilius/raxos-router.git", "git@github.com:basmilius/raxos-router.git")
        content += '[submodule "oauth2"]\n    path = oauth2\n    url = https://github.com/basmilius/raxos-oauth2\n'
        config.write_text(content)
        self.git("add", ".gitmodules")
        self.git("update-index", "--add", "--cacheinfo", f"160000,{'c' * 40},oauth2")
        self.git("-c", "commit.gpgsign=false", "commit", "-m", "URL variants")
        self.assertEqual({"cache", "router", "oauth2"}, release.libraries(self.root, "HEAD").keys())

    def test_malformed_release_data_fails_before_any_write(self):
        candidates = [[], dict(self.manifest, libraries=[]), dict(self.manifest, notes=None)]
        for changes in ({"base_commit": None}, {"notes": ["invalid"]}, {"base": 3}):
            plan = copy.deepcopy(self.manifest)
            plan["libraries"]["cache"].update(changes)
            candidates.append(plan)
        for plan in candidates:
            with self.subTest(plan=plan):
                self.bundle.write_text(json.dumps(plan))
                with self.assertRaises(release.ReleaseError):
                    self.preflight()
                self.assertEqual([], self.api.writes)

    def test_oversized_or_duplicate_json_data_is_rejected(self):
        self.bundle.write_text('{"schema": 1, "schema": 2}')
        with self.assertRaisesRegex(release.ReleaseError, "Duplicate key"):
            release.load_plan(self.root, self.bundle, "3.2.0", "HEAD")
        self.bundle.write_text(" " * 2_000_001)
        with self.assertRaisesRegex(release.ReleaseError, "size limit"):
            release.load_plan(self.root, self.bundle, "3.2.0", "HEAD")

    def test_prepare_uses_pinned_commits_even_when_library_heads_have_advanced(self):
        pins = {}
        for path in self.entries:
            local = self.root / path
            local.mkdir()
            release.git(local, "init", "-b", "main")
            release.git(local, "config", "user.name", "Release test")
            release.git(local, "config", "user.email", "release-test@example.invalid")
            for name in ("Previous release", "Pinned change", "Later unpublished change"):
                (local / "source.txt").write_text(name)
                release.git(local, "add", "source.txt")
                release.git(local, "-c", "commit.gpgsign=false", "commit", "-m", name)
                if name == "Previous release":
                    release.git(local, "tag", "3.1.0")
                elif name == "Pinned change":
                    pins[path] = release.git(local, "rev-parse", "HEAD")
            self.git("update-index", "--cacheinfo", f"160000,{pins[path]},{path}")
        self.git("-c", "commit.gpgsign=false", "commit", "-m", "Pin library commits")
        drafts = self.root.parent / "prepared"
        release.prepare(self.root, drafts, "3.2.0", "HEAD", self.api, "3.1.0")
        manifest = json.loads((drafts / "manifest.json").read_text())
        self.assertEqual(release.git(self.root, "rev-parse", "HEAD"), manifest["root_commit"])
        for path, pin in pins.items():
            self.assertEqual(pin, manifest["libraries"][path]["commit"])
            text = (drafts / f"{path}.md").read_text()
            self.assertIn("Pinned change", text)
            self.assertNotIn("Later unpublished change", text)
        with self.assertRaisesRegex(release.ReleaseError, "draft text"):
            release.build_bundle(self.root, drafts, "3.2.0", "HEAD")


class ReleaseUnits(unittest.TestCase):
    def test_semver_accepts_stable_and_prereleases(self):
        for tag in ("3.2.0", "v3.2.0", "0.0.0", "3.2.0+build.4"):
            self.assertFalse(release.version(tag))
        for tag in ("3.2.0-beta.1", "v3.2.0-rc.2+build"):
            self.assertTrue(release.version(tag))

    def test_invalid_versions_and_path_traversal_are_rejected(self):
        for tag in ("../3.2.0", "main", "3.2", "03.2.0", "3.2.0-beta.01", "3.2.0\n", "3.2.0;echo bad", "3.2.0-", "3.2.0/other"):
            with self.subTest(tag=tag), self.assertRaises(release.ReleaseError):
                release.version(tag)

    def test_notes_require_final_house_style(self):
        for notes in ("", "## Fixes\n\n- Fix behavior.", "## 🐛 Fixes\n\nDRAFT", "## 🐛 Fixes\n\n- TODO", "## 🐛 Fixes\n\n- Faster — safer."):
            with self.subTest(notes=notes), self.assertRaises(release.ReleaseError):
                release.check_notes(notes, "cache")

    def test_base_uses_latest_published_release_with_stability_rules(self):
        records = [
            {"tag_name": "3.1.0", "draft": False, "prerelease": False, "published_at": "2026-01-01"},
            {"tag_name": "3.2.0-beta.1", "draft": False, "prerelease": True, "published_at": "2026-02-01"},
            {"tag_name": "3.2.0", "draft": False, "prerelease": False, "published_at": "2026-03-01"},
            {"tag_name": "3.3.0", "draft": True, "prerelease": False, "published_at": "2026-04-01"},
        ]
        self.assertEqual("3.1.0", release.previous_release(records, "3.2.0", False)["tag_name"])
        self.assertEqual("3.2.0-beta.1", release.previous_release(records, "3.2.0", True)["tag_name"])
        self.assertIsNone(release.previous_release([], "3.2.0", False))

    def test_github_follows_annotated_tags_and_rejects_non_commits(self):
        api = release.GitHub("test-token")
        with patch.object(api, "request", side_effect=[{"object": {"type": "tag", "sha": "1" * 40}}, {"object": {"type": "commit", "sha": "a" * 40}}]):
            self.assertEqual("a" * 40, api.tag_commit("basmilius/raxos-cache", "3.1.0"))
        with patch.object(api, "request", return_value={"object": {"type": "tree", "sha": "1" * 40}}):
            with self.assertRaisesRegex(release.ReleaseError, "resolve to a commit"):
                api.tag_commit("basmilius/raxos-cache", "3.1.0")

    def test_release_listing_is_paginated(self):
        api = release.GitHub("test-token")
        with patch.object(api, "request", side_effect=[[{"id": n} for n in range(100)], [{"id": 100}]]) as request:
            self.assertEqual(101, len(api.releases("basmilius/raxos-cache")))
            self.assertIn("page=2", request.call_args.args[1])

    def test_optional_404_is_distinguished_from_permission_errors(self):
        api = release.GitHub("test-token")
        with patch.object(release, "urlopen", side_effect=HTTPError("test", 404, "Not found", {}, None)):
            self.assertIsNone(api.request("GET", "repos/test/release", optional=True))
            with self.assertRaisesRegex(release.ReleaseError, "HTTP 404"):
                api.request("GET", "repos/test/release")
        with patch.object(release, "urlopen", side_effect=HTTPError("test", 403, "Forbidden", {}, None)), patch.object(release.time, "sleep"):
            with self.assertRaisesRegex(release.ReleaseError, "HTTP 403"):
                api.request("GET", "repos/test/release", optional=True)

    def test_transient_http_error_is_retried_and_token_is_only_in_header(self):
        api = release.GitHub("test-token")
        response = BytesIO(b'{"id": 1}')
        with patch.object(release, "urlopen", side_effect=[HTTPError("test", 429, "Limit", {"Retry-After": "1"}, None), response]) as request, patch.object(release.time, "sleep") as sleep:
            self.assertEqual({"id": 1}, api.request("POST", "repos/test/releases", {"tag_name": "3.2.0"}))
            sleep.assert_called_once_with(1)
            http_request = request.call_args.args[0]
            self.assertEqual("Bearer test-token", http_request.headers["Authorization"])
            self.assertNotIn("test-token", http_request.full_url)


if __name__ == "__main__":
    unittest.main()
