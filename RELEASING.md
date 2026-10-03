# Releasing Raxos

Publish one GitHub release in `basmilius/raxos`. Its `Release libraries` workflow creates the same version tag and a GitHub release in each library repository, using the submodule commits pinned by the root release. Each library gets its own reviewed English release notes.

Release notes live in GitHub release bodies. The release agent prepares drafts in a temporary directory outside the repositories and attaches one `raxos-libraries.json` file to the root release. Changelogs and release manifests are never committed to the repositories.

## GitHub setup

Create a repository Actions secret named `RAXOS_RELEASE_TOKEN` in `basmilius/raxos`. Use a fine-grained personal access token with access to all 21 `basmilius/raxos-*` library repositories and **Contents: write**. Include **Workflows: write** if the selected commits contain workflow changes relative to a library's current default branch. GitHub documents that additional permission for [release creation](https://docs.github.com/en/rest/releases/releases#create-a-release).

The root workflow's own `GITHUB_TOKEN` reads the root release, downloads its asset and checks Actions results. The cross-repository token creates library tags and releases. Store it only as the Actions secret.

## Instructions for the release agent

Follow the `release` and `release-notes` skills. Resolve the version, refresh root and library refs and tags, run the local checks, and obtain the user's release approval before creating the root release. Push library and root code changes before starting a release; the [push order](TESTING.md#push-order) keeps dependency CI consistent.

The selected root commit must be on `origin/main`, and its latest push-triggered `Tests` workflow must have succeeded. That workflow tests the exact library commits that will receive the version tags. Both published stable releases and published prereleases trigger orchestration.

1. Capture the exact root commit with `git rev-parse origin/main`. Use that SHA as `--ref` for preparation and validation, and as `--target` when creating the release.
2. Create a temporary parent directory with `mktemp -d`. Prepare drafts in a new child directory:

   ```sh
   python3 tools/release.py prepare --tag 3.2.0 --ref <root-sha> --base 3.1.0 --directory <scratch>/3.2.0
   ```

   The explicit `--base 3.1.0` is for the first orchestrated release: these repositories have existing version tags but no published GitHub releases. For subsequent releases, omit `--base`; the tool selects each library's previous published stable release, or its previous published release of either kind for prereleases. Without a published release or explicit base, it uses the library's first commit.

3. Rewrite `raxos.md` and each library's Markdown draft in that scratch directory using the release-notes skill. Inspect the actual diff from the recorded base to the pinned library commit. Fold follow-up commits, document migrations, and omit unrelated library changes. A library without runtime changes still receives the synchronized version and a short maintenance note.
4. Add the root comparison link and any prerelease or breaking-change notice to `raxos.md`. Library comparison links, notices and the link back to the root release are added by the workflow.
5. Build the release asset and validate every note and pinned commit:

   ```sh
   python3 tools/release.py bundle --tag 3.2.0 --ref <root-sha> --directory <scratch>/3.2.0
   python3 tools/release.py validate --tag 3.2.0 --ref <root-sha> --bundle <scratch>/3.2.0/raxos-libraries.json
   ```

6. Present the root and library notes for review, including the version, target SHA and comparison bases. The root release publishes all libraries, so that approval covers the prepared library releases too.
7. After approval, create the root release with its asset in the same command:

   ```sh
   gh release create 3.2.0 <scratch>/3.2.0/raxos-libraries.json \
     --repo basmilius/raxos --target <root-sha> --title 'Release 3.2.0' \
     --notes-file <scratch>/3.2.0/raxos.md --latest
   ```

   Use `--prerelease` instead of `--latest` for prereleases. Keep the root body identical to `raxos.md` after bundling. GitHub CLI [uploads supplied assets before publishing the release](https://cli.github.com/manual/gh_release_create), so the notes are available when the workflow starts. Do not publish first and upload the asset afterward.

8. Watch `Release libraries` in the root repository's Actions tab. Its summary lists every library's commit and publication result; `raxos-release-plan` preserves the rendered notes and progress.

## Checks and recovery

Before its first write, the workflow checks every repository, the root CI result, commit ancestry, comparison bases, prepared notes and existing version tags. Existing tags pointing elsewhere cause a failure and are never moved. Existing releases must match the prepared commit, notes, title and prerelease flag.

GitHub does not offer an atomic release across repositories. If an API call fails after some libraries have published, rerun the failed workflow. Matching releases are skipped; a matching tag without a release is completed. Fix missing permissions or a temporary API failure before retrying. Keep the reviewed asset unchanged when resuming a partial release.

`workflow_dispatch` accepts a published root release tag. Its `dry_run` option defaults to true and checks the same conditions without creating tags or releases. Disable it to resume publication manually. A release without the required asset fails before any library is changed.

## Local checks

```sh
python3 -m unittest discover -s tests/release -v
composer test:lint
composer test
```

Configure the disposable MySQL, MariaDB and Redis services described in [TESTING.md](TESTING.md) for the full library run. The release-tool tests use temporary Git repositories and a fake GitHub API; they publish nothing.
