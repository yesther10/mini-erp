# Design: Add Admin Panel with Shared Layout and Dashboard

## Technical Approach

Auto-apply `AdminLayout` via Inertia v3 persistent layout pattern in the `app.js` resolver. The resolver's `resolve` callback returns a component wrapped with `h(AdminLayout, () => page.component)` for every page except `Auth/*`. Each existing page strips its standalone `<main>` wrapper so content slots into `AdminLayout`'s `<slot />`. `Dashboard.vue` is rewritten from a static landing page into a data-driven stats dashboard consuming `DashboardController` props.

## Architecture Decisions

### Decision: Auto-apply layout in resolver (chosen)

**Choice**: Detect page path in `resolve` callback; wrap non-Auth pages with `h(AdminLayout, () => page.component)`.

| Option | Pros | Cons |
|--------|------|------|
| Auto-apply in resolver | Zero per-page boilerplate; idiomatic Inertia v3 | Implicit — new devs need to know the resolver auto-wraps |
| Per-page `defineOptions({ layout })` | Explicit, visible in each file | Touches 6+ files just for layout declaration |
| Manual `<AdminLayout>` wrapper | Simplest mental model | Layout destroyed/recreated on every navigation |

**Rationale**: The resolver approach is the canonical Inertia v3 pattern for app-wide layouts. The `Auth/*` exclusion check is a simple string prefix match — reliable and explicit. New pages automatically inherit the layout with zero configuration.

### Decision: Extract stat card and customer table into sub-components

**Choice**: Keep Dashboard.vue as a single-file composition — stat cards and customer table inline, no separate component files.

| Option | Pros | Cons |
|--------|------|------|
| Single-file composition | Faster to build, easier to review for 3 cards + 1 table | Less reusable (but reuse is out of scope) |
| Separate StatCard.vue + CustomerTable.vue | Reusable, testable in isolation | Over-engineered for MVP; adds 2 files for trivially simple markup |

**Rationale**: The dashboard has exactly 3 stat cards and 1 table. There is no reuse requirement. Extracting components would be premature abstraction. Keep it flat.

## Data Flow

```
DashboardController::__invoke()
  ├── Customer::count()           → stats.totalCustomers
  ├── Asset::count()              → stats.totalAssets
  ├── Asset::whereHas(assignment) → stats.totalAssigned
  └── Customer::with(assets)      → customers[]
        └── map to { id, legal_name, city, state, assets[] }

         ┌──────────────────────────────────────────┐
         │  Inertia::render('Dashboard', props)     │
         └──────────────┬───────────────────────────┘
                        ▼
         ┌──────────────────────────────────────────┐
         │  app.js resolver                         │
         │  Auth/* → page component (no layout)     │
         │  Other → h(AdminLayout, () => page)      │
         └──────────────┬───────────────────────────┘
                        ▼
         ┌──────────────────────────────────────────┐
         │  Dashboard.vue                           │
         │  Props: stats, customers                 │
         │  ┌─────────┐ ┌─────────┐ ┌─────────┐    │
         │  │ Total   │ │ Total   │ │ Assigned│    │
         │  │ Cust.   │ │ Assets  │ │ Assets  │    │
         │  └─────────┘ └─────────┘ └─────────┘    │
         │  ┌──────────────────────────────────┐    │
         │  │ Customer-Asset Summary Table      │    │
         │  │ legal_name | city/st | assets[]   │    │
         │  └──────────────────────────────────┘    │
         └──────────────────────────────────────────┘
```

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `resources/js/app.js` | Modify | Import `AdminLayout`, wrap resolve callback to auto-apply layout for non-Auth pages |
| `resources/js/Pages/Dashboard.vue` | Rewrite | Replace static landing page with stats cards (3-col grid) + customer-asset table |
| `resources/js/Pages/Customers/Index.vue` | Modify | Replace `<main class="mx-auto ...">` wrapper with `<div class="space-y-6">` |
| `resources/js/Pages/Customers/Form.vue` | Modify | Replace `<main>` wrapper with `<div class="space-y-6">` |
| `resources/js/Pages/Assets/Index.vue` | Modify | Replace `<main>` wrapper with `<div class="space-y-6">` |
| `resources/js/Pages/Assets/Form.vue` | Modify | Replace `<main>` wrapper with `<div class="space-y-6">` |
| `resources/js/Pages/Assets/Assign.vue` | Modify | Replace `<main>` wrapper with `<div class="space-y-6">` |
| `tests/Feature/DashboardControllerTest.php` | Create | Feature test asserting stats + customer props |

## Interfaces / Contracts

### DashboardController props passed to Dashboard.vue

```typescript
// stats — passed as a plain object
interface DashboardStats {
  totalCustomers: number;
  totalAssets: number;
  totalAssigned: number;
}

// customers — array of customer objects with nested assets
interface DashboardCustomer {
  id: number;
  legal_name: string;
  city: string;
  state: string;
  assets: {
    internal_code: string;
    brand: string;
    model: string;
    status: string;
  }[];
}
```

### Resolver pattern in app.js

```javascript
resolve: (name) => {
  const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

  if (name.startsWith('Auth/')) {
    return page;
  }

  return page.then((component) => {
    component.default.layout = AdminLayout;
    return component;
  });
},
```

**Note**: Inertia v3 supports `component.default.layout` assignment. This avoids the `h()` wrapper pattern and is cleaner for persistent layouts. The `h()` pattern (proposal) works but `layout` assignment is the documented Inertia v3 approach.

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Feature | DashboardController returns correct stats + customers | `DashboardControllerTest.php` — seed customers/assets, assert Inertia props match expected counts and structure |
| Manual | All non-Auth pages render inside AdminLayout | Navigate to `/customers`, `/assets`, etc. — verify sidebar is visible |
| Manual | Auth/Login.vue renders without layout | Navigate to `/login` — verify no sidebar |
| Manual | Layout persists across navigation | Click sidebar links — verify layout doesn't remount |

## Migration / Rollout

No data migration required. This is a frontend-only wiring change with one new test file. All changes are backward-compatible — no database, route, or API changes.

## Open Questions

- None. The proposal's question round items (card layout, table scope, empty state) are resolved: 3-column equal grid, all customers regardless of assignment, zero-count cards with empty table row.
