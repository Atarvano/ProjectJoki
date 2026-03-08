<?php
$page_title = $dashboard_context['page_title'] ?? 'Dashboard';
$breadcrumb = $dashboard_context['breadcrumb'] ?? [];
$profile_label = trim((string) ($dashboard_context['profile_label'] ?? ''));
if ($profile_label === '') {
    $profile_label = 'Pengguna';
}

$profile_initials = trim((string) ($dashboard_context['profile_initials'] ?? ''));
if ($profile_initials === '') {
    $profile_initials = 'PG';
}
$role_key = strtolower((string) ($dashboard_context['role'] ?? 'hr'));
$profile_role = trim((string) ($dashboard_context['profile_role'] ?? ''));
if ($profile_role === '') {
    $profile_role = $role_key === 'employee' ? 'Karyawan' : 'HR';
}
?>
<header class="dashboard-topbar bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <!-- Mobile Toggle -->
        <button class="btn btn-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashboardSidebar" aria-controls="dashboardSidebar">
            <i class="bi bi-list fs-4 text-muted"></i>
        </button>

        <div>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0" style="font-family: var(--font-body); font-size: 0.875rem;">
                    <?php foreach ($breadcrumb as $index => $crumb): ?>
                        <?php if ($index === count($breadcrumb) - 1): ?>
                            <li class="breadcrumb-item active fw-bold text-primary-dark" aria-current="page"><?php echo $crumb['label']; ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?php echo $crumb['href'] ?? '#'; ?>" class="text-decoration-none text-muted hover-primary"><?php echo $crumb['label']; ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <h5 class="mb-0 fw-bold d-md-none text-primary-dark" style="font-family: var(--font-display);"><?php echo $page_title; ?></h5>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-3">
        <!-- Dummy Search -->
        <div class="dashboard-search d-none d-md-block position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-primary" style="opacity: 0.5;"></i>
            <input type="text" class="form-control bg-light border-0 ps-5 rounded-pill" placeholder="Cari..." aria-label="Search" style="font-family: var(--font-body);" disabled readonly>
        </div>

        <!-- Dummy Notification -->
        <button class="btn btn-light position-relative rounded-circle p-2 border-0 bg-light" type="button" disabled>
            <i class="bi bi-bell text-muted"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-accent border border-light rounded-circle">
                <span class="visually-hidden">New alerts</span>
            </span>
        </button>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm" style="width: 36px; height: 36px; font-family: var(--font-display);">
                    <?php echo $profile_initials; ?>
                </div>
                <span class="d-none d-sm-inline-flex flex-column lh-sm">
                    <span class="fw-bold text-primary-dark" style="font-family: var(--font-body);"><?php echo $profile_label; ?></span>
                    <small class="text-muted" style="font-family: var(--font-body); font-size: 0.72rem;"><?php echo $profile_role; ?></small>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end text-small shadow-sm border-0" aria-labelledby="dropdownUser" style="font-family: var(--font-body);">
                <li><a class="dropdown-item fw-medium text-secondary" href="#"><i class="bi bi-person me-2 text-primary opacity-75"></i>Profil</a></li>
                <li><a class="dropdown-item fw-medium text-secondary" href="#"><i class="bi bi-gear me-2 text-primary opacity-75"></i>Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item fw-bold text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
        </div>
    </div>
</header>
