<?php
$role = $dashboard_context['role'] ?? 'employee';
$active_nav = $dashboard_context['active_nav'] ?? 'dashboard';

// Define navigation based on role
$nav_items = [];
if ($role === 'hr') {
    $nav_items = [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid', 'href' => '/hr/dashboard.php'],
        ['id' => 'kalkulator', 'label' => 'Kalkulator Cuti', 'icon' => 'bi-calculator', 'href' => '/hr/kalkulator.php'],
        ['id' => 'laporan', 'label' => 'Laporan', 'icon' => 'bi-file-earmark-text', 'href' => '/hr/laporan.php'],
    ];
} else {
    $nav_items = [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid', 'href' => '/employee/dashboard.php'],
        ['id' => 'hak-cuti', 'label' => 'Hak Cuti Saya', 'icon' => 'bi-calendar-check', 'href' => '#', 'is_helper' => true],
    ];
}
?>

<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start dashboard-sidebar" tabindex="-1" id="dashboardSidebar" aria-labelledby="dashboardSidebarLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-primary" id="dashboardSidebarLabel">
            <i class="bi bi-layers me-2"></i>Sicuti HRD
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div class="p-3">
            <div class="role-badge mb-3 d-inline-block">
                <i class="bi bi-shield-check me-1"></i> Mode <?php echo $role === 'hr' ? 'HR' : 'Karyawan'; ?>
            </div>
        </div>

        <ul class="nav flex-column mb-auto px-2">
            <?php foreach ($nav_items as $item): ?>
                <?php if (isset($item['is_helper']) && $item['is_helper']): ?>
                    <li class="nav-item mb-1 px-3 mt-3">
                        <span class="text-muted small fw-bold text-uppercase" style="cursor: default;">
                            <?php echo $item['label']; ?>
                        </span>
                    </li>
                <?php else: ?>
                    <li class="nav-item mb-1">
                        <a href="<?php echo $item['href']; ?>" class="nav-link px-3 py-2 <?php echo $active_nav === $item['id'] ? 'active bg-primary-subtle text-primary fw-bold' : 'text-dark'; ?> rounded d-flex align-items-center">
                            <i class="bi <?php echo $item['icon']; ?> me-3 fs-5 <?php echo $active_nav === $item['id'] ? 'text-primary' : 'text-muted'; ?>"></i>
                            <?php echo $item['label']; ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Desktop Sidebar (visible on lg up) -->
<aside class="dashboard-sidebar border-end d-none d-lg-flex flex-column" id="desktopSidebar">
    <div class="sidebar-header p-3 border-bottom d-flex align-items-center">
        <i class="bi bi-layers text-primary fs-4 me-2"></i>
        <span class="fw-bold fs-5 text-primary sidebar-brand-text">Sicuti HRD</span>
    </div>
    
    <div class="p-3 sidebar-role-container">
        <div class="role-badge w-100 text-center sidebar-role-text">
            Mode <?php echo $role === 'hr' ? 'HR' : 'Karyawan'; ?>
        </div>
    </div>

    <ul class="nav flex-column mb-auto px-2">
        <?php foreach ($nav_items as $item): ?>
            <?php if (isset($item['is_helper']) && $item['is_helper']): ?>
                <li class="nav-item mb-1 px-3 mt-3">
                    <span class="text-muted small fw-bold text-uppercase sidebar-nav-text" style="cursor: default;" title="<?php echo $item['label']; ?>">
                        <?php echo $item['label']; ?>
                    </span>
                </li>
            <?php else: ?>
                <li class="nav-item mb-1">
                    <a href="<?php echo $item['href']; ?>" class="nav-link px-3 py-2 <?php echo $active_nav === $item['id'] ? 'active bg-primary-subtle text-primary fw-bold' : 'text-dark'; ?> rounded d-flex align-items-center" title="<?php echo $item['label']; ?>">
                        <i class="bi <?php echo $item['icon']; ?> me-3 fs-5 <?php echo $active_nav === $item['id'] ? 'text-primary' : 'text-muted'; ?>"></i>
                        <span class="sidebar-nav-text"><?php echo $item['label']; ?></span>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</aside>