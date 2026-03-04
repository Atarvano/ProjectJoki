<?php
// Shared dashboard layout shell

// Ensure page title and class are set for the header
$page_title = $dashboard_context['page_title'] ?? 'Dashboard - Sicuti HRD';
if (!isset($page_class)) {
    $role_class = isset($dashboard_context['role']) ? 'role-' . $dashboard_context['role'] : '';
    $page_class = trim("page-dashboard $role_class");
}

// Include the standard header which contains all CSS and font links
require_once __DIR__ . '/header.php';
?>
<div class="dashboard-shell <?php echo isset($dashboard_context['sidebar_collapsed']) && $dashboard_context['sidebar_collapsed'] ? 'is-sidebar-collapsed' : ''; ?>">
    <!-- Sidebar -->
    <?php include __DIR__ . '/dashboard-sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="dashboard-main d-flex flex-column min-vh-100">
        <!-- Topbar -->
        <?php include __DIR__ . '/dashboard-topbar.php'; ?>

        <!-- Page Content -->
        <main class="dashboard-content p-3 p-md-4 flex-grow-1">
            <?php echo $page_content ?? ''; ?>
        </main>

        <!-- Footer -->
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
</div>