# Proposal: Add Admin Panel with Shared Layout and Dashboard

## Intent

The codebase has `AdminLayout.vue` and a working `DashboardController` but they are disconnected: no page uses the layout, and `Dashboard.vue` ignores the controller's stats/customers props. Every page wraps itself in a standalone `<main>`, duplicating boilerplate and preventing consistent navigation. This change wires them together into a cohesive admin panel.

## Scope

### In Scope
- Auto-apply `AdminLayout` to all non-Auth pages via Inertia persistent layout resolver in `app.js`
- Rewrite `Dashboard.vue` to consume `stats` and `customers` props from `DashboardController`
- Remove standalone `<main>` wrappers from 5 existing pages so they render inside `AdminLayout`'s slot
- Add a feature test for `DashboardController` stats output

### Out of Scope
- Role-based access control (all authenticated users are uniform per existing auth spec)
- Pagination/lazy-loading of dashboard data (acceptable for MVP with small dataset)
- AdminLayout sidebar tweaks (active-route highlighting, collapse behavior, responsive hamburger)
- New navigation items or page creation beyond the Dashboard rewrite
- `Auth/Login.vue` — explicitly excluded from admin layout

## Capabilities

### New Capabilities
- `admin-panel`: Shared persistent layout, dashboard stats view, and unified navigation for authenticated backoffice pages

### Modified Capabilities
- None — this does not change spec-level behavior of existing capabilities. The backoffice-authentication spec is unaffected because layout wrapping is purely a frontend presentation concern.

## Approach

**Inertia Persistent Layout via `app.js` resolver (auto-apply for non-Auth pages).**

1. Modify `app.js` resolve callback to detect page path — if NOT `Auth/*`, attach `AdminLayout` as persistent layout via `h(AdminLayout, () => page)`.
2. Pages do NOT need to declare their layout; the resolver applies it automatically. Auth pages are explicitly excluded.
3. Remove the standalone `<main class="mx-auto flex min-h-screen max-w-*xl ...">` wrapper from each non-Auth page, replacing it with a simple `<div>` or `<section>` since `AdminLayout` now provides the page shell.
4. Rewrite `Dashboard.vue` to render: (a) 3 stat cards from `stats`, (b) a customer-asset summary table from `customers`.
5. Add a PHPUnit feature test asserting `DashboardController` returns correct stats and customer data.

### Tradeoffs

| Option | Pros | Cons |
|--------|------|------|
| **Auto-apply in resolver (chosen)** | Zero per-page boilerplate. All non-auth pages get layout automatically. Idiomatic Inertia v3. | Implicit — new devs need to know the resolver auto-wraps. |
| Per-page `defineOptions({ layout })` | Explicit, visible in each file. | Touches 6 files just for layout declaration. Easy to forget on new pages. |
| Manual `<AdminLayout>` wrapper | Simplest mental model. | Layout destroyed/recreated on every navigation. Repeated imports. |

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `resources/js/app.js` | Modified | Add `h` import, auto-apply `AdminLayout` for non-Auth pages |
| `resources/js/Pages/Dashboard.vue` | Rewritten | Consume `stats` + `customers` props; render stat cards + customer table |
| `resources/js/Pages/Customers/Index.vue` | Modified | Remove standalone `<main>` wrapper |
| `resources/js/Pages/Assets/Index.vue` | Modified | Remove standalone `<main>` wrapper |
| `resources/js/Pages/Customers/Form.vue` | Modified | Remove standalone `<main>` wrapper |
| `resources/js/Pages/Assets/Form.vue` | Modified | Remove standalone `<main>` wrapper |
| `resources/js/Pages/Assets/Assign.vue` | Modified | Remove standalone `<main>` wrapper |
| `tests/Feature/DashboardControllerTest.php` | New | Feature test for stats + customers props |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Login page gets admin layout accidentally | Low | Explicit `Auth/*` exclusion check in resolver; test with manual navigation |
| Double padding/constraint nesting | Medium | Remove `<main>` wrappers from all pages; `AdminLayout` provides `<main class="flex-1 px-8 py-10">` |
| Layout state breaks on edge-case navigation | Low | Inertia v3 persistent layout is well-tested; use standard `h()` pattern |
| Performance with all customers loaded | Low | MVP dataset is small; pagination is a future concern explicitly out of scope |

## Rollback Plan

1. Revert `resources/js/app.js` to remove the layout resolution logic and `h` import.
2. Restore the standalone `<main>` wrapper in each page file (re-add the `mx-auto flex min-h-screen max-w-*xl` classes).
3. Revert `Dashboard.vue` to the static landing page version.
4. Delete `tests/Feature/DashboardControllerTest.php`.
5. Run `docker compose run --rm app php artisan test --compact` to confirm all tests pass after rollback.

## Dependencies

- Existing `AdminLayout.vue` component (already implemented, no changes needed)
- Existing `DashboardController` with stats + customers props (already implemented, no changes needed)
- Inertia.js v3 persistent layout support (confirmed available in current stack)

## Success Criteria

- [ ] All non-Auth pages render inside `AdminLayout` sidebar without manual layout declaration
- [ ] `Auth/Login.vue` renders WITHOUT the admin layout (full standalone page)
- [ ] Dashboard displays 3 stat cards (total customers, total assets, assigned assets) from controller props
- [ ] Dashboard displays customer-asset summary table from controller props
- [ ] Sidebar active-route highlighting works correctly across all pages
- [ ] No double padding or visual nesting artifacts on any page
- [ ] Feature test passes asserting `DashboardController` returns expected stats and customer data
- [ ] All existing feature tests continue to pass after changes

## Proposal Question Round

> Since this is a delegated proposal, here are the questions that would improve the proposal if answered:

1. **Dashboard visual priority**: Should stat cards use a 3-column equal grid or a 2+1 layout (e.g., 2 small + 1 large for the most important metric)?
2. **Customer table scope**: Should the dashboard customer table show only customers with at least one assigned asset, or all customers regardless of assignment status?
3. **Empty state**: When there are zero customers or zero assets, should the dashboard show an onboarding message or just empty stat cards with zeros?
