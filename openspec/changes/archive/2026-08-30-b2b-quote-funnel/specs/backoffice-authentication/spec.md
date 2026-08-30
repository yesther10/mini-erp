# Delta for Backoffice Authentication

## MODIFIED Requirements

### Requirement: Protected Backoffice Routes

The system MUST require authentication for all `/admin/*` backoffice routes, including dashboard, customers, assets, nested assignment routes, and `/admin/leads`, and SHALL treat all authenticated users uniformly because roles are out of scope for this change.
(Previously: Only `/customers` and `/assets` backoffice routes were explicitly covered, with assignment endpoints called out separately.)

#### Scenario: Guest requests a customer route

- GIVEN the visitor is not authenticated
- WHEN the visitor requests any `/admin/customers` route
- THEN the system redirects the visitor to the login page

#### Scenario: Guest requests an asset assignment route

- GIVEN the visitor is not authenticated
- WHEN the visitor requests any protected `/admin/assets` route, including assignment endpoints
- THEN the system redirects the visitor to the login page

#### Scenario: Guest requests the leads route

- GIVEN the visitor is not authenticated
- WHEN the visitor requests `/admin/leads`
- THEN the system redirects the visitor to the login page

#### Scenario: Authenticated user accesses protected backoffice routes

- GIVEN the user has an authenticated backoffice session
- WHEN the user requests an `/admin/*` backoffice route, including `/admin/leads`
- THEN the system grants access without additional role checks
