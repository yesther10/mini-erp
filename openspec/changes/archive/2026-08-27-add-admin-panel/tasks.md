# Tasks: Add Admin Panel with Shared Layout and Dashboard

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | 200–260 |
| 400-line budget risk | Low |
| Chained PRs recommended | No |
| Suggested split | Single PR |
| Delivery strategy | ask-on-risk |
| Chain strategy | stacked-to-main |

Decision needed before apply: Yes
Chained PRs recommended: No
Chain strategy: stacked-to-main
400-line budget risk: Low

### Suggested Work Units

| Unit | Goal | Likely PR | Focused test command | Runtime harness | Rollback boundary |
|------|------|-----------|----------------------|-----------------|-------------------|
| 1 | Layout wiring + page cleanup | PR 1 | `docker compose run --rm app php artisan test --compact` | Manual browser: navigate /customers, verify sidebar renders | Revert app.js + 5 page wrappers; no data loss |
| 2 | Dashboard rewrite + test | PR 1 | `docker compose run --rm app php artisan test --compact` | Manual browser: visit /, verify stat cards + table render | Revert Dashboard.vue + delete test file |

## Phase 1: Layout Wiring

- [x] 1.1 Modify `resources/js/app.js`: import `AdminLayout` from `./Components/AdminLayout.vue`; wrap the `resolve` callback so non-`Auth/*` pages get `component.default.layout = AdminLayout`
- [x] 1.2 Manual verification: navigate to `/customers` — sidebar visible; navigate to `/login` — no sidebar

## Phase 2: Page Cleanup

- [x] 2.1 `resources/js/Pages/Customers/Index.vue`: replace `<main class="mx-auto ...">` wrapper with `<div class="space-y-6">`
- [x] 2.2 `resources/js/Pages/Customers/Form.vue`: replace `<main>` wrapper with `<div class="space-y-6">`
- [x] 2.3 `resources/js/Pages/Assets/Index.vue`: replace `<main>` wrapper with `<div class="space-y-6">`
- [x] 2.4 `resources/js/Pages/Assets/Form.vue`: replace `<main>` wrapper with `<div class="space-y-6">`
- [x] 2.5 `resources/js/Pages/Assets/Assign.vue`: replace `<main>` wrapper with `<div class="space-y-6">`
- [x] 2.6 Manual verification: browse all 5 pages — no double padding, content fits within AdminLayout slot

## Phase 3: Dashboard Rewrite

- [x] 3.1 Rewrite `resources/js/Pages/Dashboard.vue`: consume `stats` and `customers` props; render 3 stat cards (Total Customers, Total Assets, Assigned Assets) in a 3-column grid
- [x] 3.2 Add customer-asset summary table: show `legal_name`, city/state, assigned assets (or "No assets assigned"); empty state when zero customers
- [x] 3.3 Manual verification: visit `/` with seeded data — stat cards show correct counts, table rows match customers

## Phase 4: Testing

- [x] 4.1 Create `tests/Feature/DashboardControllerTest.php`: seed customers and assets, assert `GET /` returns Inertia Dashboard with `stats` matching seeded counts and `customers` array structure
- [x] 4.2 Run `docker compose run --rm app php artisan test --compact` — all tests pass including new test

## Phase 5: Verification

- [x] 5.1 Run full test suite: `docker compose run --rm app php artisan test --compact`
- [x] 5.2 Manual smoke test: navigate / → /customers → /assets → /login → back to / — layout persists, Login excluded, no visual artifacts
