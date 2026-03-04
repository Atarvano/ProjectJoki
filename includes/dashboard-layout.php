<?php
// Shared dashboard layout shell
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