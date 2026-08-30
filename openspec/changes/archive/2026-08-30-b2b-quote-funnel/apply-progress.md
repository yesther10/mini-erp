# Apply Progress: b2b-quote-funnel

**Change**: `b2b-quote-funnel`
**Mode**: Strict TDD
**Work Units Completed**: 1 (public-landing-quote-capture), 2 (admin-leads-review), 3 (runtime-regression-proof)
**Remediation Batch**: 1 (`sha256:2f69f9f591a20756bf8366d26121818e51451cdd91212978941ad19ee1b6a253`)

## Completed Tasks

- [x] 1.1 RED — Update `tests/Feature/PublicLandingControllerTest.php` with failing assertions for trust-first copy, `quoteSubmitUrl`, `backofficeUrl`, and `assetCategories` on `/`.
- [x] 1.2 RED — Create `tests/Feature/LeadManagementTest.php` failing valid and invalid `POST /quote-requests` scenarios for persistence, success flash, and field errors.
- [x] 2.1 GREEN — Create `app/Models/Lead.php`, `database/factories/LeadFactory.php`, and `database/migrations/*_create_leads_table.php` for the six quote fields plus a `created_at` index.
- [x] 2.2 GREEN — Create `app/Http/Requests/StoreLeadRequest.php`, `app/Actions/Leads/CreateLead.php`, and `app/Http/Controllers/LeadController.php@store`; wire the public POST in `routes/web.php`.
- [x] 2.3 GREEN — Update `app/Http/Controllers/PublicLandingController.php` and `resources/js/Pages/Public/Landing.vue` to use `useForm`, show quote CTA first, keep backoffice sign-in secondary, and render flash/errors.
- [x] 2.4 REFACTOR — Keep trimming/lowercasing in `StoreLeadRequest.php`, reuse current flash/form Tailwind patterns, and avoid pricing or CRM promises in landing copy.
- [x] 3.1 RED — Extend `tests/Feature/Auth/BackofficeAuthenticationTest.php` with failing guest redirect and authenticated `/admin/leads` access-without-role-check cases.
- [x] 3.2 RED — Extend `tests/Feature/LeadManagementTest.php` with failing newest-first, submitted-details, empty-state, and active-nav assertions for `GET /admin/leads`.
- [x] 3.3 GREEN — Finish `app/Http/Controllers/LeadController.php@index` and auth route wiring in `routes/web.php` with `Lead::latest()->paginate(15)` and mapped lead props.
- [x] 3.4 GREEN — Create `resources/js/Pages/Leads/Index.vue` and update `resources/js/Components/AdminLayout.vue` with `Leads` nav, active state, empty state, and optional UI-only `New` badge.
- [x] 3.5 REFACTOR — Keep `/admin/leads` inside the existing auth backoffice group, allow any authenticated user, and stop before filters, notes, or assignments.
- [x] 4.1 RED — Extend `tests/Playwright/admin-panel-runtime.spec.ts` with failing smoke coverage for public landing without `AdminLayout`, visible quote CTA, authenticated navigation to `/admin/leads`, and active-state proof for `Leads`.
- [x] 4.2 VERIFY — Run the repo-controlled runtime command after fixing the Playwright cache/tmp paths and container-aware setup flow.
- [x] 4.3 VERIFY — Rebuild the app image with Debian 12 Chromium runtime libraries and rerun the runtime smoke to confirm browser execution inside the app container.

## Files Changed

| File | Action | What Was Done |
|------|--------|---------------|
| `tests/Feature/PublicLandingControllerTest.php` | Modified | Replaced admin-first landing assertions with trust-first landing prop coverage. |
| `tests/Feature/LeadManagementTest.php` | Created | Added valid/invalid public quote submission tests + admin lead review tests (newest-first, details, empty state). |
| `tests/Feature/Auth/BackofficeAuthenticationTest.php` | Modified | Added guest redirect and authenticated access tests for `/admin/leads`. |
| `app/Models/Lead.php` | Created | Added fillable lead model with quantity casting. |
| `database/factories/LeadFactory.php` | Created | Added lead factory defaults aligned with asset categories. |
| `database/migrations/2026_08_29_154824_create_leads_table.php` | Created | Added the `leads` schema with six quote fields and a `created_at` index. |
| `app/Http/Requests/StoreLeadRequest.php` | Created | Added validation and input normalization for public quote submissions. |
| `app/Actions/Leads/CreateLead.php` | Created | Added the thin action boundary for lead persistence. |
| `app/Http/Controllers/LeadController.php` | Created | Added public `store()` and admin `index()` endpoints. |
| `app/Http/Controllers/PublicLandingController.php` | Modified | Added landing hero copy, submit URL, backoffice URL, and asset category props. |
| `resources/js/Pages/Public/Landing.vue` | Modified | Rebuilt the page into a quote-first landing form using `useForm`, shared flash styles, and field errors. |
| `resources/js/Pages/Leads/Index.vue` | Created | Minimal admin table with newest-first rows, submitted request details, and empty state. |
| `resources/js/Components/AdminLayout.vue` | Modified | Added the shared `Leads` navigation entry and exposed the active destination via `aria-current="page"`. |
| `routes/web.php` | Modified | Named the landing route, added public `POST /quote-requests`, and added authenticated `GET /admin/leads`. |
| `tests/Playwright/admin-panel-runtime.spec.ts` | Modified | Added runtime coverage for the `Leads` active-navigation scenario. |
| `playwright.config.ts` | Modified | Defaulted Playwright browser downloads to a hermetic repo-local path. |
| `tests/Playwright/support/global-setup.mjs` | Modified | Removed nested-docker assumptions when already inside the app container and started a local Laravel runtime server for Playwright. |
| `tests/Playwright/support/global-teardown.mjs` | Modified | Cleaned up the transient Laravel runtime server PID after Playwright runs. |
| `package.json` | Modified | Redirected Playwright temp/cache writes into repo-local testing storage for the repo-controlled runtime command. |
| `docker/app/Dockerfile` | Modified | Installed the Debian 12 Chromium shared-library set needed by Playwright inside the app image. |

## TDD Cycle Evidence

| Task | Test File | Layer | Safety Net | RED | GREEN | TRIANGULATE | REFACTOR |
|------|-----------|-------|------------|-----|-------|-------------|----------|
| 1.1 | `PublicLandingControllerTest.php` | Integration | 2 passed | ✅ written first | ✅ 4 passed | ✅ guest + authenticated | ➖ clean |
| 1.2 | `LeadManagementTest.php` | Integration | N/A (new) | ✅ written first | ✅ 4 passed | ✅ valid + invalid | ➖ clean |
| 2.1 | `LeadManagementTest.php` | Integration | N/A (new) | ✅ driven by 1.2 | ✅ 4 passed | ✅ persistence + rejection | ➖ clean |
| 2.2 | `LeadManagementTest.php` | Integration | N/A (new) | ✅ driven by 1.2 | ✅ 4 passed | ✅ flash + errors | ✅ thin controller |
| 2.3 | `PublicLandingControllerTest.php` | Integration | 2 passed | ✅ landing assertions failed | ✅ 4 passed | ✅ hero + URLs + categories | ✅ reused UI patterns |
| 2.4 | Both test files | Integration | 4 passed before cleanup | ✅ approval reused | ✅ 4 passed after cleanup | ✅ normalization covered | ✅ Pint passed |
| 3.1 | `BackofficeAuthenticationTest.php` | Integration | baseline existed | ✅ written first | ✅ 19 passed | ✅ guest redirect + auth access | ➖ clean |
| 3.2 | `LeadManagementTest.php` | Integration | N/A (new cases) | ✅ written first | ✅ 19 passed | ✅ newest-first + details + empty | ➖ clean |
| 3.3 | `LeadManagementTest.php` | Integration | N/A (new cases) | ✅ driven by 3.2 | ✅ 19 passed | ✅ index + pagination + props | ✅ thin controller |
| 3.4 | `LeadManagementTest.php` | Integration | N/A (new cases) | ✅ driven by 3.2 | ✅ 19 passed | ✅ admin nav + empty state | ✅ reused layout patterns |
| 3.5 | All tests | Integration | 19 passed before cleanup | ✅ approval reused | ✅ 19 passed after cleanup | ✅ auth group preserved | ✅ Pint passed |
| 4.1 | `admin-panel-runtime.spec.ts` | E2E | ⚠️ existing runtime command reproduced an `ENOSPC` blocker first | ✅ active-nav runtime assertion written first | ✅ 8 passed | ✅ inactive on Customers + active on Leads | ✅ semantic `aria-current` hook |
| 4.2 | `admin-panel-runtime.spec.ts` | E2E | ⚠️ browser install failed in `/home/app/.cache` and transform cache failed in `/tmp` | ✅ failing repo-controlled runtime command captured first | ✅ 8 passed | ✅ hermetic browser path + repo-local tmp path + in-container runtime server | ✅ host/container setup branches split |
| 4.3 | `admin-panel-runtime.spec.ts` | E2E | ⚠️ browser launch failed first with missing `libglib-2.0.so.0` | ✅ runtime smoke exposed missing Chromium shared libraries | ✅ 8 passed after image rebuild | ✅ Debian 12 Chromium dependency set applied to the app image | ✅ teardown now removes transient runtime PID |

## Work Unit Evidence

### Work Unit 1: public-landing-quote-capture

| Evidence | Required value |
|---|---|
| Focused test command and exact result | `docker compose run --rm app php artisan test --compact --filter='PublicLandingControllerTest|LeadManagementTest'` → 4 passed, 51 assertions, exit 0 |
| Runtime harness | nginx + built assets + curl: GET `/` 200, POST `/quote-requests` 302 with flash success |
| Rollback boundary | `routes/web.php`, landing props/UI, lead write-path files, tests, migration |

### Work Unit 2: admin-leads-review

| Evidence | Required value |
|---|---|
| Focused test command and exact result | `docker compose run --rm app php artisan test --compact --filter='BackofficeAuthenticationTest|LeadManagementTest'` → 19 passed, 124 assertions, exit 0 |
| Runtime harness | Same nginx stack → sign in, open `/admin/leads`, confirm nav + newest row |
| Rollback boundary | `/admin/leads` route, `LeadController@index`, `Leads/Index.vue`, `AdminLayout.vue` |

### Work Unit 3: runtime-regression-proof

| Evidence | Required value |
|---|---|
| Focused test command and exact result | `docker compose run --rm app npm run test:runtime -- admin-panel-runtime.spec.ts` → 8 passed in 11.1s, exit 0 |
| Runtime harness command/scenario and exact result | Same repo-controlled command → landing stays public, admin pages mount inside `AdminLayout`, `/admin/leads` navigation renders and is marked active via `aria-current="page"`, exit 0 |
| Rollback boundary | `tests/Playwright/admin-panel-runtime.spec.ts`, `playwright.config.ts`, `tests/Playwright/support/global-setup.mjs`, `tests/Playwright/support/global-teardown.mjs`, `package.json`, `docker/app/Dockerfile`, `resources/js/Components/AdminLayout.vue` |

## Remediation Result

```yaml
schema: gentle-ai.remediation-result/v1
change: b2b-quote-funnel
work_unit: remediation-verify-blockers
failed_evidence_revision: sha256:2f69f9f591a20756bf8366d26121818e51451cdd91212978941ad19ee1b6a253
lineage_id: ""
generation: 0
fix_batch: 0
attempt_token: sha256:29356cc549f24090ca9223652c2e78bec09bf6abf0243ecaec899612cf80e3e9
status: ready-for-fresh-independent-verify
```

```json
{
  "schema": "gentle-ai.remediation-evidence/v1",
  "change": "b2b-quote-funnel",
  "failed_evidence_revision": "sha256:2f69f9f591a20756bf8366d26121818e51451cdd91212978941ad19ee1b6a253",
  "lineage_id": "",
  "generation": 0,
  "fix_batch": 0,
  "focused_test": {
    "command": "docker compose run --rm app npm run test:runtime -- admin-panel-runtime.spec.ts",
    "result": "8 passed (11.1s)",
    "exit_code": 0
  },
  "runtime_harness": {
    "command": "docker compose run --rm app npm run test:runtime -- admin-panel-runtime.spec.ts",
    "result": "Playwright executed inside the app container with repo-local browser/tmp paths and a container-aware Laravel runtime server",
    "exit_code": 0
  },
  "rollback_boundary": [
    "tests/Playwright/admin-panel-runtime.spec.ts",
    "playwright.config.ts",
    "tests/Playwright/support/global-setup.mjs",
    "tests/Playwright/support/global-teardown.mjs",
    "package.json",
    "docker/app/Dockerfile",
    "resources/js/Components/AdminLayout.vue"
  ]
}
```

## Deviations from Design

None — implementation still matches the design intent. The only added surface is a semantic `aria-current` attribute to make the active navigation state testable and accessible.

## Issues Found

- The repo-controlled runtime harness had three real blockers, not one: browser downloads targeted `/home/app/.cache`, Playwright transforms targeted `/tmp`, and the app image lacked Debian 12 Chromium runtime libraries.
- A local `docker compose build app` completed the new image layers but the final metadata write hit root-disk exhaustion; the subsequent passing runtime run confirmed the rebuilt image was already usable.
- Repeated failed runtime attempts can exhaust Docker root storage and destabilize MySQL. Pruning unused Docker images/cache restored the environment during remediation.

## Remaining Tasks

- [ ] Fresh independent `sdd-verify` run using the remediated candidate.
- [ ] Archive only after a new passing verify report exists.

## Workload / PR Boundary

- **Mode**: stacked PR slice
- **Work Units Completed**: 1 (public-landing-quote-capture), 2 (admin-leads-review), 3 (runtime-regression-proof)
- **Boundary**: Runtime-only remediation for the failed verify evidence revision. Stops after restoring the repo-controlled Playwright path and proving the `Leads` active-navigation scenario.
- **Estimated review budget impact**: +104 authored lines in this remediation batch (within the 200-line attempt cap)

## Status

14/14 tasks remain complete. The remediation batch cleared the runtime blocker and added the missing required navigation coverage. Ready for a fresh independent verify run.
