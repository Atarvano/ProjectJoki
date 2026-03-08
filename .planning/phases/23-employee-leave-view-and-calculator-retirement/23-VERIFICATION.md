---
phase: 23-employee-leave-view-and-calculator-retirement
verified: 2026-03-09T00:00:00Z
status: passed
score: 4/4 must-haves verified
human_verification:
  - test: "Employee self-view visual emphasis"
    expected: "After employee login, /employee/dashboard.php shows the years 6-8 leave table as the first dominant content block, with only minimal identity/join-date context above it and a short HR-contact hint still visible."
    why_human: "Visual hierarchy, copy emphasis, and perceived prominence cannot be fully verified from static file inspection and smoke tests alone."
    result: "Approved by user on 2026-03-09 after visual verification."
---

# Phase 23: Employee Leave View and Calculator Retirement Verification Report

**Phase Goal:** Employees can view their own leave entitlement directly from their authenticated area, and the calculator flow is fully removed once the replacement path exists.
**Verified:** 2026-03-09T00:00:00Z
**Status:** passed
**Re-verification:** No — initial verification

## Goal Achievement

### Observable Truths

| # | Truth | Status | Evidence |
| --- | --- | --- | --- |
| 1 | An authenticated employee can open their own page and see leave entitlement details tied to their session-linked account without entering a separate calculator flow. | ✓ VERIFIED | `employee/dashboard.php` stays guard-first and wires to `employee/logic/dashboard.php`; logic loads `karyawan` by session `karyawan_id`, reuses `hitungHakCuti()`, filters years 6-8, and `employee/views/dashboard.php` renders the leave table. `php tests/phase23_employee_leave_retirement_smoke.php` PASS. |
| 2 | HR and employee users no longer see the calculator in main navigation or as the primary route for checking leave information. | ✓ VERIFIED | `includes/layout/dashboard-sidebar.php` removes HR calculator nav and links employee `Hak Cuti Saya` to `/employee/dashboard.php`; `includes/layout/footer.php` keeps only live destinations; `hr/dashboard.php` points guidance to `reports.php` and `employee-detail.php`. Navigation smoke PASS. |
| 3 | The standalone calculator page is removed only after the direct detail/self-view replacement path is available and working. | ✓ VERIFIED | Replacement path exists in employee self-view files before retirement; `hr/kalkulator.php` is now a 6-line authenticated redirect bridge to `/hr/reports.php` with `exit;`. Retirement smoke and Phase 21 bridge checks PASS. |
| 4 | Users follow a detail-first leave journey end-to-end without encountering dead calculator links or fallback demo-style routes. | ✓ VERIFIED | `hr/views/reports.php` links rows to `employee-detail.php`; `hr/views/employee-detail.php` shows years 6-8 leave review; shared sidebar/footer/dashboard no longer surface calculator-first links; `hr/kalkulator.php` silently redirects instead of rendering legacy UI. |

**Score:** 4/4 truths verified

### Required Artifacts

| Artifact | Expected | Status | Details |
| --- | --- | --- | --- |
| `tests/phase23_employee_leave_retirement_smoke.php` | Named smoke groups for self-view, navigation, retirement, and missing-data states | ✓ VERIFIED | Exists, substantive at 177 lines, and executable; full suite PASS. |
| `.planning/phases/23-employee-leave-view-and-calculator-retirement/23-VALIDATION.md` | Validation map aligned to final smoke groups | ✓ VERIFIED | Exists and references `employee-self-view`, `navigation`, `retirement`, and `missing-data` commands exactly. |
| `includes/auth/auth-guard.php` | Employee guard allows narrow self-view warning state without weakening role/inactive checks | ✓ VERIFIED | Exists, substantive at 229 lines, and contains explicit self-view warning-state branch for missing employee row. |
| `employee/logic/dashboard.php` | Session-linked employee lookup, years 6-8 leave preparation, warning-state data | ✓ VERIFIED | Exists, substantive at 114 lines, queries employee row with prepared statement, reuses `hitungHakCuti()`, and prepares warning/view data. |
| `employee/views/dashboard.php` | Minimal identity block plus dominant leave table and HR-contact hint | ✓ VERIFIED | Exists, substantive at 92 lines, renders inline warning state, focused table, and support hint. |
| `includes/layout/dashboard-sidebar.php` | Shared nav without calculator-first path and with clickable employee self-view link | ✓ VERIFIED | Exists, substantive at 87 lines, HR nav omits calculator and employee nav links to `/employee/dashboard.php`. |
| `includes/layout/footer.php` | Shared footer links trimmed to live replacement destinations | ✓ VERIFIED | Exists, substantive at 77 lines, includes reports and employee dashboard links only. |
| `hr/dashboard.php` | Reports-first HR guidance | ✓ VERIFIED | Exists, substantive at 234 lines, helper cards and hero copy push `reports.php -> employee-detail.php`. |
| `hr/kalkulator.php` | Redirect bridge to `/hr/reports.php` | ✓ VERIFIED | Exists, concise but intentionally substantive for its role; contains auth guard, redirect header, and `exit;`, with legacy UI/query code removed. |

### Key Link Verification

| From | To | Via | Status | Details |
| --- | --- | --- | --- | --- |
| `employee/dashboard.php` | `employee/logic/dashboard.php` | `require_once` route-to-logic include | ✓ WIRED | Route includes logic directly on line 6. |
| `employee/logic/dashboard.php` | `includes/cuti-calculator.php` | Shared leave engine reuse | ✓ WIRED | Logic requires calculator file on line 2 and calls `hitungHakCuti()` on line 72. |
| `includes/auth/auth-guard.php` | `employee/dashboard.php` | Employee role check that preserves self-view warning state | ✓ WIRED | `cekRole('employee')` allows missing-row sessions through the documented narrow branch so the page can render warnings. |
| `includes/layout/dashboard-sidebar.php` | `/employee/dashboard.php` | `Hak Cuti Saya` nav href | ✓ WIRED | Employee nav item points to `/employee/dashboard.php` in both sidebar renders. |
| `includes/layout/footer.php` | `/hr/reports.php` and `/employee/dashboard.php` | Shared footer links trimmed to live destinations only | ✓ WIRED | Dashboard footer and marketing footer both link to reports and employee dashboard; calculator link absent. |
| `hr/kalkulator.php` | `/hr/reports.php` | Header redirect plus exit | ✓ WIRED | `header('Location: /hr/reports.php');` followed by `exit;`. |
| `hr/dashboard.php` | `hr/views/reports.php` / `employee-detail.php` path | Copy and CTA alignment toward reports-first review | ✓ WIRED | Hero copy and helper cards explicitly direct HR to `reports.php`, then detail review. |
| `hr/views/reports.php` | `employee-detail.php` | Detail-first review CTA | ✓ WIRED | Each report row links to `employee-detail.php?id=...&from=reports`. |

### Requirements Coverage

| Requirement | Source Plan | Description | Status | Evidence |
| --- | --- | --- | --- | --- |
| CRUD-03 | 23-00, 23-01 | Employee can open their own page and see direct leave entitlement details from their session-linked account. | ✓ SATISFIED | `employee/dashboard.php` -> `employee/logic/dashboard.php` -> `employee/views/dashboard.php`; session-linked query and years 6-8 leave table; Phase 23 smoke PASS. |
| LEAV-02 | 23-00, 23-01 | Employee can view their leave entitlement directly from their own authenticated page without using a standalone calculator page. | ✓ SATISFIED | Employee dashboard is the direct destination, logic does not reference `/hr/kalkulator.php`, and view renders leave summary directly. |
| LEAV-03 | 23-00, 23-02 | Users no longer see the calculator as part of the main navigation or primary action path. | ✓ SATISFIED | Sidebar/footer remove calculator links; HR dashboard guidance no longer points to calculator; navigation smoke PASS. |
| LEAV-04 | 23-00, 23-02 | The standalone calculator page is removed after the detail-based replacement flow is in place. | ✓ SATISFIED | `hr/kalkulator.php` no longer renders calculator UI and instead redirects to `/hr/reports.php`; replacement self-view path already exists and works. |

**Requirement accounting:** All requirement IDs declared in Phase 23 plan frontmatter (`CRUD-03`, `LEAV-02`, `LEAV-03`, `LEAV-04`) were found in `REQUIREMENTS.md` and verified. No orphaned Phase 23 requirements were found.

### Anti-Patterns Found

| File | Line | Pattern | Severity | Impact |
| --- | --- | --- | --- | --- |
| None | — | No blocking TODO/FIXME/placeholders or stub handlers found in Phase 23 implementation files. | — | — |

### Human Verification

### 1. Employee self-view visual emphasis

**Test:** Login as an employee and open `/employee/dashboard.php`.
**Expected:** The years 6-8 leave table appears as the first dominant content block, identity context is minimal, and the HR-contact hint remains visible.
**Why human:** Static inspection confirms structure and content, but not actual visual prominence, spacing, or perceived emphasis.
**Result:** Approved by user on 2026-03-09.

### Gaps Summary

No implementation gaps were found. Automated checks passed, and the remaining visual-emphasis verification was approved by the user.

---

_Verified: 2026-03-09T00:00:00Z_
_Verifier: Claude (gsd-verifier)_