---
phase: quick
plan: 3
type: execute
wave: 1
depends_on: []
files_modified:
  - assets/css/style.css
  - hr/dashboard.php
  - employee/dashboard.php
  - includes/dashboard-sidebar.php
autonomous: true
requirements:
  - QUICK-03
must_haves:
  truths:
    - Dashboard typography matches the landing page exactly (Outfit for headings)
    - Stat cards match landing page feature cards (borders, shadows, hover effects)
    - Sidebar and topbar utilize Deep Teal and Warm Amber theme properly
  artifacts:
    - path: assets/css/style.css
      provides: Dashboard token overrides matching landing page
    - path: hr/dashboard.php
      provides: Refactored markup using new classes
    - path: employee/dashboard.php
      provides: Refactored markup using new classes
  key_links:
    - from: assets/css/style.css
      to: Dashboard Views
      via: CSS classes (.dashboard-stat-card, .dashboard-sidebar)
---

<objective>
Redesign the HR and Employee dashboards to visually align with the newly styled landing page.

Purpose: Bring visual cohesion across the entire application by enforcing the Deep Teal / Warm Amber color palette, Outfit font for headers, and the precise card/shadow styling established on the landing page.
Output: Unified CSS in style.css and refactored dashboard PHP files.
</objective>

<execution_context>
@./.opencode/get-shit-done/workflows/execute-plan.md
@./.opencode/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md

# CSS Reference for target style
@assets/css/style.css
</context>

<tasks>

<task type="auto">
  <name>Task 1: Unify Dashboard CSS with Landing Page</name>
  <files>assets/css/style.css, includes/dashboard-sidebar.php</files>
  <action>
    Modify `assets/css/style.css` to update dashboard styles (`.page-dashboard` scope):
    - Apply `font-family: var(--font-display)` to all headings (`h1, h2, h3, h4, h5, h6, .dashboard-header`) inside the dashboard.
    - Update `.dashboard-stat-card` to match the landing page's `.gradient-card` or feature cards: add subtle borders, exact `box-shadow`, `--radius-lg`, and the slight transform-up hover effect.
    - Apply the exact Deep Teal (`var(--color-primary)`) to the `.dashboard-sidebar` and ensure active nav links use Warm Amber (`var(--color-accent)`).
    
    Modify `includes/dashboard-sidebar.php` to ensure text and icons contrast correctly against the new background (e.g., using `text-white` or `text-light` for links and brand, removing conflicting inline text classes).
  </action>
  <verify>
    <automated>grep -q "var(--font-display)" assets/css/style.css</automated>
  </verify>
  <done>CSS contains landing page token integrations for dashboard elements and sidebar reflects the primary colors.</done>
</task>

<task type="auto">
  <name>Task 2: Refactor HR and Employee Dashboard Views</name>
  <files>hr/dashboard.php, employee/dashboard.php</files>
  <action>
    Update both dashboard views to utilize the refined CSS classes:
    - In `hr/dashboard.php`: Convert generic `.stat-card` and inline-styled `.card` elements to use `.dashboard-stat-card` matching `employee/dashboard.php`. Add bold text utilities and ensure icons use `.dashboard-stat-icon`.
    - In `employee/dashboard.php`: Remove inline `style="font-family: var(--font-display)..."` from headings since it will now be handled by CSS. Clean up redundant utility classes on `.dashboard-stat-card`.
    - Ensure any "Hero" or welcome banner in the dashboards uses a soft Deep Teal background (`bg-primary` with `text-white`) with the Warm Amber accent button (`btn-accent` if available, or `btn-warning`), mirroring the landing page CTA.
  </action>
  <verify>
    <automated>grep -q "dashboard-stat-card" hr/dashboard.php</automated>
  </verify>
  <done>HR and Employee dashboards are structurally clean, use standardized dashboard classes, and visually match the landing page.</done>
</task>

</tasks>

<verification>
Check both HR and Employee dashboard views in a browser to confirm typography (Outfit), card styling (shadows/hover), and sidebar colors match the landing page.
</verification>

<success_criteria>
Dashboard UI shares exact visual identity (Deep Teal, Warm Amber, Outfit font, clean cards) with the landing page without breaking layout constraints.
</success_criteria>

<output>
After completion, create `.planning/phases/quick/3-redesign-dashboard-to-match-landing-page-SUMMARY.md`
</output>