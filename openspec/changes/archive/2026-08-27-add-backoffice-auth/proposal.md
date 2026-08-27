# Proposal: Add Backoffice Auth

## Intent

Add the smallest coherent authentication slice for the interview MVP so backoffice customer and asset workflows stop being publicly accessible.

## Scope

### In Scope
- Add guest-only login screen and login submission for backoffice users.
- Add authenticated logout flow using Laravel session authentication.
- Protect all `/customers` and `/assets` routes, including assignment endpoints.
- Share authenticated user data and auth flash/errors with Inertia.

### Out of Scope
- Roles, permissions, registration, password reset, profile management.
- Starter-kit scaffolding such as Breeze, Jetstream, or Fortify.
- New business modules or redesign of the public landing page.

## Capabilities

### New Capabilities
- `backoffice-authentication`: Manual session-based login/logout and authenticated access to the backoffice.

### Modified Capabilities
- None.

## Approach

Use Laravel's existing `web` guard and sessions instead of installing auth scaffolding. Add login routes, a thin auth controller backed by a Form Request, `Auth::attempt()` with session regeneration, authenticated logout, and an `auth` middleware group around the current backoffice routes. Keep the implementation Docker-first for PHP and test execution.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `routes/web.php` | Modified | Add login/logout routes and protect customer/asset routes. |
| `bootstrap/app.php` | Modified | Optionally make guest redirect target explicit. |
| `app/Http/Controllers/` | New | Add thin auth controller for create/store/destroy auth actions. |
| `app/Http/Requests/` | New | Add login Form Request. |
| `app/Http/Middleware/HandleInertiaRequests.php` | Modified | Share auth user and auth-related flash/errors. |
| `resources/js/Pages/Auth/Login.vue` | New | Add the Inertia login page. |
| `tests/Feature/` | Modified | Add feature coverage for guest redirects, login, logout, and protected access. |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Some backoffice endpoints remain public | Medium | Apply `auth` to the full `/customers` and `/assets` route set, not only index pages. |
| Login redirect creates confusing entry UX | Low | Use `redirect()->intended()` and confirm dashboard/customer entry behavior in tests. |
| Host PHP mismatch breaks local verification | High | Run Artisan and feature tests through Docker only. |

## Rollback Plan

Revert the auth routes, middleware grouping, Inertia shared props, login page, and related tests; restore the previous public route map if the slice blocks the demo.

## Dependencies

- Existing Laravel session auth primitives and Docker PHP 8.4 runtime.

## Success Criteria

- [ ] Guests are redirected to login when accessing `/customers` or `/assets` routes.
- [ ] Valid credentials create a session and return the user to the intended backoffice page.
- [ ] Invalid credentials keep the user logged out and show a login error.
- [ ] Logout ends the session and protected routes become inaccessible again.
