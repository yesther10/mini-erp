# Admin Panel Specification

## Purpose

Provides a shared persistent layout, dashboard stats view, and unified navigation for all authenticated backoffice pages in the mini ERP system.

## Requirements

### Requirement: Shared Layout Persistence

The system SHALL auto-apply `AdminLayout` to all non-Auth pages via an Inertia persistent layout resolver. Auth pages (under `Auth/*`) MUST NOT receive the admin layout.

#### Scenario: Authenticated user navigates to /customers

- GIVEN a user is authenticated
- WHEN the user navigates to /customers
- THEN the page renders inside `AdminLayout` with sidebar visible

#### Scenario: User navigates to /login

- GIVEN a user is on any page
- WHEN the user navigates to /login
- THEN the page renders WITHOUT `AdminLayout` (no sidebar)

#### Scenario: Layout persists across navigation

- GIVEN a user is on /customers inside `AdminLayout`
- WHEN the user navigates to /assets
- THEN `AdminLayout` remains alive (sidebar state, scroll position preserved)

### Requirement: Dashboard Stats Display

The system SHALL display three stat cards on the Dashboard: total customers, total assets, and assigned assets. Stats MUST reflect actual database counts from the `DashboardController` props.

#### Scenario: Dashboard shows stat cards

- GIVEN the user is authenticated and navigates to /
- WHEN the Dashboard page loads
- THEN three stat cards are visible: Total Customers, Total Assets, Assigned Assets

#### Scenario: Stats reflect actual database counts

- GIVEN the database contains 5 customers, 8 assets, and 3 assignments
- WHEN the Dashboard page loads
- THEN Total Customers shows 5, Total Assets shows 8, Assigned Assets shows 3

### Requirement: Customer-Asset Table

The system SHALL display a table on the Dashboard showing customers with their assigned assets. Customers with no assets MUST show "No assets assigned." An empty state MUST display when no customers exist.

#### Scenario: Customer with assigned assets

- GIVEN a customer "Acme Corp" has two assets assigned
- WHEN the Dashboard loads
- THEN the table shows "Acme Corp" with both asset details (name, type, or identifier)

#### Scenario: Customer with no assets

- GIVEN a customer "Bare Metals" has zero assets assigned
- WHEN the Dashboard loads
- THEN the table shows "Bare Metals" with "No assets assigned"

#### Scenario: Empty state

- GIVEN the database contains zero customers
- WHEN the Dashboard loads
- THEN the customer table area displays an empty state message
