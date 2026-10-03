import argparse
import configparser
import hashlib
import json
import os
from pathlib import Path
import re
import subprocess
import sys
import time
from urllib.error import HTTPError
from urllib.parse import quote
from urllib.request import Request, urlopen


ROOT_REPOSITORY = "basmilius/raxos"
SEMVER = re.compile(r"v?(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?")
SHA = re.compile(r"[0-9a-f]{40}")
HEADINGS = ("⚠️ Breaking changes", "✨ New modules", "🚀 Features", "♿ Accessibility", "🐛 Fixes", "⚡ Performance", "🎨 Styles", "🧹 Chores")
ASSET_NAME = "raxos-libraries.json"


class ReleaseError(RuntimeError):
    pass


def version(tag):
    match = SEMVER.fullmatch(tag) if isinstance(tag, str) else None
    if not match or (match[4] and any(part.isdecimal() and len(part) > 1 and part[0] == "0" for part in match[4].split("."))):
        raise ReleaseError(f"Invalid semantic version: {tag!r}")
    return bool(match[4])


def git(root, *args):
    result = subprocess.run(["git", "-C", str(root), *args], capture_output=True, text=True)
    if result.returncode:
        raise ReleaseError(f"Git failed: {' '.join(args)}\n{result.stderr.strip()}")
    return result.stdout.strip()


def libraries(root, ref):
    config = configparser.ConfigParser(interpolation=None)
    config.read_string(git(root, "show", f"{ref}:.gitmodules"))
    modules = {}
    for section in config.sections():
        path = config[section]["path"]
        repository = f"basmilius/raxos-{path}"
        allowed_urls = tuple(prefix + repository + suffix for prefix in ("https://github.com/", "git@github.com:") for suffix in ("", ".git"))
        if not re.fullmatch(r"[a-z][a-z0-9]*(?:-[a-z0-9]+)*", path) or config[section]["url"] not in allowed_urls or path in modules:
            raise ReleaseError(f"Unexpected submodule configuration: {section}")
        modules[path] = repository
    pins = {}
    for line in git(root, "ls-tree", ref).splitlines():
        metadata, path = line.split("\t", 1)
        mode, kind, commit = metadata.split()
        if mode == "160000" and kind == "commit":
            pins[path] = commit
    if not pins or pins.keys() != modules.keys():
        raise ReleaseError("Submodule declarations do not match the release's gitlinks.")
    return {path: {"repository": modules[path], "commit": pins[path]} for path in sorted(pins)}


def check_notes(notes, label):
    if not isinstance(notes, str):
        raise ReleaseError(f"{label}: release notes must be text.")
    headings = re.findall(r"^## (.+)$", notes, re.MULTILINE)
    if not notes.strip() or not headings or any(heading not in HEADINGS for heading in headings):
        raise ReleaseError(f"{label}: use the release skill's English emoji sections.")
    if "TODO" in notes or "DRAFT" in notes or "—" in notes or "–" in notes:
        raise ReleaseError(f"{label}: release notes still contain draft text or unsupported dashes.")


def validate_plan(root, plan, tag, ref):
    prerelease = version(tag)
    if not isinstance(plan, dict):
        raise ReleaseError("Release data must be a JSON object.")
    if plan.get("schema") != 1 or plan.get("tag") != tag or plan.get("prerelease") is not prerelease:
        raise ReleaseError("Release data version or prerelease flag is inconsistent.")
    if plan.get("root_commit") != git(root, "rev-parse", f"{ref}^{{commit}}"):
        raise ReleaseError("Release data does not match the selected root commit.")
    expected = libraries(root, ref)
    entries = plan.get("libraries", {})
    if not isinstance(entries, dict) or entries.keys() != expected.keys():
        raise ReleaseError("The release must include every submodule exactly once.")
    check_notes(plan.get("notes", ""), "raxos")
    for path, entry in entries.items():
        if not isinstance(entry, dict):
            raise ReleaseError(f"{path}: release data must be a JSON object.")
        if any(entry.get(key) != value for key, value in expected[path].items()):
            raise ReleaseError(f"{path}: release data does not match the pinned commit.")
        if not SHA.fullmatch(entry.get("commit", "")):
            raise ReleaseError(f"{path}: invalid commit.")
        if entry.get("base") is not None:
            version(entry["base"])
        if not isinstance(entry.get("base_commit"), str) or not SHA.fullmatch(entry["base_commit"]):
            raise ReleaseError(f"{path}: invalid changelog base commit.")
        check_notes(entry.get("notes", ""), path)
    return plan


def load_plan(root, bundle, tag, ref):
    if bundle.stat().st_size > 2_000_000:
        raise ReleaseError("Library release data exceeds the 2 MB size limit.")
    return validate_plan(root, json.loads(bundle.read_text(), object_pairs_hook=unique_keys), tag, ref)


def unique_keys(pairs):
    result = {}
    for key, value in pairs:
        if key in result:
            raise ReleaseError(f"Duplicate key in release data: {key!r}")
        result[key] = value
    return result


class GitHub:
    def __init__(self, token):
        if not token:
            raise ReleaseError("Set RAXOS_RELEASE_TOKEN as a repository secret (GH_TOKEN locally).")
        self.token = token

    def request(self, method, path, payload=None, optional=False):
        data = json.dumps(payload).encode() if payload is not None else None
        request = Request(f"https://api.github.com/{path}", data=data, method=method, headers={
            "Accept": "application/vnd.github+json",
            "Authorization": f"Bearer {self.token}",
            "Content-Type": "application/json",
            "User-Agent": "raxos-release",
            "X-GitHub-Api-Version": "2026-03-10",
        })
        for attempt in range(3):
            try:
                with urlopen(request, timeout=30) as response:
                    return json.load(response)
            except HTTPError as error:
                error.close()
                if optional and error.code == 404:
                    return None
                delay = error.headers.get("Retry-After")
                if error.code in (403, 429, 500, 502, 503, 504) and attempt < 2:
                    time.sleep(min(60, int(delay) if delay and delay.isdecimal() else 5 * (attempt + 1)))
                    continue
                raise ReleaseError(f"GitHub {method} {path}: HTTP {error.code}") from None

    def releases(self, repository):
        releases = []
        for page in range(1, 1001):
            batch = self.request("GET", f"repos/{repository}/releases?per_page=100&page={page}")
            releases.extend(batch)
            if len(batch) < 100:
                return releases
        raise ReleaseError(f"{repository}: release pagination limit exceeded.")

    def tag_commit(self, repository, tag):
        ref = self.request("GET", f"repos/{repository}/git/ref/tags/{quote(tag, safe='')}", optional=True)
        if ref is None:
            return None
        obj = ref["object"]
        for _ in range(10):
            if obj["type"] == "commit":
                return obj["sha"]
            if obj["type"] != "tag":
                break
            obj = self.request("GET", f"repos/{repository}/git/tags/{obj['sha']}")["object"]
        raise ReleaseError(f"{repository}: tag does not resolve to a commit.")


def previous_release(releases, tag, prerelease):
    eligible = [release for release in releases if release["tag_name"] != tag and not release["draft"] and (prerelease or not release["prerelease"])]
    return max(eligible, key=lambda release: release["published_at"], default=None)


def render_notes(tag, entry):
    notes = entry["notes"].strip()
    if version(tag):
        notes = "🧪 This is a pre-release.\n\n" + notes
    if "## ⚠️ Breaking changes" in notes:
        notes = "⚠️ This release contains breaking changes.\n\n" + notes
    base = entry["base"] or entry["base_commit"]
    return f"{notes}\n\n**Full Changelog**: https://github.com/{entry['repository']}/compare/{base}...{tag}\n\nPart of [Raxos {tag}](https://github.com/{ROOT_REPOSITORY}/releases/tag/{tag}).\n"


def require_ancestor(api, repository, base, head):
    comparison = api.request("GET", f"repos/{repository}/compare/{quote(base, safe='')}...{quote(head, safe='')}")
    if comparison["status"] not in ("ahead", "identical"):
        raise ReleaseError(f"{repository}: {base} is not an ancestor of {head}.")


def verify_existing(api, repository, tag, commit, payload):
    actual_commit = api.tag_commit(repository, tag)
    if actual_commit is not None and actual_commit != commit:
        raise ReleaseError(f"{repository}: existing {tag} points at a different commit; it will not be moved.")
    release = api.request("GET", f"repos/{repository}/releases/tags/{quote(tag, safe='')}", optional=True)
    if release:
        if actual_commit != commit or release["draft"] or any(release.get(key) != payload[key] for key in ("name", "prerelease")) or (release.get("body") or "").strip() != payload["body"].strip():
            raise ReleaseError(f"{repository}: existing release differs from the prepared release.")
    return actual_commit, release


def preflight(root, bundle, tag, api, root_api):
    release = root_api.request("GET", f"repos/{ROOT_REPOSITORY}/releases/tags/{quote(tag, safe='')}")
    if release["draft"] or release["prerelease"] != version(tag):
        raise ReleaseError("The root release must be published with the matching prerelease flag.")
    commit = git(root, "rev-parse", "HEAD")
    if root_api.tag_commit(ROOT_REPOSITORY, tag) != commit:
        raise ReleaseError("Checkout does not match the published root release's tag.")
    require_ancestor(root_api, ROOT_REPOSITORY, commit, "main")
    runs = root_api.request("GET", f"repos/{ROOT_REPOSITORY}/actions/workflows/tests.yml/runs?head_sha={commit}&branch=main&event=push&per_page=100")["workflow_runs"]
    runs = [run for run in runs if run["head_sha"] == commit and run["head_branch"] == "main" and run["event"] == "push"]
    latest = max(runs, key=lambda run: run["id"], default=None)
    if latest is None or latest["status"] != "completed" or latest["conclusion"] != "success":
        raise ReleaseError("The latest main Tests workflow for this exact root commit must have succeeded.")
    assets = [asset for asset in release.get("assets", []) if asset["name"] == ASSET_NAME and asset["state"] == "uploaded"]
    if len(assets) != 1:
        raise ReleaseError(f"Attach exactly one uploaded {ASSET_NAME} asset before publishing the root release.")
    digest = assets[0].get("digest")
    if digest and digest != "sha256:" + hashlib.sha256(bundle.read_bytes()).hexdigest():
        raise ReleaseError("Downloaded release data does not match the root release asset's digest.")
    plan = load_plan(root, bundle, tag, commit)
    if (release.get("body") or "").strip() != plan["notes"].strip():
        raise ReleaseError("Root release notes differ from the reviewed release data.")
    prepared = []
    for path, entry in plan["libraries"].items():
        repository = entry["repository"]
        require_ancestor(api, repository, entry["commit"], "main")
        require_ancestor(api, repository, entry["base_commit"], entry["commit"])
        if entry["base"] and api.tag_commit(repository, entry["base"]) != entry["base_commit"]:
            raise ReleaseError(f"{repository}: changelog base tag changed.")
        previous = previous_release(api.releases(repository), tag, plan["prerelease"])
        if previous and previous["tag_name"] != entry["base"]:
            raise ReleaseError(f"{repository}: prepare notes against the latest published release, {previous['tag_name']}.")
        payload = {
            "tag_name": tag, "target_commitish": entry["commit"], "name": f"Release {tag}",
            "body": render_notes(tag, entry), "draft": False,
            "prerelease": plan["prerelease"], "make_latest": "false" if plan["prerelease"] else "true",
        }
        _, existing = verify_existing(api, repository, tag, entry["commit"], payload)
        prepared.append({"library": path, "repository": repository, "commit": entry["commit"], "payload": payload, "status": "existing" if existing else "pending"})
    return prepared


def publish(prepared, api, dry_run, progress=None):
    for item in prepared:
        repository, payload, commit = item["repository"], item["payload"], item["commit"]
        tag_commit, existing = verify_existing(api, repository, payload["tag_name"], commit, payload)
        if existing:
            item["status"] = "existing"
        elif dry_run:
            item["status"] = "dry-run"
        else:
            if tag_commit is None:
                try:
                    api.request("POST", f"repos/{repository}/git/refs", {"ref": f"refs/tags/{payload['tag_name']}", "sha": commit})
                except ReleaseError:
                    # A retry can find a tag created before an interrupted API response.
                    if api.tag_commit(repository, payload["tag_name"]) != commit:
                        raise
            if api.tag_commit(repository, payload["tag_name"]) != commit:
                raise ReleaseError(f"{repository}: tag changed before release publication.")
            api.request("POST", f"repos/{repository}/releases", payload)
            item["status"] = "published"
            time.sleep(2)
        if progress:
            progress(prepared)
        print(f"{repository}: {item['status']}", flush=True)


def record(prepared, output):
    if output:
        output.parent.mkdir(parents=True, exist_ok=True)
        output.write_text(json.dumps(prepared, indent=2, ensure_ascii=False) + "\n")
    summary = os.environ.get("GITHUB_STEP_SUMMARY")
    if summary:
        table = "| Library | Commit | Result |\n| --- | --- | --- |\n"
        table += "".join(f"| {item['library']} | `{item['commit']}` | {item['status']} |\n" for item in prepared)
        Path(summary).write_text(table)


def prepare(root, directory, tag, ref, api, base_override):
    prerelease = version(tag)
    require_scratch(root, directory)
    if directory.exists():
        raise ReleaseError(f"{directory} already exists; edit its notes rather than overwriting them.")
    entries, drafts = {}, {}
    for path, pin in libraries(root, ref).items():
        previous = previous_release(api.releases(pin["repository"]), tag, prerelease)
        base = base_override or (previous["tag_name"] if previous else None)
        local = root / path
        base_commit = git(local, "rev-parse", f"refs/tags/{base}^{{commit}}") if base else git(local, "rev-list", "--max-parents=0", pin["commit"]).splitlines()[0]
        git(local, "merge-base", "--is-ancestor", base_commit, pin["commit"])
        entries[path] = {**pin, "base": base, "base_commit": base_commit}
        changes = git(local, "log", "--format=%h %s", f"{base_commit}..{pin['commit']}")
        diff = git(local, "diff", "--stat", base_commit, pin["commit"])
        drafts[path] = "DRAFT: review the actual diff and replace this evidence with English release notes.\n\n## 🧹 Chores\n\n```text\n" + changes + "\n\n" + diff + "\n```\n"
    directory.mkdir(parents=True)
    (directory / "manifest.json").write_text(json.dumps({"schema": 1, "tag": tag, "prerelease": prerelease, "root_commit": git(root, "rev-parse", f"{ref}^{{commit}}"), "libraries": entries}, indent=2) + "\n")
    (directory / "raxos.md").write_text("DRAFT: write the root release notes after reviewing all library changes.\n\n## 🚀 Features\n")
    for path, draft in drafts.items():
        (directory / f"{path}.md").write_text(draft)
    print(f"Prepared {len(entries)} library changelog drafts in {directory}.")


def require_scratch(root, directory):
    if directory.resolve().is_relative_to(root.resolve()):
        raise ReleaseError("Release drafts must be stored outside the repository.")


def build_bundle(root, directory, tag, ref):
    require_scratch(root, directory)
    plan = json.loads((directory / "manifest.json").read_text(), object_pairs_hook=unique_keys)
    plan["notes"] = (directory / "raxos.md").read_text()
    for path, entry in plan["libraries"].items():
        if not re.fullmatch(r"[a-z][a-z0-9]*(?:-[a-z0-9]+)*", path):
            raise ReleaseError(f"Unexpected library name: {path!r}")
        entry["notes"] = (directory / f"{path}.md").read_text()
    validate_plan(root, plan, tag, ref)
    output = directory / ASSET_NAME
    output.write_text(json.dumps(plan, indent=2, ensure_ascii=False) + "\n")
    print(f"Validated library release data: {output}")
    return output


def main():
    parser = argparse.ArgumentParser(description="Prepare and publish synchronized Raxos library releases.")
    parser.add_argument("command", choices=("prepare", "bundle", "validate", "publish"))
    parser.add_argument("--tag", required=True)
    parser.add_argument("--base")
    parser.add_argument("--ref", default="origin/main")
    parser.add_argument("--directory", type=Path)
    parser.add_argument("--bundle", type=Path)
    parser.add_argument("--output", type=Path)
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()
    root = Path(__file__).resolve().parent.parent
    version(args.tag)
    if args.command == "validate":
        if not args.bundle:
            parser.error("--bundle is required for validate")
        load_plan(root, args.bundle, args.tag, args.ref)
        print("Validated release data and all library notes.")
        return
    if args.command in ("prepare", "bundle"):
        if not args.directory:
            parser.error("--directory is required for prepare and bundle")
        require_scratch(root, args.directory)
        if args.command == "bundle":
            build_bundle(root, args.directory, args.tag, args.ref)
            return
    elif not args.bundle:
        parser.error("--bundle is required for publish")
    token = os.environ.get("GH_TOKEN")
    if not token and os.environ.get("GITHUB_ACTIONS") == "true":
        raise ReleaseError("Configure the RAXOS_RELEASE_TOKEN repository secret before releasing.")
    if not token:
        result = subprocess.run(["gh", "auth", "token"], capture_output=True, text=True)
        token = result.stdout.strip() if result.returncode == 0 else None
    api = GitHub(token)
    if args.command == "prepare":
        if args.base:
            version(args.base)
        prepare(root, args.directory, args.tag, args.ref, api, args.base)
        return
    root_api = GitHub(os.environ.get("RAXOS_ROOT_TOKEN") or token)
    prepared = preflight(root, args.bundle, args.tag, api, root_api)
    record(prepared, args.output)
    try:
        publish(prepared, api, args.dry_run or os.environ.get("RAXOS_RELEASE_DRY_RUN") == "true", lambda items: record(items, args.output))
    finally:
        record(prepared, args.output)


if __name__ == "__main__":
    try:
        main()
    except (ReleaseError, OSError, ValueError, KeyError) as error:
        print(f"Release failed: {error}", file=sys.stderr)
        sys.exit(1)
