<?php
$role = isset($_GET['role']) ? $_GET['role'] : 'hr';
$valid_roles = ['hr', 'employee'];
if (!in_array($role, $valid_roles)) {
    $role = 'hr';
}

$is_hr = ($role === 'hr');
$role_label = $is_hr ? 'HR' : 'Karyawan';
$role_action = $is_hr ? 'Masuk sebagai HR' : 'Masuk sebagai Karyawan';
$role_dashboard = $is_hr ? 'hr/dashboard.php' : 'employee/dashboard.php';
$switch_role = $is_hr ? 'employee' : 'hr';
$switch_label = $is_hr ? 'Masuk sebagai Karyawan' : 'Masuk sebagai HR';
$role_icon = $is_hr ? 'bi-shield-check' : 'bi-person';

$page_title = "Login $role_label - Sicuti HRD";
$page_class = "page-login role-$role";
include 'includes/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <div class="text-center mb-4">
            <span class="role-badge">
                <i class="bi <?php echo $role_icon; ?> me-2"></i>
                <?php echo htmlspecialchars($role_label); ?>
            </span>
        </div>

        <h1 class="h2 text-center mb-4 accent-bar d-inline-block mx-auto">
            Selamat Datang, <?php echo htmlspecialchars($role_label); ?>
        </h1>

        <div class="demo-notice mb-4 text-center">
            <i class="bi bi-info-circle me-1"></i>
            Ini adalah akses demo — tidak memerlukan autentikasi.
        </div>

        <!-- Decorative Form Fields -->
        <div class="mb-3">
            <label class="form-label text-muted small">Email (Demo)</label>
            <input type="email" class="form-control bg-light" value="demo@perusahaan.com" readonly>
        </div>
        <div class="mb-4">
            <label class="form-label text-muted small">Password</label>
            <input type="password" class="form-control bg-light" value="********" readonly>
        </div>

        <a href="<?php echo htmlspecialchars($role_dashboard); ?>" class="btn btn-primary btn-lg w-100 mb-4">
            <?php echo htmlspecialchars($role_action); ?>
        </a>

        <div class="role-switch text-center pt-3 border-top">
            <span class="text-muted small d-block mb-2">Atau</span>
            <a href="login.php?role=<?php echo htmlspecialchars($switch_role); ?>" class="text-decoration-none fw-medium">
                <?php echo htmlspecialchars($switch_label); ?> <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
