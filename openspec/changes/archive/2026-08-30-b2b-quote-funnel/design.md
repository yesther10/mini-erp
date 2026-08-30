# Design: B2B Quote Funnel

## Technical Approach

Upgrade `/` from an admin teaser into a public quote funnel, then add one small lead slice that bridges guest capture to admin review. Keep the existing Laravel pattern: thin controllers, Form Requests for normalization/validation, one write action, Eloquent model, and Inertia pages using `useForm` plus shared flash props. `/admin/leads` remains an auth-only backoffice route that any authenticated user can access without extra role checks. The MVP stays deliberately narrow: landing upgrade, public submit, authenticated list, and no CRM, pricing, contract, return, or assignment-history expansion.

## Architecture Decisions

| Decision | Options | Tradeoff | Choice / Rationale |
|---|---|---|---|
| Write boundary | Inline `Lead::create()` in controller; action class | Inline is smaller but breaks the repo's write-flow convention | Add `App\Actions\Leads\CreateLead` so `LeadController` stays thin like customer/asset writes |
| Transport | API/JSON endpoint; web POST + redirect | API adds a second feedback path and extra CSRF/error handling | Use web POST to a named public route and redirect back to `/`; this reuses current Inertia validation and flash behavior |
| Lead state | Persisted enum/status; no workflow state | Persisted status starts CRM drift and adds schema/tests | Keep no persisted workflow state in MVP; an optional UI-only “New” badge is acceptable |

## Data Flow

Guest ──GET `/`──> `PublicLandingController` ──> `Public/Landing`

Guest ──POST `/quote-requests`──> `StoreLeadRequest` ──> `CreateLead` ──> `leads`
  └── redirect `/` + flash success / validation errors

Admin ──GET `/admin/leads`──> `LeadController@index` ──> `Lead::latest()->paginate(15)` ──> `Leads/Index`

`Leads/Index` must render the submitted request details required by spec: company name, contact name, contact email, asset category, quantity, and need summary.

`PublicLandingController` should pass `quoteSubmitUrl`, `backofficeUrl`, and `assetCategories` (from `AssetCategory::values()` labels) so the landing form stays aligned with the current asset catalog.

## File Changes

| File | Action | Description |
|---|---|---|
| `routes/web.php` | Modify | Add public quote POST route and authenticated `/admin/leads` route inside the existing auth/admin groups |
| `app/Http/Controllers/PublicLandingController.php` | Modify | Pass landing form props and keep the admin CTA secondary |
| `app/Http/Controllers/LeadController.php` | Create | Host public `store()` and admin `index()` for the single lead slice, with `/admin/leads` available to any authenticated user |
| `app/Http/Requests/StoreLeadRequest.php` | Create | Trim/lowercase input, validate required fields, and expose `validated()` data |
| `app/Actions/Leads/CreateLead.php` | Create | Persist one lead record from validated attributes |
| `app/Models/Lead.php` | Create | Fillable lead model with a created-at descending default query usage |
| `database/migrations/*_create_leads_table.php` | Create | Add `company_name`, `contact_name`, `contact_email`, `asset_category`, `quantity`, `need_summary`, timestamps, and an index on `created_at` |
| `database/factories/LeadFactory.php` | Create | Support feature coverage for ordering and empty/non-empty states |
| `resources/js/Pages/Public/Landing.vue` | Modify | Replace admin-first hero with trust content plus a `useForm` quote form and success/error states |
| `resources/js/Pages/Leads/Index.vue` | Create | Minimal admin table with newest-first rows, submitted request details, and empty state |
| `resources/js/Components/AdminLayout.vue` | Modify | Add shared `Leads` navigation entry and active-state support |
| `tests/Feature/PublicLandingControllerTest.php` | Modify | Assert landing props and the quote-first public experience |
| `tests/Feature/LeadManagementTest.php` | Create | Cover valid submit, invalid submit, newest-first admin list, and empty state |
| `tests/Feature/Auth/BackofficeAuthenticationTest.php` | Modify | Cover guest redirect from `/admin/leads` |
| `tests/Playwright/admin-panel-runtime.spec.ts` | Modify | Extend existing runtime smoke for landing CTA visibility and Leads navigation |

## Interfaces / Contracts

```php
// StoreLeadRequest payload
[
    'company_name' => 'required|string|max:255',
    'contact_name' => 'required|string|max:255',
    'contact_email' => 'required|email|max:255',
    'asset_category' => ['required', Rule::in(AssetCategory::values())],
    'quantity' => 'required|integer|min:1',
    'need_summary' => 'required|string|max:2000',
]
```

## Testing Strategy

| Layer | What to Test | Approach |
|---|---|---|
| Unit | None unless normalization logic is extracted beyond the Form Request | Prefer feature coverage first, matching project convention |
| Integration | Landing render, valid submit persistence, invalid submit errors, guest redirect from `/admin/leads`, authenticated access without role checks, admin newest-first list with submitted request details, empty state | Run focused PHPUnit feature tests through Docker: `docker compose run --rm app php artisan test --compact --filter=Lead` |
| E2E | Public landing remains outside `AdminLayout`; authenticated nav exposes `Leads` and reaches `/admin/leads` | Extend existing Playwright smoke using the repo’s Docker-backed runtime harness |

## Threat Matrix

Routing/auth applicability was reviewed because this change adds public and authenticated routes.

| Boundary | Applicability | Design response | Planned RED tests |
|---|---|---|---|
| Documentation-like paths | N/A — no executable/document classification | None | None |
| Git repository selection | N/A — no repo/cwd command boundary | None | None |
| Commit state | N/A — no commit automation | None | None |
| Push state | N/A — no push automation | None | None |
| PR commands | N/A — no PR automation | None | None |

Route safety is handled outside this shared matrix with feature RED tests for guest submit, validation failure, guest redirect from `/admin/leads`, and authenticated admin access.

## Migration / Rollout

One reversible migration creates `leads`; no feature flag is needed. Rollout is `docker compose run --rm app php artisan migrate --force`, then normal web deployment. Rollback removes the new routes/UI and rolls back the `leads` table if the captured data does not need to be preserved.

## Open Questions

- [ ] Confirm final public marketing copy; the structure is fixed, but wording may still change during implementation.
