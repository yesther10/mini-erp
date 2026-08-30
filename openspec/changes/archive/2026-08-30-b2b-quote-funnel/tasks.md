# Tasks: B2B Quote Funnel

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | 480-620 |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 public landing + quote capture → PR 2 admin leads review + auth/nav → PR 3 runtime smoke + final verify |
| Delivery strategy | auto-chain |
| Chain strategy | stacked-to-main |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: stacked-to-main
400-line budget risk: High

### Suggested Work Units

| Unit | Goal | Likely PR | Focused test command | Runtime harness | Rollback boundary |
|------|------|-----------|----------------------|-----------------|-------------------|
| 1 | Public landing contract and quote capture flow | PR 1 | `docker compose run --rm app php artisan test --compact --filter='PublicLandingControllerTest|LeadManagementTest'` | `docker compose up -d mysql app nginx && docker compose run --rm app php artisan migrate --force && docker compose run --rm app npm run build` → curl GET `/` returns 200, CSRF-backed POST `/quote-requests` returns 302 redirect with flash success | `routes/web.php`, landing props/UI, lead write path, `leads` migration/model/factory |
| 2 | Authenticated `/admin/leads` review with shared nav | PR 2 | `docker compose run --rm app php artisan test --compact --filter='LeadManagementTest|BackofficeAuthenticationTest'` | Same harness → sign in, open `/admin/leads`, confirm nav + newest row | `/admin/leads` route, `LeadController@index`, `Leads/Index.vue`, `AdminLayout.vue` |
| 3 | Runtime smoke and final regression proof | PR 3 | `docker compose run --rm app npm run test:runtime -- admin-panel-runtime.spec.ts` | Same harness → `/` stays public, `/admin/leads` stays auth-only | `tests/Playwright/admin-panel-runtime.spec.ts`, final verification-only tweaks |

## Phase 1: Public Landing RED

- [x] 1.1 RED — Update `tests/Feature/PublicLandingControllerTest.php` with failing assertions for trust-first copy, `quoteSubmitUrl`, `backofficeUrl`, and `assetCategories` on `/`.
- [x] 1.2 RED — Create `tests/Feature/LeadManagementTest.php` failing valid and invalid `POST /quote-requests` scenarios for persistence, success flash, and field errors.

## Phase 2: Public Capture GREEN

- [x] 2.1 GREEN — Create `app/Models/Lead.php`, `database/factories/LeadFactory.php`, and `database/migrations/*_create_leads_table.php` for the six quote fields plus a `created_at` index.
- [x] 2.2 GREEN — Create `app/Http/Requests/StoreLeadRequest.php`, `app/Actions/Leads/CreateLead.php`, and `app/Http/Controllers/LeadController.php@store`; wire the public POST in `routes/web.php`.
- [x] 2.3 GREEN — Update `app/Http/Controllers/PublicLandingController.php` and `resources/js/Pages/Public/Landing.vue` to use `useForm`, show quote CTA first, keep backoffice sign-in secondary, and render flash/errors.
- [x] 2.4 REFACTOR — Keep trimming/lowercasing in `StoreLeadRequest.php`, reuse current flash/form Tailwind patterns, and avoid pricing or CRM promises in landing copy.

## Phase 3: Admin Review RED/GREEN

- [x] 3.1 RED — Extend `tests/Feature/Auth/BackofficeAuthenticationTest.php` with failing guest redirect and authenticated `/admin/leads` access-without-role-check cases.
- [x] 3.2 RED — Extend `tests/Feature/LeadManagementTest.php` with failing newest-first, submitted-details, empty-state, and active-nav assertions for `GET /admin/leads`.
- [x] 3.3 GREEN — Finish `app/Http/Controllers/LeadController.php@index` and auth route wiring in `routes/web.php` with `Lead::latest()->paginate(15)` and mapped lead props.
- [x] 3.4 GREEN — Create `resources/js/Pages/Leads/Index.vue` and update `resources/js/Components/AdminLayout.vue` with `Leads` nav, active state, empty state, and optional UI-only `New` badge.
- [x] 3.5 REFACTOR — Keep `/admin/leads` inside the existing auth backoffice group, allow any authenticated user, and stop before filters, notes, or assignments.

## Phase 4: Runtime / Verify

- [x] 4.1 RED — Extend `tests/Playwright/admin-panel-runtime.spec.ts` with failing smoke coverage for public landing without `AdminLayout`, visible quote CTA, and authenticated navigation to `/admin/leads`.
- [x] 4.2 VERIFY — Run `docker compose run --rm app php artisan test --compact --filter='PublicLandingControllerTest|LeadManagementTest|BackofficeAuthenticationTest'`, `docker compose run --rm app npm run test:runtime -- admin-panel-runtime.spec.ts`, and `docker compose run --rm app vendor/bin/pint --test`.
- [x] 4.3 VERIFY — Run `docker compose run --rm app php artisan migrate --force`, `docker compose run --rm app php artisan test --compact`, and browser smoke via `docker compose up -d mysql app nginx` + `docker compose run --rm --service-ports app npm run dev`.
