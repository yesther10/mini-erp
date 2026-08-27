# Backoffice Authentication Specification

## Purpose

Define the minimal session-authenticated backoffice access required for the interview MVP.

## Requirements

### Requirement: Guest Login Entry

The system MUST provide a guest-accessible login entry for backoffice users and MUST NOT require authentication to reach it.

#### Scenario: Guest opens the login page

- GIVEN the visitor is not authenticated
- WHEN the visitor requests the backoffice login page
- THEN the system renders the login page

#### Scenario: Authenticated user revisits the login page

- GIVEN the user already has an authenticated backoffice session
- WHEN the user requests the login page
- THEN the system redirects the user to the default backoffice destination

### Requirement: Session Login

The system MUST authenticate backoffice users with the existing Laravel session guard and SHALL return them to their intended backoffice destination after a successful login.

#### Scenario: Valid credentials create a backoffice session

- GIVEN the visitor attempted to access a protected backoffice route
- WHEN the visitor submits valid credentials
- THEN the system creates an authenticated session and redirects to the intended route

#### Scenario: Invalid credentials are rejected

- GIVEN the visitor is on the login page
- WHEN the visitor submits invalid credentials
- THEN the system keeps the visitor unauthenticated and shows an authentication error

### Requirement: Session Logout

The system MUST allow an authenticated backoffice user to end the current session from the backoffice.

#### Scenario: Authenticated user logs out

- GIVEN the user has an authenticated backoffice session
- WHEN the user submits the logout action
- THEN the system ends the session and redirects the user to the login page

#### Scenario: Logged out user retries a protected route

- GIVEN the user has logged out successfully
- WHEN the user requests a protected backoffice route
- THEN the system redirects the user to the login page

### Requirement: Protected Backoffice Routes

The system MUST require authentication for all `/customers` and `/assets` backoffice routes, including nested assignment routes, and SHALL treat all authenticated users uniformly because roles are out of scope for this change.

#### Scenario: Guest requests a customer route

- GIVEN the visitor is not authenticated
- WHEN the visitor requests any `/customers` route
- THEN the system redirects the visitor to the login page

#### Scenario: Guest requests an asset assignment route

- GIVEN the visitor is not authenticated
- WHEN the visitor requests any protected `/assets` route, including assignment endpoints
- THEN the system redirects the visitor to the login page

#### Scenario: Authenticated user accesses protected backoffice routes

- GIVEN the user has an authenticated backoffice session
- WHEN the user requests a `/customers` or `/assets` backoffice route
- THEN the system grants access without additional role checks
