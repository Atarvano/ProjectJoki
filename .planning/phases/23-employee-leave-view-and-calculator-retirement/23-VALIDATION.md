---
phase: 23
slug: employee-leave-view-and-calculator-retirement
status: draft
nyquist_compliant: true
wave_0_complete: false
created: 2026-03-09
---

# Phase 23 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | Plain PHP smoke scripts on PHP 8.4.10 |
| **Config file** | none — direct CLI `php tests/*.php` style |
| **Quick run command** | `php tests/phase23_employee_leave_retirement_smoke.php --group=navigation` |
| **Full suite command** | `php tests/phase19_auth_session_smoke.php && php tests/phase21_structure_smoke.php && php tests/phase22_hr_detail_first_crud_smoke.php && php tests/phase23_employee_leave_retirement_smoke.php` |
| **Estimated runtime** | ~15 seconds |

---

## Sampling Rate

- **After every task commit:** Run `php tests/phase23_employee_leave_retirement_smoke.php --group=navigation`
- **After every plan wave:** Run `php tests/phase19_auth_session_smoke.php && php tests/phase21_structure_smoke.php && php tests/phase22_hr_detail_first_crud_smoke.php && php tests/phase23_employee_leave_retirement_smoke.php`
- **Before `/gsd-verify-work`:** Full suite must be green
- **Max feedback latency:** 15 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 23-00-01 | 00 | 1 | CRUD-03, LEAV-02 | smoke | `php tests/phase23_employee_leave_retirement_smoke.php --group=employee-self-view` | ✅ after W0 | ⬜ pending |
| 23-00-02 | 00 | 1 | LEAV-03, LEAV-04 | smoke | `php tests/phase23_employee_leave_retirement_smoke.php --group=navigation && php tests/phase23_employee_leave_retirement_smoke.php --group=retirement && php tests/phase23_employee_leave_retirement_smoke.php --group=missing-data` | ✅ after W0 | ⬜ pending |
| 23-01-01 | 01 | 2 | CRUD-03, LEAV-02 | smoke | `php tests/phase23_employee_leave_retirement_smoke.php --group=employee-self-view && php tests/phase23_employee_leave_retirement_smoke.php --group=missing-data` | ✅ after W0 | ⬜ pending |
| 23-02-01 | 02 | 2 | LEAV-03, LEAV-04 | smoke | `php tests/phase23_employee_leave_retirement_smoke.php --group=navigation && php tests/phase23_employee_leave_retirement_smoke.php --group=retirement` | ✅ after W0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [x] `tests/phase23_employee_leave_retirement_smoke.php` — named smoke groups for `employee-self-view`, `navigation`, `retirement`, `missing-data`, and `all`
- [x] Added smoke group for employee missing-data behavior — missing employee row and invalid join-date warning states
- [x] Added smoke assertions for shared layout cleanup in `includes/layout/dashboard-sidebar.php` and `includes/layout/footer.php`
- [x] Added smoke assertions for `hr/kalkulator.php` redirect behavior and absence of rendered calculator UI strings

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Employee page visually makes the years 6-8 leave table the main focal area while keeping profile context minimal | CRUD-03, LEAV-02 | Visual emphasis and copy tone are hard to prove fully with smoke assertions | Login as employee, open `/employee/dashboard.php`, confirm the leave table is the first dominant block, only minimal identity/join info shows above it, and a short HR-contact hint remains visible |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify or Wave 0 dependencies
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 15s
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
