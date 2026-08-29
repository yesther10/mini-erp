# Contributing

Use this lightweight branch workflow to keep changes predictable.

## Happy path

1. Start your work from `develop`.
2. Create a branch named `feature/<short-name>`.
3. Open a pull request into `develop` when the feature is ready to integrate.

## Branch model

| Branch | Purpose |
| --- | --- |
| `develop` | Integration branch for ongoing work |
| `main` | Stable branch for releases |
| `feature/*` | Feature work that will be merged into `develop` |
| `hotfix/*` | Urgent production fixes that start from `main` |

## Releases

- Promote `develop` to `main` only through a dedicated release pull request.
- Use that release pull request when the current state of `develop` is ready to become the stable version.

## Issue tracking

- Pull requests merged into `develop` do not automatically close linked issues because `develop` is not the default branch.
- Close those issues manually after the PR is merged into `develop` and the work is confirmed.
- Pull requests merged into `main` can auto-close linked issues when they use the standard closing keywords.

## Hotfixes

1. Create `hotfix/<short-name>` from `main`.
2. Open a pull request to get the production fix into `main`.
3. Merge the same fix back into `develop` so both branches stay aligned.
