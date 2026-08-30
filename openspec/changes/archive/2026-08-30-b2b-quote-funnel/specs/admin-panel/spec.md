# Delta for Admin Panel

## ADDED Requirements

### Requirement: Shared Leads Navigation

The system SHALL include a `Leads` entry in the shared authenticated admin navigation alongside Dashboard, Customers, and Assets.

#### Scenario: Admin sees the leads entry

- GIVEN the user is authenticated
- WHEN any backoffice page renders inside `AdminLayout`
- THEN the navigation includes a link to `/admin/leads`

#### Scenario: Leads page is marked active

- GIVEN the user is authenticated
- WHEN the user visits `/admin/leads`
- THEN the shared navigation highlights the `Leads` entry as the active destination
