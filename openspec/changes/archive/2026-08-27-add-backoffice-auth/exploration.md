## Exploration: add-backoffice-auth

### Current State
Laravel's session auth foundation already exists in the repo: `config/auth.php` uses the `web` session guard, `users`, `password_reset_tokens`, and `sessions` tables exist, `App\Models\User` is an `Authenticatable`, and `DatabaseSeeder` creates a default `test@example.com` user. However, the application has no login/logout routes, no auth controllers or Form Requests, no auth-related Inertia pages, no route protection on `/customers` or `/assets`, and no shared authenticated-user prop in `HandleInertiaRequests`. The current backoffice routes are fully public.

No starter auth scaffolding is installed beyond Laravel's base auth primitives. Direct dependencies include Laravel, Inertia, Vue, and Tailwind, but not Breeze, Jetstream, or Fortify.

### Affected Areas
- `routes/web.php` — add public guest login routes, authenticated logout route, and wrap the existing backoffice routes with `auth` middleware.
- `bootstrap/app.php` — optionally set `redirectGuestsTo('/login')` explicitly for predictable guest redirects.
- `app/Http/Middleware/HandleInertiaRequests.php` — share authenticated user identity and login errors/flash data with Inertia pages.
- `app/Http/Controllers/` — add auth controller(s) while preserving the thin-controller convention.
- `app/Http/Requests/` — add a login Form Request to keep validation out of controllers.
- `app/Actions/` — optionally add a small auth action if the team wants auth attempt logic extracted; not required for the smallest MVP.
- `resources/js/Pages/` — add a `Auth/Login.vue` page and likely adjust dashboard/backoffice navigation links.
- `tests/Feature/` — add feature coverage for guest redirects, successful login, failed login, logout, and authenticated access to protected routes.

### Approaches
1. **Manual session auth slice** — Build a small login/logout flow with Laravel's built-in session guard and custom Inertia page.
   - Pros: Smallest scope, matches interview MVP, avoids large scaffolding, preserves current UI and architecture, easy to test with feature tests.
   - Cons: We must wire the few auth pieces ourselves instead of getting generated pages/controllers.
   - Effort: Low

2. **Install a starter kit / Fortify-backed scaffold** — Add first-party auth scaffolding and trim unused features.
   - Pros: Official auth flow, good long-term base if registration/reset/profile are coming soon.
   - Cons: Overshoots current scope, adds many files and conventions, increases review size, and conflicts with the request for a minimal auth slice.
   - Effort: Medium

### Recommendation
Use **manual session auth slice**. The repo already has the Laravel auth primitives needed for session login, and the requested scope is deliberately narrow: login, logout, and route protection with no roles. Installing starter auth would introduce registration, password reset, profile, and extra frontend structure that the interview demo does not need.

The MVP design should be:
- Public `GET /login` and guest-only `POST /login`.
- Authenticated `POST /logout`.
- Existing `/customers` and `/assets` route trees wrapped in `auth` middleware, including asset assignment routes.
- Login submission via a Form Request plus `Auth::attempt`, `session()->regenerate()`, and `redirect()->intended()`.
- Logout via `Auth::logout()`, `session()->invalidate()`, and `session()->regenerateToken()`.
- Shared Inertia auth prop for the logged-in user name/email so future layout work has a stable base.

### Risks
- Protecting only index routes would leave create/store/edit/update/assign endpoints exposed; middleware must wrap the full backoffice route set.
- The current dashboard links directly to `/customers`; once protected, UX should account for the redirect to `/login` or update the CTA.
- Host PHP 8.2 is below the project requirement, so auth tests and Artisan verification must run through Docker.

### Ready for Proposal
Yes — the scope is clear and the smallest coherent design is identified. The next phase should propose `add-backoffice-auth` as a manual session-based auth slice with protected backoffice routes and Docker-first feature-test coverage.
