---
phase: quick
plan: 7
type: execute
wave: 1
depends_on: []
files_modified:
  - .planning/PROJECT.md
  - .planning/v2.0-v2.0-MILESTONE-AUDIT.md
  - .planning/phases/14-database-foundation/14-VERIFICATION.md
  - database/sicuti_hrd.sql
  - koneksi.php
  - database/migrations/.gitkeep
  - database/verify_phase14_runtime.php
  - .agents/skills/php-mysqli-newbie-crud/SKILL.md
  - .agents/skills/skill-creator/SKILL.md
  - assets/img/
  - skills-lock.json
autonomous: true
requirements:
  - QUICK-07
must_haves:
  truths:
    - Only planning or code changes that still reflect the current shipped app state remain in the working tree.
    - Accidental or out-of-scope stray changes are reverted or deleted instead of being carried forward.
    - If any remaining changes are genuinely important, they are committed with a message that explains the cleanup purpose.
  artifacts:
    - path: .planning/PROJECT.md
      provides: Current milestone/project description only if the diff is still accurate and useful.
    - path: .planning/v2.0-v2.0-MILESTONE-AUDIT.md
      provides: Audit text aligned with the real verified state only if the change is materially correct.
    - path: .planning/phases/14-database-foundation/14-VERIFICATION.md
      provides: Phase 14 verification record kept only if it reflects real evidence rather than accidental drift.
  key_links:
    - from: git status
      to: retained file set
      via: explicit keep-or-revert decision for every modified/untracked path
    - from: retained planning docs
      to: current shipped v2.0/v3.0 state
      via: content review against .planning/STATE.md and roadmap facts
---

<objective>
Triage the current strange working-tree changes, keep only the meaningful planning/code updates, and remove the accidental noise.

Purpose: Prevent random repo drift from polluting the next planning/execution cycle while still preserving any genuinely useful fixes.
Output: A cleaned working tree where irrelevant changes are reverted/deleted and important cleanup is committed.
</objective>

<execution_context>
@./.opencode/get-shit-done/workflows/execute-plan.md
@./.opencode/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md
@.planning/PROJECT.md
@.planning/ROADMAP.md
@.planning/v2.0-v2.0-MILESTONE-AUDIT.md
@.planning/phases/14-database-foundation/14-VERIFICATION.md

<interfaces>
No new code interfaces are introduced in this quick plan. The main contract is repository state discipline:
- keep files only when they reflect current shipped behavior or active planning workflow
- delete/revert files that are accidental, stale, duplicated, or unrelated to the app roadmap
- create a commit only if the retained diff is meaningful after cleanup
</interfaces>
</context>

<tasks>

<task type="auto">
  <name>Task 1: Classify every current repo change as keep, revert, or delete</name>
  <files>.planning/PROJECT.md, .planning/v2.0-v2.0-MILESTONE-AUDIT.md, .planning/phases/14-database-foundation/14-VERIFICATION.md, database/sicuti_hrd.sql, koneksi.php, database/migrations/.gitkeep, database/verify_phase14_runtime.php, .agents/skills/php-mysqli-newbie-crud/SKILL.md, .agents/skills/skill-creator/SKILL.md, assets/img/, skills-lock.json</files>
  <action>Review the current modified, deleted, and untracked paths from `git status --short` one by one. For each path, decide whether it is (a) an intentional repository asset that belongs in the project, (b) an out-of-scope stray change, or (c) an accidental artifact. Keep only changes that clearly support the current planning workflow or real shipped app state. Revert/delete anything weird, stale, duplicated, or unrelated. Specifically do not preserve random generated assets, deleted runtime helpers, or planning rewrites unless they are still factually correct and useful.</action>
  <verify>
    <automated>git status --short</automated>
  </verify>
  <done>Every listed changed path has been explicitly handled, and the remaining diff contains no unexplained stray files.</done>
</task>

<task type="auto">
  <name>Task 2: Normalize retained planning/database changes against current truth</name>
  <files>.planning/PROJECT.md, .planning/v2.0-v2.0-MILESTONE-AUDIT.md, .planning/phases/14-database-foundation/14-VERIFICATION.md, database/sicuti_hrd.sql, koneksi.php</files>
  <action>If any of the retained diffs are important, edit them so they match the current repo reality exactly. Keep the beginner-style procedural PHP constraints intact. For planning docs, preserve only factual updates supported by `.planning/STATE.md`, `.planning/ROADMAP.md`, and the already shipped v2.0/v3.0 history; remove speculative or contradictory wording. For DB/runtime files, only keep changes that are required for the real app and do not silently regress the verified connection/schema contract. Avoid broad rewrites; this is a precision cleanup pass, not a new feature pass.</action>
  <verify>
    <automated>git diff -- .planning/PROJECT.md .planning/v2.0-v2.0-MILESTONE-AUDIT.md .planning/phases/14-database-foundation/14-VERIFICATION.md database/sicuti_hrd.sql koneksi.php</automated>
  </verify>
  <done>Any remaining retained file diffs are small, intentional, internally consistent, and aligned with the current shipped state.</done>
</task>

<task type="auto">
  <name>Task 3: Commit only the meaningful cleanup that survives triage</name>
  <files>.planning/PROJECT.md, .planning/v2.0-v2.0-MILESTONE-AUDIT.md, .planning/phases/14-database-foundation/14-VERIFICATION.md, database/sicuti_hrd.sql, koneksi.php, .agents/skills/php-mysqli-newbie-crud/SKILL.md, .agents/skills/skill-creator/SKILL.md, assets/img/, skills-lock.json</files>
  <action>If Task 1 and Task 2 leave behind a meaningful set of changes, stage only those intentional files and create one commit describing the cleanup, for example a docs/chore message about removing stray changes and preserving important planning fixes. If nothing meaningful remains after cleanup, do not create an empty commit. In either case, finish with a clean explanation in the summary stating what was reverted, what was kept, and why.</action>
  <verify>
    <automated>git status --short && git log -1 --oneline</automated>
  </verify>
  <done>The repo is either clean with no commit needed, or the meaningful cleanup is committed and `git status --short` shows no accidental leftovers.</done>
</task>

</tasks>

<verification>
Confirm the final working tree contains only intentional files. If a commit was created, verify its scope matches the surviving cleanup and does not include unrelated noise.
</verification>

<success_criteria>
The repo no longer carries unexplained strange changes, important retained fixes are accurate, and a commit exists only if the surviving diff is genuinely worth keeping.
</success_criteria>

<output>
After completion, create `.planning/quick/7-tracking-planning-fixing-sama-stange-cha/7-SUMMARY.md`
</output>
