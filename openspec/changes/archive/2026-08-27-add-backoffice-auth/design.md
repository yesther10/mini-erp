# Design: Add Backoffice Auth

## Technical Approach

Implement a manual Laravel session-auth slice that follows the current thin-controller, Form Request, and Inertia middleware patterns. Add guest-only login routes, authenticated logout, an `auth` wrapper around all `/customers` and `/assets` routes, an explicit authenticated fallback, and shared Inertia auth props for the backoffice pages.

## Architecture Decisions

| Decision | Alternatives considered | Choice | Rationale |
|---|---|---|---|
| Login flow shape | Starter kit, route closures, custom controller | `AuthenticatedSessionController` with `LoginRequest` | Matches the existing controller plus Form Request convention and keeps auth logic out of routes. |
| Authenticated fallback | `dashboard`, `customers.index`, new backoffice home | `customers.index` | It already exists, is inside the protected slice, and satisfies the spec without inventing another page. |
| Shared auth state | Page-specific props, global middleware props | `HandleInertiaRequests::share()` provides `auth.user` and auth flash data | Keeps auth context consistent across customer and asset pages while preserving the existing shared-prop pattern. |

## Data Flow

Guest → `GET /login` (`guest`) → `Auth/Login` Inertia page  
Guest → `POST /login` (`guest`, `LoginRequest`) → `Auth::attempt()` → session regenerate → `redirect()->intended(route('customers.index'))`  
Authenticated user → `/customers` or `/assets` (`auth`) → existing controllers → Inertia pages with shared `auth.user`  
Authenticated user → `POST /logout` (`auth`) → `Auth::logout()` + session invalidate/regenerate token → redirect to `login`

## File Changes

| File | Action | Description |
|---|---|---|
| `routes/web.php` | Modify | Add named login/logout routes and wrap all customer, asset, and assignment routes in `auth` while keeping `/` public. |
| `bootstrap/app.php` | Modify | Configure guest redirect to `login` and authenticated-user redirect away from `login` to `customers.index`. |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Create | Render the login page, submit credentials, and terminate the session. |
| `app/Http/Requests/Auth/LoginRequest.php` | Create | Validate credentials and centralize the authentication attempt contract. |
| `app/Http/Middleware/HandleInertiaRequests.php` | Modify | Share lazy `auth.user`, keep parent shared validation errors, and expose auth-related flash messages. |
| `resources/js/Pages/Auth/Login.vue` | Create | Provide the guest login form with Inertia `useForm` and existing page styling conventions. |
| `tests/Feature/Auth/BackofficeAuthenticationTest.php` | Create | Cover login page access, intended redirect, invalid credentials, logout, and guest redirects from protected routes. |
| `tests/Feature/CustomerManagementTest.php` | Modify | Authenticate requests so current customer feature coverage still exercises protected behavior. |
| `tests/Feature/AssetManagementTest.php` | Modify | Authenticate requests so current asset and assignment feature coverage still exercises protected behavior. |

## Interfaces / Contracts

```php
'auth' => [
    'user' => fn () => $request->user()?->only('id', 'name', 'email'),
],
'flash' => [
    'success' => fn () => $request->session()->get('success'),
    'error' => fn () => $request->session()->get('error'),
],
```

`LoginRequest` validates `email` and `password`; failed authentication is returned as validation-style errors so the login page can render them through existing Inertia error props.

## Testing Strategy

| Layer | What to Test | Approach |
|---|---|---|
| Feature | Guest login entry and authenticated revisit | Assert guests can render `Auth/Login` and authenticated users are redirected to `customers.index`. |
| Feature | Login and logout session behavior | Assert valid credentials authenticate and honor `intended`; invalid credentials keep the guest logged out; logout clears access and redirects to `login`. |
| Feature | Protected route coverage | Assert guests are redirected from representative `/customers`, `/assets`, and `/assets/{asset}/assign` routes; assert authenticated users can access them without role checks. |
| Feature | Existing backoffice feature tests | Add `actingAs(User::factory()->create())` to customer and asset tests so auth becomes a precondition, not duplicated coverage. |
| Unit | None required | This slice is HTTP and framework-auth driven; feature tests own the contract. |
| E2E | N/A | No E2E runner is installed. |

## Threat Matrix

N/A — no shell, subprocess, VCS/PR automation, executable-file classification, or process-integration boundary is introduced by this auth slice.

## Migration / Rollout

No migration required.

## Open Questions

- [ ] None blocking.
