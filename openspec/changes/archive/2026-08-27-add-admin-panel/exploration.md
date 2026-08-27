## Exploration: Admin Panel with Shared Layout and Dashboard

### Current State

The codebase already has significant groundwork in place:

- **`AdminLayout.vue`** exists at `resources/js/Components/AdminLayout.vue` with a dark sidebar (Dashboard, Customers, Assets links) + logout. It uses `<slot />` for content. **No page currently uses it.**
- **`DashboardController`** already queries customers with eager-loaded `assetAssignments.asset`, plus stats (`totalCustomers`, `totalAssets`, `totalAssigned`). It renders `Inertia::render('Dashboard', ...)` with `stats` and `customers` props.
- **`Dashboard.vue`** is a landing/welcome page — NOT a real admin dashboard. It shows static text about the stack and a link to customers. It does NOT consume the `stats` or `customers` props from the controller.
- **`app.js`** resolves pages via `resolvePageComponent` but has no layout resolution logic.
- **Existing pages** (`Customers/Index.vue`, `Assets/Index.vue`) each define their own full-page `<main>` wrapper with standalone styling — they do NOT use `AdminLayout`.

### Affected Areas

- `resources/js/app.js` — needs layout resolution logic added
- `resources/js/Pages/Dashboard.vue` — must be rewritten to consume `stats` + `customers` props and render the admin dashboard
- `resources/js/Pages/Customers/Index.vue` — must wrap content in `AdminLayout` (or use persistent layout)
- `resources/js/Pages/Assets/Index.vue` — must wrap content in `AdminLayout` (or use persistent layout)
- `resources/js/Pages/Customers/Form.vue` — must wrap content in `AdminLayout`
- `resources/js/Pages/Assets/Form.vue` — must wrap content in `AdminLayout`
- `resources/js/Pages/Assets/Assign.vue` — must wrap content in `AdminLayout`
- `resources/js/Pages/Auth/Login.vue` — should NOT use admin layout (it's the login page)
- `app/Http/Controllers/DashboardController.php` — already correct, no changes needed
- `resources/js/Components/AdminLayout.vue` — already exists, may need minor tweaks (e.g., active route styling refinement)

### Approaches

1. **Inertia Persistent Layout via `app.js` resolver** — Modify the resolver in `app.js` to detect a `layout` property on page components and wrap them automatically. Pages declare `AdminLayout` as their layout; Inertia keeps it alive across navigations.
   - Pros: Layout state preserved across visits (sidebar scroll position, collapsed state). Standard Inertia v3 pattern. Single source of truth in resolver.
   - Cons: Requires modifying `app.js` resolver. Each page must explicitly declare its layout. Auth pages must opt out.
   - Effort: **Low** — ~15 lines in `app.js`, each page adds one property.

2. **Manual wrapper in each page** — Import `AdminLayout` in every page and wrap `<template>` content inside `<AdminLayout>...</AdminLayout>`.
   - Pros: No resolver changes. Explicit and visible in each file.
   - Cons: Layout is destroyed/recreated on every navigation (no state preservation). Repeated import boilerplate across 5+ pages. Auth page needs special handling.
   - Effort: **Low** — but poor long-term DX.

3. **`resolvePageComponent` with layout auto-detection by directory** — Resolver checks if the page path starts with `Pages/` (excluding `Auth/`) and auto-wraps with `AdminLayout`. Pages don't need to declare anything.
   - Pros: Zero per-page boilerplate. All non-auth pages get the layout automatically.
   - Cons: Implicit/magical — new developers won't know why pages have a sidebar. Harder to opt out for edge cases. Layout NOT persistent (destroyed on navigation).
   - Effort: **Low** — ~20 lines in `app.js`.

### Recommendation

**Approach 1: Inertia Persistent Layout via `app.js` resolver** — this is the idiomatic Inertia v3 pattern and the right foundation for a growing admin panel.

Specific implementation:

```js
// app.js resolver addition
import AdminLayout from './Components/AdminLayout.vue';

resolve: (name) => {
    const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
    return page.then((resolved) => {
        // Use default export for plain pages, or layout property for persistent layout
        if (resolved.default.layout !== undefined) {
            return resolved; // Page declares its own layout via defineOptions or static property
        }
        // Auto-wrap non-auth pages with AdminLayout
        if (!name.startsWith('Auth/')) {
            resolved.default.layout = (page) => h(AdminLayout, () => page);
        }
        return resolved;
    });
},
```

However, this has a subtlety: Inertia v3 persistent layouts in Vue require the layout to be defined on the **default export** as a function. The cleanest approach is:

- Add `AdminLayout` wrapping in `app.js` resolver for all non-Auth pages
- Pages declare `defineOptions({ layout: (page) => h(AdminLayout, () => page) })` OR the resolver auto-applies it

**Actually, the simplest and most maintainable approach**: Auto-apply `AdminLayout` in the resolver for all pages NOT under `Auth/`. This avoids touching every page file just to add a layout declaration. The Dashboard rewrite only needs to consume its props.

### Dashboard Content Design

The `DashboardController` already provides:
- `stats.totalCustomers`, `stats.totalAssets`, `stats.totalAssigned`
- `customers[]` with `id`, `legal_name`, `city`, `state`, `assets[]` (each with `internal_code`, `brand`, `model`, `status`)

The new `Dashboard.vue` should render:
1. **Stats cards row** — 3 cards: Total Customers, Total Assets, Assigned Assets (with visual distinction for assigned vs. available)
2. **Customer-asset table** — Customer name, location, assigned assets count, expandable detail or inline list showing asset internal_code + brand/model

### Risks

- **Login page must NOT get the admin layout** — the resolver logic must explicitly exclude `Auth/*` pages. Test this edge case.
- **Layout persistence requires Vue 3 setup** — the current `app.js` uses `createApp({ render: () => h(App, props) })`. Persistent layouts work with this setup, but the resolver must return a component with `.layout` on the default export.
- **Existing page styling conflict** — current pages define their own `<main class="mx-auto max-w-6xl...">` wrappers. When wrapped in `AdminLayout`, these will be nested inside the layout's `<main class="flex-1 overflow-y-auto px-8 py-10">`. The page-level wrappers must be removed or adjusted to avoid double padding/constraints.
- **Performance** — `DashboardController` loads ALL customers with ALL asset assignments eagerly. For a prototype/MVP this is fine, but it will need pagination or lazy loading as data grows.

### Ready for Proposal

**Yes** — the exploration is clear enough. The key decisions to confirm with the user:
1. Auto-apply `AdminLayout` in resolver (recommended) vs. explicit per-page declaration
2. Dashboard stats scope (currently loads all customers — acceptable for MVP?)
3. Any specific visual requirements for the stats cards or customer-asset table beyond what the controller already provides
