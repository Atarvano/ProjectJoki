# Roadmap: Sicuti HRD Cuti Tracker (v2.0)

**Milestone:** v2.0 — Backend Native PHP + HR-First Employee Onboarding
**Phases:** 7 (Phase 14–20, continuing from v1.0 which ended at Phase 13)
**Requirements:** 36 mapped across 7 phases
**Created:** 2026-03-05

## Implementation Guardrails

These constraints apply to ALL phases:

- **Architecture:** Native procedural PHP only — no OOP, no classes, no frameworks
- **Database API:** MySQLi procedural via single `koneksi.php` (variable: `$koneksi`)
- **Auth:** Native PHP sessions (`session_start`, `$_SESSION`, `session_destroy`)
- **DB Setup:** SQL file for manual import (phpMyAdmin/CLI) — no auto-bootstrap
- **Security:** `password_hash()`/`password_verify()`, prepared statements for all user input
- **Flow:** HR creates employee → HR provisions account → Employee logs in
- **Style:** Basic beginner-level PHP CRUD code (reference repo patterns)

## Phases

- [x] **Phase 14: Database Foundation** — SQL schema, connection file, tables, and HR admin seed (completed 2026-03-05)
- [x] **Phase 15: Authentication & Access Control** — Real login/logout, role guards on all pages, auth-aware UI (completed 2026-03-05)
- [x] **Phase 16: Employee CRUD & HR Navigation** — Full employee management with list/add/edit/delete and sidebar nav (completed 2026-03-05)
- [x] **Phase 17: Account Provisioning** — HR creates login accounts for employees with auto-generated passwords (completed 2026-03-05)
- [x] **Phase 18: Data Wiring — Calculator, Reports & Dashboards** — All pages swap from demo/session data to live DB data (completed 2026-03-06)
- [ ] **Phase 19: Auth Session Revalidation & Identity Consistency** — Revalidate live session state after employee delete and normalize persistent identity display
- [ ] **Phase 20: Provisioning E2E Verification & Flash Contract Alignment** — Close milestone runtime verification gaps for auth/provisioning and align credential flash contract

## Phase Details

### Phase 14: Database Foundation
**Goal:** Application has a working MySQL database with connection, schema, and seeded HR admin account
**Depends on:** Nothing (foundation for all subsequent phases)
**Requirements:** DATA-01, DATA-02, DATA-03, DATA-04, DATA-05, DATA-06
**Success Criteria** (what must be TRUE):
  1. Any PHP file can `require_once 'koneksi.php'` and use `$koneksi` to execute a query against the MySQL database
  2. SQL file imports cleanly via phpMyAdmin/CLI, creating the database, `karyawan` table (12 fields, NIK unique), and `users` table (FK cascade to karyawan)
  3. Seeded HR admin account exists in `users` table with hashed password and role='hr' after import
  4. A sample prepared statement query (`mysqli_prepare` + `mysqli_stmt_bind_param`) works end-to-end, establishing the security pattern for all subsequent phases
**Plans:** 2/2 plans complete
Plans:
- [ ] 14-01-PLAN.md — SQL schema, connection file, seed data & prepared statement pattern

### Phase 15: Authentication & Access Control
**Goal:** Users can securely log in and out, with role-based page protection and real identity in the UI
**Depends on:** Phase 14 (needs DB + users table + HR admin seed)
**Requirements:** AUTH-01, AUTH-02, AUTH-03, RBAC-01, RBAC-02, RBAC-03, RBAC-04, RBAC-05, DASH-02, DASH-04
**Success Criteria** (what must be TRUE):
  1. User can log in at login.php with NIK + password (POST form), validated against DB with `password_verify()`, and is redirected to the correct dashboard by role
  2. User can log out from any page via "Keluar" button — session is destroyed and user is redirected to login page
  3. All HR pages reject non-HR users; all employee pages reject non-employees; unauthenticated users redirect to login (single `auth-guard.php` with `cekLogin()` + `cekRole()`)
  4. Topbar displays the logged-in user's real name and role from session/DB data
  5. All "Demo v1" badges, "Akses demo" labels, and demo notice text are removed from the UI
**Plans:** 3/3 plans complete
Plans:
- [ ] 15-01-PLAN.md — Core auth infrastructure: auth-guard.php, logout.php, and login.php rewrite with real POST authentication
- [ ] 15-02-PLAN.md — Guard all pages, session-aware dashboards, demo cleanup, logout button in sidebar

### Phase 16: Employee CRUD & HR Navigation
**Goal:** HR can fully manage employee records through a complete CRUD interface
**Depends on:** Phase 15 (CRUD pages need auth guards; HR role required)
**Requirements:** CRUD-01, CRUD-02, CRUD-03, CRUD-04, CRUD-05, DASH-03
**Success Criteria** (what must be TRUE):
  1. HR can add a new employee via 9-field form with server-side validation (required fields, NIK uniqueness, email format)
  2. HR can view all employees from the database in an HTML table on the employee list page
  3. HR can edit any employee's data via a pre-filled form and save updates to the database
  4. HR can permanently delete an employee (hard delete) and the associated user account is automatically removed via CASCADE
  5. HR sidebar includes a "Kelola Karyawan" navigation link that leads to the employee management pages
**Plans:** 3/3 plans complete
Plans:
- [ ] 16-01-PLAN.md — Sidebar `Kelola Karyawan` nav + employee list page (table, empty state, actions)
- [ ] 16-02-PLAN.md — Separate add/edit pages with server validation, prefill, and PRG flash flow
- [ ] 16-03-PLAN.md — Employee detail page + POST hard-delete endpoint with cascade-aware messaging

### Phase 17: Account Provisioning
**Goal:** HR can provision login accounts for employees, completing the HR-first onboarding flow
**Depends on:** Phase 16 (needs employees to exist in karyawan table)
**Requirements:** PROV-01, PROV-02, PROV-03, PROV-04
**Success Criteria** (what must be TRUE):
  1. HR can click "Buat Akun Login" for any existing employee who doesn't yet have an account
  2. System auto-generates password from employee data (NIK + tanggal_lahir), hashes it with `password_hash()`, and creates a `users` record linked via `karyawan_id` FK with role='employee'
  3. After provisioning, the generated credentials (NIK as username + plaintext password) are displayed exactly once via flash message
  4. A provisioned employee can successfully log in with their NIK and auto-generated password (end-to-end flow validated)
**Plans:** 2/2 plans complete
Plans:
- [ ] 17-01-PLAN.md — Provisioning actions on list/detail + POST provisioning endpoint with password generation and users insert
- [ ] 17-02-PLAN.md — One-time credential flash UX + Wave 0 manual validation checklist

### Phase 18: Data Wiring — Calculator, Reports & Dashboards
**Goal:** All application pages display real DB data instead of demo/session data, completing the v1→v2 transformation
**Depends on:** Phase 17 (needs provisioned employee accounts for employee dashboard testing; needs real karyawan data for calculator/reports)
**Requirements:** CALC-01, CALC-02, CALC-03, CALC-04, RPT-01, RPT-02, RPT-03, RPT-04, DASH-01, DASH-05
**Success Criteria** (what must be TRUE):
  1. HR calculator page offers an employee dropdown populated from the karyawan DB table, and `hitungHakCuti()` receives real join-date data (no engine changes)
  2. Employee dashboard auto-loads own leave calculation from DB based on session user — no manual input, no preset selector
  3. Reports page computes leave data live from karyawan table + `hitungHakCuti()` (no session storage), and Excel export produces a valid XLSX from DB query results
  4. HR dashboard shows real employee count and statistics from database queries
  5. All hardcoded `preset_karyawan` arrays, "Preset Demo" badges, sample labels, and session reset buttons are removed from the codebase
**Plans:** 4/4 plans complete
Plans:
- [x] 18-01-PLAN.md — Wave 0 smoke tests, manual checks, and validation contract for Phase 18 (completed 2026-03-06)
- [x] 18-02-PLAN.md — Employee-first HR calculator wiring from DB + entry link from employee detail (completed 2026-03-06)
- [x] 18-03-PLAN.md — Live reports and Excel export from DB rows + removal of session report helper (completed 2026-03-06)
- [x] 18-04-PLAN.md — Real HR dashboard stats + focused employee dashboard year 6/7/8 view (completed 2026-03-06)

### Phase 19: Auth Session Revalidation & Identity Consistency
**Goal:** Authentication state stays consistent with live database records, especially after employee deletion, and logged-in identity labels come from persistent user data
**Depends on:** Phase 18 (closes cross-phase audit gaps across auth, CRUD, provisioning, and employee dashboard flows)
**Requirements:** CRUD-04, RBAC-03, RBAC-04, DASH-02
**Gap Closure:** Closes critical audit gaps from `.planning/v2.0-v2.0-MILESTONE-AUDIT.md`
**Success Criteria** (what must be TRUE):
  1. `cekLogin()` and `cekRole()` revalidate the active session against live `users` data and reject deleted or inactive accounts
  2. If HR deletes an employee whose account is currently logged in, the next employee request is forced through a valid auth redirect instead of showing stale-session data or fallback errors
  3. HR identity shown in topbar/session context is sourced from persistent user data rather than a hardcoded fallback label
  4. Delete -> auth -> employee dashboard flow has explicit runtime verification coverage and evidence
**Plans:** 2/4 plans executed
Plans:
- [ ] 19-01-PLAN.md — Shared auth guard revalidation plus a focused stale-session smoke test
- [ ] 19-02-PLAN.md — Persistent login identity hydration and a short Phase 19 validation checklist
- [ ] 19-03-PLAN.md — Browser verification for delete redirect and identity consistency

### Phase 20: Provisioning E2E Verification & Flash Contract Alignment
**Goal:** Milestone auth and provisioning flows have closed runtime evidence, and provisioning credential flash output matches the documented structured contract
**Depends on:** Phase 19 (requires auth/session fix before final E2E verification)
**Requirements:** AUTH-01, AUTH-02, AUTH-03, RBAC-01, RBAC-02, RBAC-05, PROV-01, PROV-02, PROV-03, PROV-04, DASH-04
**Gap Closure:** Closes remaining audit partial requirements and verification debt from `.planning/v2.0-v2.0-MILESTONE-AUDIT.md`
**Success Criteria** (what must be TRUE):
  1. Provisioning endpoint and karyawan list use the same structured flash contract for generated credentials
  2. Browser walkthrough proves HR login, logout, role guards, demo-free UI, provisioning, employee login, and employee dashboard access all work end-to-end
  3. Phase 15 and Phase 17 verification artifacts can be updated from `human_needed` to closed milestone-ready evidence
  4. Milestone audit blockers tied to auth/provisioning runtime validation are removed
**Plans:** 0/0 plans complete

## Requirement Coverage

| Requirement | Phase | Category |
|-------------|-------|----------|
| DATA-01 | Phase 14 | Database Foundation |
| DATA-02 | Phase 14 | Database Foundation |
| DATA-03 | Phase 14 | Database Foundation |
| DATA-04 | Phase 14 | Database Foundation |
| DATA-05 | Phase 14 | Database Foundation |
| DATA-06 | Phase 14 | Database Foundation |
| AUTH-01 | Phase 20 | Authentication |
| AUTH-02 | Phase 20 | Authentication |
| AUTH-03 | Phase 20 | Authentication |
| RBAC-01 | Phase 20 | Access Control |
| RBAC-02 | Phase 20 | Access Control |
| RBAC-03 | Phase 19 | Access Control |
| RBAC-04 | Phase 19 | Access Control |
| RBAC-05 | Phase 20 | Access Control |
| DASH-02 | Phase 19 | Dashboard (auth UI) |
| DASH-04 | Phase 20 | Dashboard (auth UI) |
| CRUD-01 | Phase 16 | Employee CRUD |
| CRUD-02 | Phase 16 | Employee CRUD |
| CRUD-03 | Phase 16 | Employee CRUD |
| CRUD-04 | Phase 19 | Employee CRUD |
| CRUD-05 | Phase 16 | Employee CRUD |
| DASH-03 | Phase 16 | Dashboard (CRUD nav) |
| PROV-01 | Phase 20 | Provisioning |
| PROV-02 | Phase 20 | Provisioning |
| PROV-03 | Phase 20 | Provisioning |
| PROV-04 | Phase 20 | Provisioning |
| CALC-01 | Phase 18 | Calculator |
| CALC-02 | Phase 18 | Calculator |
| CALC-03 | Phase 18 | Calculator |
| CALC-04 | Phase 18 | Calculator |
| RPT-01 | Phase 18 | Reports |
| RPT-02 | Phase 18 | Reports |
| RPT-03 | Phase 18 | Reports |
| RPT-04 | Phase 18 | Reports |
| DASH-01 | Phase 18 | Dashboard (stats) |
| DASH-05 | Phase 18 | Dashboard (cleanup) |

**Mapped: 36/36 ✓ — No orphans, no duplicates**

## Dependency Graph

```
Phase 14 (DB Foundation)
    └── Phase 15 (Auth & Access Control)
            └── Phase 16 (Employee CRUD)
                    └── Phase 17 (Account Provisioning)
                            └── Phase 18 (Data Wiring)
                                    └── Phase 19 (Auth Session Revalidation)
                                            └── Phase 20 (Provisioning E2E Closure)
```

All phases are sequential. Each phase depends on its predecessor. No parallelization within the critical path (though Phase 18's internal work items — calculator, reports, dashboards — are independent of each other).

## Progress

| Phase | Plans Complete | Status | Completed |
|-------|---------------|--------|-----------|
| 14. Database Foundation | 2/2 | Complete   | 2026-03-05 |
| 15. Authentication & Access Control | 3/3 | Complete    | 2026-03-05 |
| 16. Employee CRUD & HR Navigation | 3/3 | Complete    | 2026-03-05 |
| 17. Account Provisioning | 2/2 | Complete    | 2026-03-05 |
| 18. Data Wiring — Calculator, Reports & Dashboards | 4/4 | Complete    | 2026-03-06 |
| 19. Auth Session Revalidation & Identity Consistency | 2/4 | In Progress|  |
| 20. Provisioning E2E Verification & Flash Contract Alignment | 0/0 | Planned     | - |

---
*Roadmap created: 2026-03-05*
*Milestone: v2.0 — Backend Native PHP + HR-First Employee Onboarding*
