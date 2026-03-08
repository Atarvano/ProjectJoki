# Sicuti HRD Cuti Tracker

## What This Is

Sicuti HRD Cuti Tracker is an internal HR/employee leave entitlement application. It now runs as a real native procedural PHP + MySQLi system where HR owns employee onboarding, provisions login credentials, and both HR and employees work against live database-backed leave data.

## Core Value

HR creates employee data first, provisions login credentials, then employees log in with native PHP sessions to view their own leave data through an enforced, real backend flow.

## Current State

- **Latest shipped milestone:** `v2.0` - Backend Native PHP + HR-First Employee Onboarding
- **Shipped on:** 2026-03-08
- **Product state:** Real database-backed employee CRUD, authentication, role guards, provisioning, live calculator, live reports/export, and live dashboards are in place.
- **Audit posture:** Functional definition of done is met; milestone archive is accepted with documentation-only tech debt remaining.

## Next Milestone Goals

- Define the next milestone from fresh requirements rather than carrying forward the archived v2.0 list.
- Decide whether the next priority is workflow expansion (for example leave requests/approvals), admin usability improvements, or technical debt cleanup.
- Clean up lingering validation-document drift from Phases 15, 16, 17, and 19 if milestone audit hygiene should be restored early.

## Requirements

### Validated

- ✓ All `v1.0` requirements met (archive: `.planning/milestones/v1.0-REQUIREMENTS.md`).
- ✓ All `v2.0` requirements met (archive: `.planning/milestones/v2.0-REQUIREMENTS.md`).

### Active

- None yet - define fresh requirements for the next milestone with `/gsd-new-milestone`.

### Out of Scope

- Framework/OOP refactor (Laravel, class-based architecture).
- Open employee self-signup.
- Token/JWT auth migration.
- Major UI redesign unless a future milestone explicitly targets it.

## Context

- The app has completed the transition from v1 demo mode to a real Laragon-hosted native PHP + MySQL backend.
- The leave entitlement engine from v1 remains reused as the business-calculation core, now fed by database-backed employee data.
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

---
*Last updated: 2026-03-08 after v2.0 milestone completion*
