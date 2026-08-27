# Tasks: Add Backoffice Auth

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | 320-440 |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 route protection + redirects → PR 2 session login/logout + login UI → PR 3 suite adaptation + final verify |
| Delivery strategy | ask-on-risk |
| Chain strategy | stacked-to-main |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: stacked-to-main
400-line budget risk: High

### Suggested Work Units

| Unit | Goal | Likely PR | Focused test command | Runtime harness | Rollback boundary |
|------|------|-----------|----------------------|-----------------|-------------------|
| 1 | Protect routes and guest/auth redirects | PR 1 | `docker compose run --rm app php artisan test --compact --filter=BackofficeAuthenticationTest` | `docker compose up -d app nginx mysql && curl -I http://localhost:8000/login && curl -I http://localhost:8000/customers` | `routes/web.php`, `bootstrap/app.php`, guest redirect behavior |
| 2 | Add session login/logout flow and login page | PR 2 | `docker compose run --rm app php artisan test --compact --filter=BackofficeAuthenticationTest` | Browser smoke: log in at `/login`, verify redirect to `/customers`, then logout | `app/Http/Controllers/Auth`, `app/Http/Requests/Auth`, `resources/js/Pages/Auth/Login.vue`, shared auth props |
| 3 | Re-baseline protected feature suites and final verify | PR 3 | `docker compose run --rm app php artisan test --compact --filter='CustomerManagementTest|AssetManagementTest'` | `docker compose run --rm app php artisan test --compact` | `tests/Feature/CustomerManagementTest.php`, `tests/Feature/AssetManagementTest.php` |

## Phase 1: Route Protection Foundation

- [x] 1.1 RED — Create `tests/Feature/Auth/BackofficeAuthenticationTest.php` with failing scenarios for guest `/login`, authenticated revisit redirect, and guest redirects from `/customers`, `/assets`, and `/assets/{asset}/assign`.
- [x] 1.2 GREEN — Update `routes/web.php` and `bootstrap/app.php` to add named `login`/`logout` routes, keep `/` public, wrap customer/asset routes in `auth`, and redirect guests/authenticated users to `customers.index`.
- [x] 1.3 REFACTOR — Trim duplicated route assertions in `tests/Feature/Auth/BackofficeAuthenticationTest.php` and keep redirect targets named, not hard-coded.

## Phase 2: Session Auth Flow

- [x] 2.1 RED — Extend `tests/Feature/Auth/BackofficeAuthenticationTest.php` with failing cases for intended redirect after login, invalid credentials, logout, and post-logout access denial.
- [x] 2.2 GREEN — Create `app/Http/Requests/Auth/LoginRequest.php` and `app/Http/Controllers/Auth/AuthenticatedSessionController.php` to validate credentials, authenticate with the session guard, regenerate/invalidate sessions, and return validation-style auth errors.
- [x] 2.3 REFACTOR — Keep controller methods thin by centralizing the auth attempt contract in `LoginRequest` and aligning flash/error responses with current Laravel conventions.

## Phase 3: Inertia UI Wiring

- [x] 3.1 RED — Add failing Inertia assertions in `tests/Feature/Auth/BackofficeAuthenticationTest.php` for `Auth/Login` rendering and shared `auth.user` props on authenticated backoffice responses.
- [x] 3.2 GREEN — Update `app/Http/Middleware/HandleInertiaRequests.php` and create `resources/js/Pages/Auth/Login.vue` with `useForm`, auth flash rendering, and existing backoffice styling patterns.
- [x] 3.3 REFACTOR — Reuse form/layout conventions from `resources/js/Pages/Customers/Form.vue` and remove any login-page-only styling duplication.

## Phase 4: Protected Suite Adaptation and Verification

- [x] 4.1 RED — Update `tests/Feature/CustomerManagementTest.php` and `tests/Feature/AssetManagementTest.php` so representative requests fail first without authenticated setup behind the new middleware boundary.
- [x] 4.2 GREEN — Add `actingAs(User::factory()->create())` setup in `tests/Feature/CustomerManagementTest.php` and `tests/Feature/AssetManagementTest.php` so business-flow coverage runs inside the protected slice without duplicating auth assertions.
- [x] 4.3 VERIFY — Run `docker compose run --rm app php artisan test --compact --filter=BackofficeAuthenticationTest`, `docker compose run --rm app php artisan test --compact --filter='CustomerManagementTest|AssetManagementTest'`, `docker compose run --rm app php artisan test --compact`, and `docker compose run --rm app vendor/bin/pint --test`.
