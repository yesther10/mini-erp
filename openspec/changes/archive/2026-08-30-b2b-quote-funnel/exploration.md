## Exploration: b2b-quote-funnel

### Current State

The repo already has a usable backoffice foundation but almost no public sales funnel. `routes/web.php` exposes only `/`, `/login`, and authenticated `/admin/*` routes. The landing page in `resources/js/Pages/Public/Landing.vue` is a single hero with admin-oriented copy and CTAs to `/admin/dashboard` and `/login`, while `app/Http/Controllers/PublicLandingController.php` only passes `ctaUrl`. The backoffice already supports customers, assets, assignments, and a dashboard through thin controllers, Form Requests, action classes, and Inertia pages with shared auth/flash props. The domain is still operationally shallow: `Asset` uses a single `assignment()` relation and `asset_assignments.asset_id` is unique, so there is no assignment history, contract lifecycle, return flow, quote intake, or lead management yet.

### Affected Areas
- `routes/web.php` — public quote submission and admin lead review would require new route contracts.
- `app/Http/Controllers/PublicLandingController.php` — landing props would need to grow beyond a single CTA URL.
- `resources/js/Pages/Public/Landing.vue` — the biggest interview-impact surface; currently lacks benchmark-style sections and any capture flow.
- `app/Http/Middleware/HandleInertiaRequests.php` — success/error flash sharing is already present and can support public form feedback.
- `app/Http/Controllers/CustomerController.php` and `app/Http/Controllers/AssetController.php` — show the existing thin-controller pattern a new lead flow should match.
- `app/Models/Asset.php` and `database/migrations/2026_08_26_170100_create_asset_assignments_table.php` — prove deeper rental operations would touch core lifecycle modeling, not just UI.
- `tests/Feature/PublicLandingControllerTest.php` — existing public HTTP coverage can anchor landing and quote-flow TDD.
- `tests/Feature/Auth/BackofficeAuthenticationTest.php` and `tests/Playwright/admin-panel-runtime.spec.ts` — confirm the project already verifies public/admin boundaries and can extend runtime coverage for the funnel.
- `README.md` — currently understates implemented scope, which matters for interview framing once the funnel exists.

### Approaches
1. **Landing-only polish** — Rework the homepage copy and sections to mirror a stronger B2B positioning without adding backend capture.
   - Pros: Fastest path, lowest schema risk, strong visual improvement for screenshots and demo videos.
   - Cons: No end-to-end business loop, no new domain proof, and the primary CTA still dies at admin auth instead of collecting demand.
   - Effort: Low

2. **Landing + public quote request capture + admin leads** — Add a benchmark-inspired landing plus a minimal public quote form that persists leads and exposes them in the admin.
   - Pros: Highest interview impact per day, creates a complete funnel from visitor intent to internal follow-up, and reuses existing Laravel patterns cleanly.
   - Cons: Needs one new persistence slice and careful scope discipline to avoid turning into a CRM.
   - Effort: Medium

3. **Deeper rental operations** — Expand contracts, returns, assignment history, asset availability rules, or quote-to-order domain workflows.
   - Pros: Richer ERP story and stronger long-term domain depth.
   - Cons: The current model is not ready for it; unique active assignments, missing contract entities, and absent lifecycle specs make this a multi-slice change that is too large for a two-day MVP.
   - Effort: High

### Recommendation

Choose **Approach 2**, but keep it BRUTALLY small: upgrade the landing into a B2B quote funnel, add a public quote request form with only the fields needed to prove demand capture, and expose a simple `/admin/leads` review list for the backoffice. This gives the strongest demo story in two days because it connects marketing, product intent, persistence, and admin operations without reopening the harder rental lifecycle model.

Recommended MVP boundary for the next change:
- Public landing: stronger headline, category-led solution blocks, trust/social proof, and one prominent quote CTA.
- Public capture: a single form for company, contact, demand summary, asset category, quantity, and message.
- Admin: read-only lead list ordered by newest first, plus a minimal status badge only if it fits without schema churn.
- Explicitly out of scope: email automation, quote generation, contracts, returns, assignment history, SLA workflows, and customer conversion flows.

### Risks
- Marketing polish can bloat fast; if copy sections and visual variants grow, the slice can spill over the 400-line review budget.
- Adding “just one more” lead workflow step turns the change into a CRM instead of an interview MVP.
- Deeper rental language on the landing can overpromise capabilities the backoffice does not yet implement.
- README and demo narrative must be updated carefully so the public promise matches the actual MVP.

### Ready for Proposal

Yes — the next proposal should target a focused public quote funnel with admin lead intake, explicitly rejecting deeper rental operations for this slice and keeping the implementation small enough for strict TDD over two days.
