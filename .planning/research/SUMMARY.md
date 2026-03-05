# Project Research Summary

**Project:** Sicuti HRD Cuti Tracker
**Domain:** Internal HR leave-management web app (procedural PHP backend modernization)
**Researched:** 2026-03-05
**Confidence:** HIGH

## Executive Summary

This is an incremental backend hardening project, not a rewrite. The product is an internal HR/employee leave system where v1 already shipped a working UI and deterministic leave calculator, while v2 must add production-grade persistence, authentication, and authorization using native procedural PHP patterns. Across all research files, the strongest consensus is: keep the current page-controller structure, add a strict DB/repository layer, and enforce HR-first onboarding (employee master first, account activation second).

The recommended approach is a phased migration from session-array demo data to a DB-canonical model (`employees`, `users`, `leave_reports`, `leave_report_rows`) with a compatibility facade during transition. Authentication should use native PHP sessions + `password_hash()`/`password_verify()` with server-side role guards at every protected endpoint. Reporting and export must read the same DB source to preserve parity, especially for the domain-critical year 6/7/8 leave outputs.

The key risks are migration split-brain (session vs DB), non-atomic provisioning, and auth/session hardening gaps. Mitigation is clear: enforce single source-of-truth ownership, require repeatable migrations, wrap provisioning in transactions, centralize auth guard utilities, and run parity fixtures for year 6/7/8 before cutover.

## Key Findings

### Recommended Stack

The stack is intentionally conservative and high-confidence: PHP 8.4.x (verified 8.4.10 locally), MySQL-compatible DB (MySQL 8.4 LTS preferred), MySQLi procedural + mysqlnd, and native PHP sessions. This aligns with project constraints (no framework, no ORM, no JWT) and minimizes regression risk while delivering v2 requirements quickly.

**Core technologies:**
- **PHP 8.4.x:** Runtime for CRUD/auth/session — already active locally; supports modern password/session APIs.
- **MySQL 8.4 LTS (or local compatible server):** Canonical storage — needed for transactions, FK integrity, and consistent reporting.
- **MySQLi procedural + mysqlnd:** DB access layer — fits native procedural architecture and prepared-statement security baseline.
- **Native PHP Session:** Login state + role enforcement — correct for server-rendered internal app.
- **Password API (`password_hash`/`password_verify`):** Credential security baseline — official and requirement-aligned.
- **PhpSpreadsheet 5.5.0 (existing only):** Keep only for export path; no new Composer footprint needed.

**Critical version/runtime notes:**
- Keep hash column flexible (`VARCHAR(255)`) for future algorithm changes.
- Enforce `utf8mb4` + InnoDB defaults across all core tables.
- Laragon runtime currently needs session-hardening ini alignment (`use_strict_mode=1`, `cookie_httponly=1`).

### Expected Features

v2 MVP is dominated by backend table-stakes, not net-new UX: DB foundation, HR employee CRUD, HR-first account provisioning, real login/session/role guards, and DB-backed leave/report parity focused on years 6/7/8. Security baselines (prepared statements, CSRF tokens, session hardening) are mandatory to consider backend “done.”

**Must have (table stakes):**
- DB foundation (`koneksi.php`, schema, repeatable migrations, migration tracking).
- HR employee CRUD with safe deactivation/soft-delete semantics.
- HR-first provisioning flow (`employee` first, then `user` activation).
- Real DB login + native session + role guard + correct logout lifecycle.
- DB-backed leave outputs/reporting/export parity, with year 6/7/8 focus.
- CSRF protection for all state-changing actions.

**Should have (competitive):**
- Explicit account lifecycle states (`not_provisioned` → `active` → `suspended`).
- Integrity guard rails when deactivating users/employees with historical reports.
- Operational flash/error messaging (clear HR troubleshooting states).

**Defer (v2+):**
- Forced first-login password reset flow.
- Richer employee search/filter dimensions.
- Full audit trail, anomaly analytics, SSO/OIDC.

### Architecture Approach

Architecture guidance is consistent and strong: guard-first page controllers, SQL confined to procedural repository modules, and a compatibility facade to migrate old report APIs safely. No framework migration; all integration stays in existing page structure plus new `includes/*-repo.php` files.

**Major components:**
1. **Page controllers (`login.php`, `hr/*.php`, `employee/dashboard.php`)** — validate input, invoke guards/repositories, render/redirect.
2. **Auth/session module (`includes/auth.php`, `logout.php`)** — login verify, session lifecycle, role-based access control.
3. **Data access layer (`koneksi.php`, `employee-repo.php`, `user-repo.php`, `report-repo.php`)** — single SQL boundary with prepared statements and transactions.
4. **Compatibility facade (`includes/reports-data.php`)** — bridge v1 function contracts while moving storage to DB.
5. **MySQL core schema** — canonical source for employees/users/reports and migration state.

### Critical Pitfalls

1. **Split-brain data (session + DB both writable)** — freeze legacy write paths and enforce DB ownership per entity from day one.
2. **Manual schema drift** — require versioned, repeatable migrations with `schema_migrations`; no ad-hoc phpMyAdmin edits.
3. **Non-atomic provisioning** — wrap employee/user provisioning in transactions with rollback and unique constraints.
4. **Delete-policy mismatch (hard delete vs history integrity)** — default to deactivate/soft-delete for employees/users; keep report history immutable.
5. **Auth/session implementation gaps** — centralize guard checks, regenerate session ID on login, and fully invalidate cookie/session on logout.

## Implications for Roadmap

Based on research, suggested phase structure:

### Phase 1: Environment & DB Foundation
**Rationale:** Every v2 feature depends on deterministic DB/runtime behavior.
**Delivers:** `koneksi.php`, canonical schema, repeatable migrations, migration visibility checks, Laragon/XAMPP baseline.
**Addresses:** DB foundation feature set (P1), security preconditions.
**Avoids:** Schema drift, runtime misconfiguration, charset/FK inconsistency.

### Phase 2: Auth Core & Access Boundaries
**Rationale:** Access control must be correct before expanding CRUD/reporting surfaces.
**Delivers:** Real login, logout, session bootstrap/hardening, centralized role guards on protected routes.
**Uses:** Native session + password API + MySQLi user lookup.
**Implements:** Guard-first controller pattern.
**Avoids:** UI-only auth, fixation, partial logout bugs.

### Phase 3: HR Employee Master CRUD
**Rationale:** HR-first business flow requires stable employee master data before provisioning.
**Delivers:** Employee create/list/update/deactivate endpoints + page integration.
**Addresses:** EMPCRUD P1 requirements.
**Implements:** Repository boundary for employee operations.
**Avoids:** Hard-delete history breakage, raw SQL regressions.

### Phase 4: HR-First Provisioning Lifecycle
**Rationale:** Converts employee records into controlled login accounts; core domain differentiator.
**Delivers:** Provisioning page/flow, employee-user linkage, lifecycle states, transactional onboarding.
**Addresses:** FLOW-01..03 + AUTH dependency chain.
**Avoids:** Orphan accounts, duplicate credential states, ambiguous login failures.

### Phase 5: Report Storage Migration & Year 6/7/8 Parity
**Rationale:** Business trust depends on unchanged cuti outcomes while data source changes.
**Delivers:** DB-backed report persistence/read, facade migration, year 6/7/8 focused presentation.
**Addresses:** P1 reporting parity and domain output focus.
**Implements:** Compatibility-facade migration pattern.
**Avoids:** Output regression, split-brain reporting, export mismatch.

### Phase 6: Export/Employee Self-View Parity + Hardening Gate
**Rationale:** Finalize end-to-end consistency across HR list, export, and employee self-view.
**Delivers:** DB-canonical export parity, authenticated employee dashboard mapping, CSRF completion, readiness checks.
**Addresses:** End-to-end launch quality.
**Avoids:** Last-mile consistency/security defects.

### Phase Ordering Rationale

- Dependencies are strict: DB foundation → auth boundary → employee master → provisioning → reporting migration → parity hardening.
- Grouping follows architecture seams (auth module, repos, facade), reducing cross-file churn and regression surface.
- This order directly neutralizes top pitfalls early (schema drift, split-brain, non-atomic onboarding) before user-facing expansion.

### Research Flags

Phases likely needing deeper research during planning:
- **Phase 4 (Provisioning lifecycle):** Needs precise state model + transaction/failure semantics and operator UX messaging.
- **Phase 5 (Report parity migration):** Needs parity fixture design for year 6/7/8 and adapter verification against v1 behavior.
- **Phase 6 (Hardening gate):** Needs environment-specific validation matrix (Laragon/XAMPP, cookie path/domain behavior, session lock edge cases).

Phases with standard patterns (skip research-phase):
- **Phase 1 (DB foundation):** Established MySQLi/migration patterns with high-confidence references.
- **Phase 2 (Auth baseline):** Well-documented PHP session/password primitives.
- **Phase 3 (CRUD baseline):** Standard procedural CRUD with prepared statements and known implementation patterns.

## Confidence Assessment

| Area | Confidence | Notes |
|------|------------|-------|
| Stack | HIGH | Strong alignment with local runtime verification + official PHP/MySQL docs; low ambiguity. |
| Features | MEDIUM | Clear P1 priorities, but some differentiator details (lifecycle UX, error semantics) still need product-level finalization. |
| Architecture | HIGH | Concrete file-level integration plan and migration pattern are explicit and consistent with constraints. |
| Pitfalls | HIGH | Risks are well-mapped to phases with concrete prevention and verification criteria. |

**Overall confidence:** HIGH

### Gaps to Address

- **Provisioning state semantics:** finalize exact status transitions and HR operational rules (suspend/reactivate/reprovision).
- **Delete/deactivate policy detail:** lock entity-level rules (employees/users/reports) in requirements + schema constraints before implementation.
- **Parity test fixtures:** define golden datasets for year 6/7/8 outputs and export matching before report cutover.
- **CSRF scope discipline:** enumerate all state-changing endpoints (including hidden form actions) to avoid partial coverage.
- **Environment consistency:** standardize one base URL per environment and align Apache vs CLI `php.ini` settings.

## Sources

### Primary (HIGH confidence)
- Internal planning docs: `.planning/PROJECT.md`, `.planning/REQUIREMENTS.md`, current code structure references.
- `.planning/research/STACK.md`
- `.planning/research/ARCHITECTURE.md`
- `.planning/research/PITFALLS.md`
- `.planning/research/LARAGON.md`
- PHP official docs (sessions, password APIs, MySQLi prepared statements/transactions).
- MySQL official docs (release model, implicit commit behavior).

### Secondary (MEDIUM confidence)
- `.planning/research/FEATURES.md` (includes official references plus interpreted prioritization).
- OWASP CSRF Prevention Cheat Sheet.

### Tertiary (LOW confidence)
- Tutorial/reference repos and blog patterns cited in research docs (useful directional examples, not normative standards).

---
*Research completed: 2026-03-05*
*Ready for roadmap: yes*
