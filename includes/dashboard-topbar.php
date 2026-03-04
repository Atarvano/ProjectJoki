<?php
$page_title = $dashboard_context['page_title'] ?? 'Dashboard';
$breadcrumb = $dashboard_context['breadcrumb'] ?? [];
$profile_label = $dashboard_context['profile_label'] ?? 'Admin HR';
$profile_initials = $dashboard_context['profile_initials'] ?? 'HR';
$demo_badge = $dashboard_context['demo_badge'] ?? 'Demo v1';
?>
<header class="dashboard-topbar bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <!-- Mobile Toggle -->
        <button class="btn btn-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashboardSidebar" aria-controls="dashboardSidebar">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0">
                    <?php foreach ($breadcrumb as $index => $crumb): ?>
                        <?php if ($index === count($breadcrumb) - 1): ?>
                            <li class="breadcrumb-item active fw-bold" aria-current="page"><?php echo $crumb['label']; ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?php echo $crumb['href'] ?? '#'; ?>" class="text-decoration-none text-muted"><?php echo $crumb['label']; ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <h5 class="mb-0 fw-bold d-md-none"><?php echo $page_title; ?></h5>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-3">
        <!-- Demo Badge -->
        <span class="demo-badge d-none d-sm-inline-flex">
            <i class="bi bi-eye me-1"></i> <?php echo $demo_badge; ?>
        </span>

        <!-- Dummy Search -->
        <div class="dashboard-search d-none d-md-block position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" class="form-control bg-light border-0 ps-5 rounded-pill" placeholder="Cari..." aria-label="Search" disabled readonly>
        </div>

        <!-- Dummy Notification -->
        <button class="btn btn-light position-relative rounded-circle p-2 border-0 bg-light" type="button" disabled>
            <i class="bi bi-bell text-muted"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                <span class="visually-hidden">New alerts</span>
            </span>
        </button>

        <!-- Profile Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 36px; height: 36px;">
                    <?php echo $profile_initials; ?>
                </div>
                <span class="d-none d-sm-inline fw-medium"><?php echo $profile_label; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end text-small shadow border-0" aria-labelledby="dropdownUser">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2 text-muted"></i>Profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2 text-muted"></i>Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="/index.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>