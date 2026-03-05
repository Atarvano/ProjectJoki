---
phase: 15-authentication-access-control
plan: 01
subsystem: auth
tags: [php, mysqli, sessions, rbac, password_verify]

requires:
  - phase: 14-database-foundation
    provides: MySQL schema, users seed data, and koneksi.php
provides:
  - Centralized auth guard functions cekLogin() and cekRole()
  - Logout endpoint that destroys session state
  - Real POST login flow with DB credential validation and role redirects
affects: [phase-15-plan-02, dashboard-auth-ui, page-guards]

tech-stack:
  added: []
  patterns: [procedural PHP auth handler, MySQLi prepared statements, session-based RBAC redirects]

key-files:
  created: [includes/auth-guard.php, logout.php]
  modified: [login.php]

key-decisions:
  - "Keep authentication in a single procedural login.php POST handler before any HTML output."
  - "Use absolute redirect paths (/login.php, /hr/dashboard.php, /employee/dashboard.php) to avoid nested path issues."

patterns-established:
  - "Auth guard include pattern: require guard then call cekLogin() and cekRole()."
  - "Session identity contract set during login: user_id, username, role, karyawan_id, nama."

requirements-completed: [AUTH-01, AUTH-02, AUTH-03, RBAC-01, RBAC-04]

duration: 0 min
completed: 2026-03-05
---

# Phase 15 Plan 01: Core Authentication Files Summary

**Procedural PHP authentication foundation with session guard helpers, real DB-backed POST login, and role-based redirect flow.**

## Performance

- **Duration:** 0 min
- **Started:** 2026-03-05T13:56:15+07:00
- **Completed:** 2026-03-05T06:57:08Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments
- Added `includes/auth-guard.php` with `cekLogin()` and `cekRole()` to enforce login and role access routing.
- Added `logout.php` to clear `$_SESSION`, destroy the session, and redirect to `/login.php`.
- Rewrote `login.php` to process real POST credentials via prepared statement + `password_verify()`, set required session keys, and redirect by role.
- Replaced demo login UI with real NIK/password inputs, error alert, HR test credential helper chip, provisioning reminder, and forgot-password note.

## Task Commits

Each task was committed atomically:

1. **Task 1: Create auth-guard.php and logout.php** - `43c38e4` (feat)
2. **Task 2: Rewrite login.php with real POST authentication** - `08168a1` (feat)

## Files Created/Modified
- `includes/auth-guard.php` - Central auth guard helpers for login and role checks.
- `logout.php` - Session destruction endpoint with redirect to login.
- `login.php` - Full POST auth handler and login form rewrite using DB validation.

## Decisions Made
- Keep `require_once 'koneksi.php'` inside the POST block so GET view rendering does not require unnecessary DB work.
- For non-HR users, fetch `karyawan.nama` when possible and fall back to username if name lookup is unavailable.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- Task 1 commit included a pre-staged unrelated `.planning/ROADMAP.md` change already present in the repository index before task staging.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Phase 15 plan 01 outputs are complete and verified with PHP lint plus required string checks.
- Ready for `15-02-PLAN.md` to apply guards across pages and wire session identity into dashboard UI.

## Self-Check: PASSED
- Verified required files exist: `includes/auth-guard.php`, `logout.php`, `login.php`, and `15-01-SUMMARY.md`.
- Verified task commits exist: `43c38e4`, `08168a1`.
