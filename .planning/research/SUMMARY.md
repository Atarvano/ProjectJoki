# Project Research Summary

**Project:** Sicuti HRD Cuti Tracker
**Domain:** Internal HR leave tracker evolving from demo prototype to procedural PHP backend-backed HR app
**Researched:** 2026-03-04
**Confidence:** HIGH

## Executive Summary

This product is no longer just a leave-calculation demo; it is becoming a small internal HR system with a strict HR-first onboarding model. The research is unusually aligned on the core product shape: keep the app server-rendered, procedural, and page-based; introduce a minimal MySQL-backed persistence layer; and replace the current visual login with real session authentication tied to employee records. Experts would not rebuild this as a framework or SPA at this stage—they would harden the existing procedural app by adding one connection bootstrap, a few repository-style include files, and role/session guards around the current pages.

The recommended approach is to treat v2.0 as a backend-foundation milestone, not a product expansion milestone. Build the database baseline first, then auth/session primitives, then HR-managed employee CRUD, then migrate existing report/session behavior onto the database, and only then wire the employee self-view and export paths to the new data model. This ordering matches both the product dependency chain and the architecture research: employee login only makes sense after HR creates canonical employee records, and DB-backed reports must become the single source of truth before dashboards and export can be trusted.

The main risks are migration risks, not feature novelty. The biggest failure modes are split-brain data between legacy session arrays and MySQL, weak request/session lifecycle handling in old procedural pages, authorization gaps in CRUD actions, and incomplete onboarding writes that leave orphan user/employee records. Mitigation is clear: establish one canonical repository seam early, centralize bootstrap/session handling, enforce role guards on every protected endpoint, use prepared statements and transactions consistently, and validate parity as legacy session-based paths are retired.

## Key Findings

### Recommended Stack

The stack recommendation is conservative by design: stay on PHP 8.4.x in Laragon, use MySQL 8.0.x with InnoDB and `utf8mb4`, access the database through procedural MySQLi, and keep authentication entirely native with PHP sessions plus `password_hash()` / `password_verify()`. This is the smallest possible change that delivers real persistence and login while respecting the project constraint to remain native procedural PHP.

The architecture and feature research reinforce the same principle: do not introduce framework migration, ORM abstraction, token auth, or frontend rewrites. Reuse the existing Bootstrap UI and isolated Composer/PhpSpreadsheet usage only for export. The only notable research conflict is that PITFALLS.md discusses PDO-oriented hardening examples, while STACK.md clearly recommends procedural MySQLi. For planning purposes, treat the underlying concern as the real requirement: one canonical DB API, strict error mode, prepared statements, transactions, and charset consistency. The implementation recommendation remains MySQLi.

**Core technologies:**
- **PHP 8.4.x**: app runtime — matches the current Laragon environment and avoids migration cost.
- **MySQL 8.0.x**: primary relational storage — supports transactions, foreign keys, InnoDB, and `utf8mb4`.
- **Procedural MySQLi**: DB access layer — best fit for a procedural PHP codebase and supports prepared statements cleanly.
- **Native PHP sessions**: login state and route protection — correct model for a local/internal server-rendered app.
- **`password_hash()` / `password_verify()`**: credential handling — official, maintained password APIs with no custom crypto risk.
- **PhpSpreadsheet ^5.5**: export only — keep existing Composer usage isolated to XLSX export.

### Expected Features

The feature research is tightly focused on HR-controlled onboarding rather than self-service account creation. The MVP is not “employee registration”; it is “HR creates the employee record, optionally enables linked credentials, and then the employee can log in to the existing self-view.” That makes employee master data, credential linkage, login persistence, status controls, and role guards the true table stakes.

The most valuable non-MVP additions are operational improvements around onboarding readiness and auditability, but they should not delay v2.0. Invite-token activation is useful but higher complexity than this milestone needs. SSO, broad onboarding workflows, and open self-signup are explicitly poor fits for the current scope.

**Must have (table stakes):**
- **HR-created employee master record** — canonical source of truth before any employee access exists.
- **Linked credential setup/enabling** — login lifecycle must be tied to employee status, not standalone accounts.
- **Real session-backed login/logout with role guards** — replaces demo-only auth and protects HR/employee pages.
- **Secure password and session baseline** — hashing, session regeneration, cookie hardening, generic login failures.
- **Active/inactive account controls** — HR must be able to disable access immediately.
- **Employee landing into existing self-view** — v2 should unlock value from v1, not recreate it.

**Should have (competitive):**
- **Onboarding readiness checklist/status** — lightweight operational visibility for HR.
- **Minimal auth/onboarding audit trail** — useful for governance and troubleshooting.
- **Simple HR onboarding metrics** — pending activation and first-login completion visibility.

**Defer (v2+ / v3+):**
- **Invite-token first-use activation** — strong improvement, but adds token lifecycle complexity.
- **SSO/OIDC** — premature for a local/internal procedural app foundation.
- **Expanded onboarding suite** — documents, provisioning, LMS, and similar modules are scope creep now.
- **Self-signup/open registration** — directly conflicts with HR-first control of employee identity.

### Architecture Approach

The architecture recommendation is to preserve the existing page-controller pattern and add a thin procedural backend seam beneath it. The new design centers on `includes/koneksi.php` for the DB connection, `includes/auth.php` for session bootstrap and route guards, repository-style include files for employee/report data access, and a compatibility façade around existing report functions so pages can migrate incrementally without a risky rewrite.

**Major components:**
1. **Page controllers (`login.php`, `hr/*.php`, `employee/dashboard.php`)** — accept request input, run guards, call repo/calculator functions, then render or redirect.
2. **`includes/koneksi.php`** — single DB connection bootstrap with consistent charset and error behavior.
3. **`includes/auth.php`** — session startup, login attempt, logout, and `requireRole()`/`requireSession()` guards.
4. **`includes/employee-repo.php`** — employee CRUD, user lookup, and employee-to-user linkage queries.
5. **`includes/report-repo.php`** — DB-backed save/list/detail/report-count functions for leave reports.
6. **`includes/reports-data.php` compatibility shim** — preserves existing callers while moving storage internals to MySQL.
7. **`hr/employees.php` + `logout.php`** — new entry points for HR-first onboarding and session teardown.

### Critical Pitfalls

The pitfall research shows that the hardest part of this milestone is safely migrating a demo-first codebase into a real authenticated one. The highest-risk mistakes are predictable and avoidable if the roadmap sequences the work correctly.

1. **Split-brain data between session/demo arrays and MySQL** — avoid by establishing one canonical repository API early, using a deliberate migration mode, and cutting old reads once parity is verified.
2. **Fragile bootstrap/session lifecycle** — avoid by centralizing include order, session start, and guard execution before any output; use `require_once` and `__DIR__`-based paths consistently.
3. **Authorization gaps / IDOR in CRUD** — avoid by enforcing server-side role checks on every HR action and scoping employee access strictly to self-only data.
4. **Partial onboarding writes / orphan records** — avoid by wrapping employee+credential creation in transactions and enforcing foreign keys and unique constraints.
5. **Weak auth hardening** — avoid by hashing passwords, regenerating session IDs after login, configuring secure cookie/session settings, and adding generic errors plus throttling.

## Implications for Roadmap

Based on research, suggested phase structure:

### Phase 1: Database Foundation and Bootstrap
**Rationale:** Everything else depends on a trustworthy persistence and bootstrap layer. This is where the project either stays coherent or becomes split-brain.
**Delivers:** Schema baseline (`users`, `employees`, report tables), `includes/koneksi.php`, DB configuration conventions, seed data strategy, and migration/cutover rules for legacy session data.
**Addresses:** HR-first source-of-truth requirement, credential linkage preconditions, report persistence prerequisites.
**Avoids:** Split-brain data, fragile bootstrap, charset drift, inconsistent DB APIs.

### Phase 2: Authentication Core and Route Protection
**Rationale:** Once the DB exists, the next hard dependency is real identity. Route protection must exist before CRUD and page integrations are trusted.
**Delivers:** `includes/auth.php`, `login.php` POST flow, `logout.php`, session hardening, role-aware redirects, shared guard pattern for protected pages.
**Uses:** Native PHP sessions, password APIs, prepared statements, role fields in `users`.
**Implements:** Guard-then-work page entry pattern.
**Avoids:** Output-before-header bugs, plaintext/reversible password handling, session fixation, URL-based role bypass.

### Phase 3: HR Employee Onboarding and Credential Lifecycle
**Rationale:** The product’s defining business rule is HR-first onboarding. This should be explicit and complete before employee self-service is integrated.
**Delivers:** `hr/employees.php`, employee CRUD, linked credential enable/disable flow, active/inactive state handling, transactional employee+user creation, self-consistent employee directory.
**Addresses:** Table-stakes HR master record, linked credential setup, account state controls.
**Avoids:** IDOR in CRUD, orphan employee/user records, premature employee access.

### Phase 4: Report Persistence Migration and HR Module Integration
**Rationale:** Existing HR pages already provide value; they should be rewired onto the database once identity and employee records are stable.
**Delivers:** `report-repo.php`, `reports-data.php` compatibility shim, DB-backed `hr/dashboard.php`, `hr/kalkulator.php`, and `hr/laporan.php`, consistent report counts and storage.
**Addresses:** HR workflow continuity and report parity with the new backend.
**Uses:** Repository boundary + compatibility façade migration pattern.
**Avoids:** Dashboard/export/report count mismatches, mixed session/DB data paths, SQL-in-page sprawl.

### Phase 5: Employee Self-View Integration and Export Parity
**Rationale:** Employee-facing value should come after HR identity and report data are canonical. Export should be last because it must read the exact same persisted dataset as the UI.
**Delivers:** `employee/dashboard.php` driven by authenticated employee identity, removal of preset demo identity selection, DB-backed `hr/export.php` with parity against report listings.
**Addresses:** First-login handoff to existing self-view, stable report export behavior.
**Avoids:** Self-view identity mismatches, export parity failures, stale session-derived data.

### Phase Ordering Rationale

- **The dependency chain is explicit:** DB connection -> auth/session -> HR employee CRUD -> report persistence -> HR page integration -> employee page integration -> export parity.
- **The grouping matches the architecture seams:** bootstrap/auth/repositories first, page rewiring second.
- **This order minimizes migration risk:** it removes dummy/session data incrementally instead of mixing old and new sources indefinitely.
- **It also matches product reality:** employees cannot log in safely until HR-managed records and account states exist.

### Research Flags

Phases likely needing deeper research during planning:
- **Phase 3:** credential issuance UX and transaction boundaries need precise implementation choices, especially if password setup is created by HR initially.
- **Phase 4:** report-table design and migration from current session-backed report structures may need a focused reconciliation plan.
- **Future invite-token activation phase:** definitely needs dedicated research because token lifecycle, expiry, and recovery design are not yet settled.

Phases with standard patterns (skip research-phase):
- **Phase 1:** DB connection/bootstrap, schema basics, and charset/index conventions are well-documented and already strongly researched.
- **Phase 2:** native PHP session auth with password hashing and role guards follows established patterns.
- **Phase 5:** export parity and employee self-view wiring are mostly integration work on top of already-known patterns.

## Confidence Assessment

| Area | Confidence | Notes |
|------|------------|-------|
| Stack | HIGH | Based primarily on official PHP/MySQL/Laragon docs and tightly aligned with existing project constraints. |
| Features | MEDIUM | Strong product logic and credible sources, but competitor/process guidance is less authoritative than technical docs. |
| Architecture | HIGH | Derived from direct reading of the current codebase plus standard procedural PHP integration patterns. |
| Pitfalls | MEDIUM | Risks are credible and useful, but one document uses PDO examples that conflict with the recommended MySQLi implementation. |

**Overall confidence:** HIGH

### Gaps to Address

- **DB API inconsistency in research:** PITFALLS.md frames some guidance through PDO while STACK.md recommends MySQLi. Planning should normalize this to one rule set: MySQLi only, with strict errors, prepared statements, transactions, and charset discipline.
- **Credential issuance workflow:** research supports HR-linked credentials, but the exact v2.0 UX for initial password creation/reset still needs a planning decision.
- **Migration/cutover mechanics:** the codebase currently uses session-backed report/demo flows; planning should specify whether rollout is direct cutover or temporary dual-read parity validation.
- **Secrets/config handling:** stack research focuses local Laragon defaults, but implementation planning should define how credentials are kept out of committed runtime config.
- **Security depth beyond baseline:** CSRF protection, brute-force throttling details, and audit logging are identified as important, but need concrete acceptance criteria in phase planning.

## Sources

### Primary (HIGH confidence)
- PHP Manual — MySQLi, prepared statements, sessions, password APIs — stack, auth, and query conventions
- MySQL Reference Manual — InnoDB and `utf8mb4` — schema/storage baseline
- Laragon documentation — local runtime/tooling compatibility
- Existing project files (`.planning/PROJECT.md`, current PHP pages/includes) — architecture fit and migration constraints

### Secondary (MEDIUM confidence)
- OrangeHRM Help Center — employee-first onboarding flow with optional login linkage
- ADP onboarding guidance (updated 2026-02-20) — process framing for HR-controlled onboarding readiness
- OWASP Authentication / Session Management / SQL Injection cheat sheets — security hardening and abuse-control expectations
- phpMyAdmin requirements/news — local tooling compatibility notes for PHP 8.4+

### Tertiary (LOW confidence)
- None material. The main uncertainty is not source scarcity but implementation choice reconciliation around DB API and migration rollout details.

---
*Research completed: 2026-03-04*
*Ready for roadmap: yes*
