# Sicuti HRD Cuti Tracker

## What This Is

Sicuti HRD Cuti Tracker is an internal HR/employee leave entitlement application. It now runs as a real native procedural PHP + MySQLi system where HR owns employee onboarding, provisions login credentials, and both HR and employees work against live database-backed leave data.

## Core Value

HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data through an enforced, real backend flow.

## Current State

- **Latest shipped milestone:** `v2.0` - Backend Native PHP + HR-First Employee Onboarding
- **Shipped on:** 2026-03-08
- **Product state:** Real database-backed employee CRUD, authentication, role guards, provisioning, live reports/export, and live dashboards are in place.
- **Audit posture:** Functional definition of done is met; milestone archive is accepted with documentation-only tech debt remaining.

## Current Milestone: v3.0 Beginner-Style PHP Rewrite & Structure Cleanup

**Goal:** Rework the app into a more intentionally beginner-style native PHP CRUD codebase, clean up the folder layout, remove the leave calculator path, and make the landing/admin experience feel less like the earlier demo.

**Target features:**
- Rewrite the main app flow using plain beginner procedural PHP patterns (`mysqli_connect`, `mysqli_query`, inline page logic, shared includes).
- Reorganize folders so the project feels tidier even though the implementation style stays simple and non-reusable.
- Remove the leave calculator flow so employee detail and direct leave data views become the main path.
- Refresh the landing page so it no longer reads like a leftover v1 demo.
- Break the milestone into clearer, distinct implementation phases.

## Requirements

### Validated

- ✓ All `v1.0` requirements met (archive: `.planning/milestones/v1.0-REQUIREMENTS.md`).
- ✓ All `v2.0` requirements met (archive: `.planning/milestones/v2.0-REQUIREMENTS.md`).

### Active

- [ ] Rewrite app pages into beginner-style procedural PHP CRUD structure.
- [ ] Remove calculator-driven leave flow and replace it with direct detail-focused flow.
- [ ] Refresh landing page and shared structure to better match the new milestone direction.

### Out of Scope

- Framework/OOP refactor (Laravel, class-based architecture).
- Open employee self-signup.
- Token/JWT auth migration.
- Advanced reusable architecture or helper abstraction cleanup.
- Workflow expansion like leave requests/approvals in this milestone.

## Context

- The app has completed the transition from v1 demo mode to a real Laragon-hosted native PHP + MySQL backend.
- The leave entitlement engine from v1 remains reused as the business-calculation core, now fed by database-backed employee data.
- The next milestone intentionally moves presentation and code style toward a more beginner-authored PHP CRUD feel while still preserving working database-backed behavior.
- The current open debt is mostly planning/validation artifact cleanup rather than missing user-facing core functionality.

## Constraints

- **Architecture:** Native procedural PHP only (no OOP app architecture, no classes, no frameworks).
- **Database API:** MySQLi procedural via single `koneksi.php` file.
- **Environment:** Laragon localhost (compatible with XAMPP local).
- **Security baseline:** `password_hash()` / `password_verify()`, prepared statements for user input.
- **Business flow:** HR-first onboarding, not employee self-registration.
- **DB setup:** SQL file for manual import - no auto-bootstrap script needed.
- **Employee delete:** Hard delete (permanent removal from DB).
- **Password provisioning:** Auto-generated from employee data (for example NIK + birthdate).

## Milestone History

<details>
<summary>Archived milestone history</summary>

### v2.0 (Shipped: 2026-03-08)

Delivered:
- Real MySQL-backed employee data model, auth/session flow, and HR-first provisioning.
- Full HR employee CRUD plus live calculator, reports/export, and dashboard wiring.
- Runtime verification closure for auth/provisioning and session identity consistency.

Accepted debt:
- Validation/Nyquist documents for some phases still lag the shipped state.
- Dummy topbar placeholders remain in shared UI.

### v1.0 (Shipped: 2026-03-04)

Delivered:
- Frontend-first leave entitlement demo (landing, role login demo, HR/employee dashboard).
- Deterministic 8-year calculator engine reused across HR and employee views.
- Session-backed report save/list/detail flow.
- Excel-compatible export via isolated PhpSpreadsheet usage.

Accepted debt:
- Data layer still mixed demo/session, not DB canonical.
- Login was visual-only (not real auth).
- Demo routes directly reachable.

</details>

## Implementation References (User-Provided)

- https://github.com/suryamsj/tutorial-crud-php-native
- https://github.com/thexdev/php-native-crud
- https://github.com/chapagain/crud-php-simple
- https://www.codepolitan.com/blog/tutorial-membuat-crud-php-dengan-mysql-59897c72d8470/

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Fresh v2.0 plan replacing previous attempt | Previous planning was overengineered; redone with simpler scope | Shipped in v2.0 |
| Single `koneksi.php` with MySQLi | Keeps procedural native PHP consistent | Shipped in v2.0 |
| HR-first provisioning flow | Business need: HR creates employee data before login is active | Shipped in v2.0 |
| Reuse v1 calculator engine as-is | Stable, working engine - just wire to DB data | Shipped in v2.0 |
| Native PHP session for login | Matches project constraints, fits server-rendered app | Shipped in v2.0 |
| SQL file import for DB setup | Simplest approach - manual import via phpMyAdmin or CLI | Shipped in v2.0 |
| Auto-generated password on provisioning | HR doesn't manually set passwords; system generates from employee data | Shipped in v2.0 |
| Hard delete for employees | Permanent removal, no soft-delete complexity | Shipped in v2.0 |
| Basic beginner-style PHP CRUD code | Match user references - popular patterns, nothing fancy | Shipped in v2.0 |
| Keep milestone completion despite documentation-only tech debt | Functional DoD is complete; remaining work is audit hygiene, not missing product behavior | Accepted at v2.0 ship |
| v3.0 focuses on beginner-style rewrite rather than new business workflow expansion | User wants the project to look and feel like early-learning PHP CRUD while keeping the app useful | Active for v3.0 |

---
*Last updated: 2026-03-09 after v3.0 milestone start*
