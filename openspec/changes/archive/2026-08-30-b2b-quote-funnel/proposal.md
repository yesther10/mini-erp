# Proposal: B2B Quote Funnel

## Intent

Create a two-day interview-ready MVP that turns the public landing page into a quote funnel and lets admins review inbound leads. It must prove public-to-backoffice value without expanding the rental domain and later be built with strict TDD in Docker.

## Scope

### In Scope
- Upgrade `/` into a professional B2B page with quote CTA and trust framing.
- Add a quote form for company, contact, asset category, quantity, and need summary.
- Add authenticated `/admin/leads` review ordered newest first, with a tiny status badge only if trivial.

### Out of Scope
- Pricing engines, quote calculation, contracts, returns, assignment history, asset reservation logic, and deeper rental operations.
- Rental order workflow, conversion, notifications, email automation, and CRM sprawl.
- Lead assignment, notes timeline, follow-up tasks, or deep reporting.

## Capabilities

### New Capabilities
- `quote-funnel`: Marketing page, quote submission, lead persistence, and admin review.

### Modified Capabilities
- `admin-panel`: Add a Leads entry to shared admin navigation.
- `backoffice-authentication`: Protect `/admin/leads` like other backoffice routes.

## Approach

Follow the existing thin-controller + Form Request + Inertia pattern. Add one `leads` persistence slice, reuse shared flash props, and keep the UI to one landing page plus one admin list. Later work should start with public feature tests, then admin access tests, then minimal delivery in Docker.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `routes/web.php` | Modified | Public submit and protected leads routes |
| `app/Http/Controllers/PublicLandingController.php` | Modified | Landing props |
| `app/Http/Controllers/LeadController.php`, `app/Http/Requests/StoreLeadRequest.php` | New | Index and validation |
| `app/Models/Lead.php`, `database/migrations/*create_leads_table.php` | New | Lead persistence |
| `resources/js/Pages/Public/Landing.vue`, `resources/js/Pages/Leads/Index.vue` | Modified/New | Funnel page and admin list |
| `resources/js/Components/AdminLayout.vue`, `tests/Feature/*Lead*` | Modified/New | Leads nav and coverage |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| UI polish expands too far | Med | Freeze to one page, one form, one admin list |
| Lead flow becomes a mini-CRM | High | Reject notes, assignments, automations, and deep statuses |
| Marketing promises exceed product reality | Med | Keep copy focused on quote capture and follow-up |

## Rollback Plan

Remove lead routes, model, migration, admin page, and landing form; revert landing copy; roll back the `leads` table if deployed.

## Dependencies

- Docker test runner and strict TDD workflow from `openspec/config.yaml`.

## Success Criteria

- [ ] A guest can submit a quote request from the landing page and receive clear feedback.
- [ ] An admin can view persisted leads at `/admin/leads` in newest-first order.
- [ ] The slice stays reviewable and realistic for a two-day interview MVP.
