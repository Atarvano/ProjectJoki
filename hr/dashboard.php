<?php
$current_role = 'hr';
$role_label = 'HR';
$page_title = 'Dashboard HR - Sicuti HRD';
$page_class = 'page-dashboard role-hr';

// Include calculator engine
require_once __DIR__ . '/../includes/cuti-calculator.php';

// Handle calculator logic
$hasil = null;
$error = null;
$tahun_input = '';

if (isset($_GET['tahun_bergabung'])) {
    $tahun_input = trim($_GET['tahun_bergabung']);
    $validasi = validasiTahunBergabung($tahun_input);
    if ($validasi['valid']) {
        $hasil = hitungHakCuti((int)$tahun_input);
    } else {
        $error = $validasi['pesan'];
    }
}

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

    <!-- Calculator Section -->
    <?php if ($hasil === null): ?>
        <!-- State A: Empty State or Error State (form is visible in both) -->
        <div class="calculator-empty-state mb-5">
            <h2 class="accent-bar mb-3">Kalkulator Hak Cuti</h2>
            <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
                Hitung hak cuti karyawan selama 8 tahun ke depan berdasarkan tahun bergabung.
                Sistem akan menampilkan tabel entitlement secara otomatis.
            </p>

            <?php if ($error): ?>
                <div class="alert alert-danger d-inline-block mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="GET" class="calculator-form mx-auto" style="max-width: 500px;">
                <div class="input-group">
                    <input type="number" 
                           name="tahun_bergabung" 
                           class="form-control form-control-lg" 
                           placeholder="Contoh: 2020" 
                           min="1990" 
                           max="<?php echo date('Y'); ?>"
                           value="<?php echo htmlspecialchars($tahun_input); ?>"
                           required>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-calculator me-2"></i>Hitung
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- State C: Result State -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="calculator-form h-100">
                    <h4 class="mb-3">Hitung Ulang</h4>
                    <form action="" method="GET">
                        <div class="mb-3">
                            <label for="tahun_bergabung" class="form-label text-muted small">Tahun Bergabung</label>
                            <input type="number" 
                                   id="tahun_bergabung"
                                   name="tahun_bergabung" 
                                   class="form-control" 
                                   placeholder="Contoh: 2020" 
                                   min="1990" 
                                   max="<?php echo date('Y'); ?>"
                                   value="<?php echo htmlspecialchars($tahun_input); ?>"
                                   required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Hitung Ulang
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="calculator-summary d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-calendar-check me-2"></i>
                        Bergabung: <strong><?php echo htmlspecialchars($hasil['tahun_bergabung']); ?></strong>
                    </span>
                    <span>
                        Periode: <strong><?php echo $hasil['tahun_mulai']; ?> - <?php echo $hasil['tahun_selesai']; ?></strong>
                    </span>
                </div>

                <div class="calculator-result mt-0 pt-0 border-0">
                    <div class="table-responsive">
                        <table class="table entitlement-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">No.</th>
                                    <th>Tahun Kalender</th>
                                    <th class="text-center">Hari Cuti</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hasil['data'] as $row): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?php echo $row['tahun_ke']; ?></td>
                                        <td class="fw-bold"><?php echo $row['tahun_kalender']; ?></td>
                                        <td class="text-center">
                                            <span class="fs-5 text-primary fw-bold"><?php echo $row['hari_cuti']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill <?php echo $row['status_class']; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
