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

## Hotfixes

1. Create `hotfix/<short-name>` from `main`.
2. Open a pull request to get the production fix into `main`.
3. Merge the same fix back into `develop` so both branches stay aligned.
