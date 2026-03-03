<?php
$current_role = 'hr';
$role_label = 'HR';
$page_title = 'Dashboard HR - Sicuti HRD';
$page_class = 'page-dashboard role-hr';
include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="dashboard-header">
        <div class="d-flex align-items-center gap-3">
            <span class="role-badge">
                <i class="bi bi-shield-check me-2"></i>
                Mode <?php echo htmlspecialchars($role_label); ?>
            </span>
            <span class="demo-badge">
                <i class="bi bi-eye me-1"></i>
                Demo
            </span>
        </div>
        <div class="text-muted small">Sicuti HRD</div>
    </div>

    <div class="guided-step">
        <h2 class="accent-bar">Selamat datang di Dashboard <?php echo htmlspecialchars($role_label); ?></h2>
        <p>Mulai dengan menghitung hak cuti karyawan berdasarkan tahun bergabung. Kelola data karyawan dan lihat laporan cuti dengan mudah.</p>
        
        <a href="#" class="btn btn-primary btn-lg px-5 disabled" aria-disabled="true">
            <i class="bi bi-calculator me-2"></i>
            Hitung Hak Cuti
        </a>
        <div class="text-muted small mt-2">(Segera hadir di Fase 2)</div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="stat-card accent-bar">
                <div class="stat-label">Karyawan Terdaftar</div>
                <div class="stat-value">--</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card accent-bar">
                <div class="stat-label">Laporan Tersimpan</div>
                <div class="stat-value">--</div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
