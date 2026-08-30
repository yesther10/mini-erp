# Quote Funnel Specification

## Purpose

Turn the public landing page into a B2B quote intake entry point and give admins a minimal place to review submitted leads.

## Requirements

### Requirement: Public Quote Landing

The system MUST keep `/` publicly accessible and SHALL present trust-focused B2B messaging plus a visible quote-request entry point.

#### Scenario: Guest opens the landing page

- GIVEN the visitor is not authenticated
- WHEN the visitor requests `/`
- THEN the system renders the public landing page with trust framing and a quote request path

#### Scenario: Landing keeps the admin entry secondary

- GIVEN the visitor is on `/`
- WHEN the page actions are shown
- THEN the quote request action is visible without hiding access to backoffice sign-in

### Requirement: Public Quote Submission

The system MUST accept a public quote request with company name, contact name, contact email, asset category, quantity, and need summary, and SHALL give clear success or validation feedback.

#### Scenario: Visitor submits a valid quote request

- GIVEN the visitor completes the required quote fields
- WHEN the visitor submits the quote request
- THEN the system stores the lead and confirms the request was received

#### Scenario: Visitor omits required information

- GIVEN the visitor leaves one or more required quote fields invalid
- WHEN the visitor submits the quote request
- THEN the system rejects the submission and shows field-level validation errors

### Requirement: Admin Lead Review

The system MUST let authenticated admins review submitted quote leads at `/admin/leads` in newest-first order and MAY show a minimal status badge.

#### Scenario: Admin reviews captured leads

- GIVEN multiple quote leads exist
- WHEN an authenticated user requests `/admin/leads`
- THEN the system lists the leads newest first with the submitted request details

#### Scenario: Admin sees an empty review state

- GIVEN no quote leads exist
- WHEN an authenticated user requests `/admin/leads`
- THEN the system shows an empty state instead of a blank table
