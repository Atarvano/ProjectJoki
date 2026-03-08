# Project Milestones: Sicuti HRD Cuti Tracker

## v2.0 Backend Native PHP + HR-First Employee Onboarding (Shipped: 2026-03-08)

**Delivered:** The v1 frontend demo was transformed into a real native-PHP HR leave system with MySQL-backed employee records, HR-first account provisioning, real login/session enforcement, and live calculator/report/dashboard data.

**Phases completed:** 7 phases (14-20) across 21 plans and 43 tasks

**Key accomplishments:**
- Established the real database foundation with importable SQL schema, seeded HR admin account, and shared `koneksi.php` connection contract.
- Replaced demo-only auth with real NIK/password login, logout, role guards, persistent identity rendering, and demo-free protected UI.
- Delivered full HR employee management: list, add, edit, detail, and hard delete with linked account cleanup via cascade.
- Implemented HR-first provisioning with generated employee credentials and a one-time flash contract for handoff.
- Rewired calculator, reports, export, and both dashboards from demo/session data to live database queries.
- Closed milestone auth/provisioning runtime verification with session revalidation and approved browser evidence.

**Stats:**
- 65 files changed in milestone implementation range
- 6,781 insertions, 2,277 deletions
- Git range: `ff30a93` -> `fa80e73`
- Timeline: 2026-03-05 to 2026-03-08

**Known debt accepted at completion:**
- Milestone audit remains `tech_debt`, not `passed`, because validation docs for Phases 15/16/17/19 still lag the achieved implementation state.
- Phase 16 and Phase 17 still need Nyquist cleanup to bring validation artifacts in line with the already-shipped behavior.
- `includes/dashboard-topbar.php` still contains inert placeholder search/notification UI noted during Phase 20 verification.

---

## v1.0 milestone (Shipped: 2026-03-04)

**Delivered:** Frontend-first leave entitlement demo with HR calculator, employee parity view, consolidated reporting, and Excel-compatible export.

**Phases completed:** 11 shipped phases (1, 2, 3, 4, 4.1, 5, 6, 7, 8, 12, 13) across 16 plans

**Key accomplishments:**
- Established shared UI foundation (landing, login routing, role dashboards) with consistent design tokens and includes.
- Delivered deterministic 8-year entitlement engine and reused it across HR and employee flows to guarantee parity.
- Implemented report save/list/detail flow using session-backed dummy data and added XLSX export via isolated PhpSpreadsheet usage.
- Closed list/export parity and empty-state handling gaps (`getReports()` consistency, empty export guard, flash feedback).
- Completed EMP traceability closure and shared footer/navigation consistency for milestone audit readiness.

**Stats:**
- 38 files changed in milestone implementation range
- 5,169 insertions, 1,292 deletions
- Git range: `029d953` -> `5eab4ff`
- Timeline: 2026-03-03 to 2026-03-04

**Known debt accepted at completion:**
- Unused seeded report helpers (`initReports()`, `getSeedReports()`) remain in `includes/reports-data.php`.
- Demo-sensitive routes are directly reachable (intentional for v1 demo mode).
- Landing role split clarity remains indirect (`index.php` -> `login.php?role=`).

---
