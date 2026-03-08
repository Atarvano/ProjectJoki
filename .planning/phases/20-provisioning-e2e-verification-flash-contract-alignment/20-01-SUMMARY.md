---
phase: 20-provisioning-e2e-verification-flash-contract-alignment
plan: 01
subsystem: testing
tags: [php, provisioning, flash, sessions, browser-verification]

requires:
  - phase: 19-auth-session-revalidation-identity-consistency
    provides: auth/session guard baseline and browser-proof pattern
provides:
  - Structured provisioning success flash contract aligned between endpoint and renderer
  - Updated provisioning smoke coverage for structured credential keys
  - Phase 20 browser validation checklist locked to the HR-first provisioning walkthrough
affects: [phase-20-browser-proof, milestone-runtime-proof, provisioning-auth-flow]

tech-stack:
  added: []
  patterns: [structured flash.credentials payload with message fallback, lean phase validation docs backed by smoke scripts]

key-files:
  created:
    - .planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-VALIDATION.md
    - .planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-01-SUMMARY.md
  modified:
    - hr/karyawan-provision.php
    - tests/provisioning_endpoint_test.php

key-decisions:
  - "Keep provisioning success on one structured flash.credentials contract while preserving a generic message fallback."
  - "Use one lightweight Phase 20 validation file to lock the final browser walkthrough instead of adding new automation tooling."

patterns-established:
  - "Provisioning success pattern: endpoint writes flash.credentials.username, password_awal, and pattern_example; hr/karyawan.php renders the specialized one-time credential block."
  - "Validation pattern: smoke scripts stay cheap and manual browser proof is documented in one focused phase validation file."

requirements-completed: [PROV-01, PROV-02, PROV-03, PROV-04]

duration: 6 min
completed: 2026-03-08
---

# Phase 20 Plan 01: Provisioning Flash Contract Alignment Summary

**Provisioning now returns one structured `flash.credentials` payload that `hr/karyawan.php` can render directly, with a locked Phase 20 browser checklist for the final HR-to-employee walkthrough.**

## Performance

- **Duration:** 6 min
- **Started:** 2026-03-08T15:56:17.465Z
- **Completed:** 2026-03-08T16:02:17.465Z
- **Tasks:** 2
- **Files modified:** 4

## Accomplishments
- Aligned `hr/karyawan-provision.php` success handling to the structured credential contract already supported by `hr/karyawan.php`.
- Updated the provisioning smoke script so automated checks now prove the structured flash keys instead of relying on the legacy text blob alone.
- Created `20-VALIDATION.md` with the exact browser walkthrough for HR login, provisioning, one-time flash, logout, employee login, wrong-role redirect, and post-logout guard proof.

## Task Commits

Each task was committed atomically:

1. **Task 1: Tighten the provisioning smoke test and align the endpoint success payload** - `fa80e73` (feat)
2. **Task 2: Create the Phase 20 validation contract and locked browser checklist** - `2df230c` (docs)

## Files Created/Modified
- `hr/karyawan-provision.php` - Changes the success branch to write `message` plus a structured `credentials` payload.
- `tests/provisioning_endpoint_test.php` - Verifies structured credential keys in the endpoint and copy markers in the list-page renderer.
- `.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-VALIDATION.md` - Defines the locked browser walkthrough and lightweight validation contract for the next plan.
- `.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-01-SUMMARY.md` - Records plan outcomes, commits, and readiness for Plan 20-02.

## Decisions Made
- Keep the provisioning endpoint procedural and local: only the success flash payload changed, while POST-only guards, password formula, hashing path, and redirect target stayed intact.
- Split automated proof and browser proof cleanly: smoke scripts cover the contract seam, while the browser checklist captures the remaining runtime evidence.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered
- The first RED test version incorrectly looked for renderer copy markers inside `hr/karyawan-provision.php`; this was corrected by asserting structured payload keys in the endpoint and user-facing copy markers in `hr/karyawan.php`.
- `.planning` artifacts are git-ignored in this repo, so task and metadata commits require `git add -f` for summary/validation files.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness
- Plan 20-02 can now run the locked browser walkthrough without guessing the provisioning path or proof points.
- The structured flash contract is aligned, so runtime verification can focus on browser behavior instead of endpoint/render drift.

## Self-Check: PASSED
- Verified required summary file exists: `.planning/phases/20-provisioning-e2e-verification-flash-contract-alignment/20-01-SUMMARY.md`.
- Verified task commit exists: `fa80e73`.
- Verified task commit exists: `2df230c`.
