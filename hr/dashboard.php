<?php
$current_role = 'hr';
$role_label = 'HR';
$page_title = 'Dashboard HR - Sicuti HRD';
$page_class = 'page-dashboard role-hr';

// Include calculator engine
require_once __DIR__ . '/../includes/cuti-calculator.php';
require_once __DIR__ . '/../includes/reports-data.php';

$reports = initReports();

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_report') {
        $tahun_input = trim($_POST['tahun_bergabung']);
        $nama = trim($_POST['nama_karyawan'] ?? '');
        $simpan = isset($_POST['simpan_laporan']);
        
        $validasi = validasiTahunBergabung($tahun_input);
        
        if ($validasi['valid']) {
            if ($simpan) {
                if (strlen($nama) < 3) {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Nama karyawan minimal 3 karakter.'];
                } else {
                    $result = saveReport($nama, (int)$tahun_input);
                    if ($result === 'created') {
                        $_SESSION['flash'] = ['type' => 'success', 'message' => "Laporan disimpan untuk $nama ($tahun_input)."];
                    } else {
                        $_SESSION['flash'] = ['type' => 'success', 'message' => "Laporan diperbarui untuk $nama ($tahun_input)."];
                    }
                }
            }
            // Always redirect to show the calculator result
            header('Location: dashboard.php?tahun_bergabung=' . urlencode($tahun_input));
            exit;
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $validasi['pesan']];
            header('Location: dashboard.php');
            exit;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset_reports') {
        resetReports();
        $_SESSION['flash'] = ['type' => 'info', 'message' => 'Semua laporan telah direset.'];
        header('Location: dashboard.php');
        exit;
    }
}

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$reports = getReports();
if ($reports === null) $reports = [];

$report_display = [];
$filter_status = $_GET['filter_status'] ?? '';
$current_year = date('Y');

foreach ($reports as $index => $report) {
    $calc = hitungHakCuti($report['tahun_bergabung']);
    $current_status = '';
    $current_status_class = '';
    
    foreach ($calc['data'] as $row) {
        if ($row['tahun_kalender'] == $current_year) {
            $current_status = $row['status'];
            $current_status_class = $row['status_class'];
            break;
        }
    }
    
    // Add current status to the report array
    $report['current_status'] = $current_status;
    $report['current_status_class'] = $current_status_class;
    $report['original_index'] = $index;
    
    if (empty($filter_status) || $current_status === $filter_status) {
        $report_display[] = $report;
    }
}

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
    
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show mb-4">
            <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle'); ?> me-2"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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

            <form action="" method="POST" class="calculator-form mx-auto" style="max-width: 500px;">
                <input type="hidden" name="action" value="save_report">
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
                
                <div class="save-option mt-3 text-start">
                    <div class="form-check">
                        <input class="form-check-input simpan-checkbox" type="checkbox" id="simpan_laporan_empty" name="simpan_laporan" value="1">
                        <label class="form-check-label" for="simpan_laporan_empty">
                            <i class="bi bi-bookmark-plus me-1"></i>Simpan laporan ini
                        </label>
                    </div>
                    <div class="save-name-field mt-2" style="display: none;">
                        <input type="text" name="nama_karyawan" class="form-control" placeholder="Nama karyawan (min. 3 karakter)" minlength="3">
                    </div>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- State C: Result State -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="calculator-form h-100">
                    <h4 class="mb-3">Hitung Ulang</h4>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="save_report">
                        <div class="mb-3">
                            <label for="tahun_bergabung_result" class="form-label text-muted small">Tahun Bergabung</label>
                            <input type="number" 
                                   id="tahun_bergabung_result"
                                   name="tahun_bergabung" 
                                   class="form-control" 
                                   placeholder="Contoh: 2020" 
                                   min="1990" 
                                   max="<?php echo date('Y'); ?>"
                                   value="<?php echo htmlspecialchars($tahun_input); ?>"
                                   required>
                        </div>
                        
                        <div class="save-option mb-3">
                            <div class="form-check">
                                <input class="form-check-input simpan-checkbox" type="checkbox" id="simpan_laporan_result" name="simpan_laporan" value="1">
                                <label class="form-check-label" for="simpan_laporan_result">
                                    <i class="bi bi-bookmark-plus me-1"></i>Simpan laporan ini
                                </label>
                            </div>
                            <div class="save-name-field mt-2" style="display: none;">
                                <input type="text" name="nama_karyawan" class="form-control" placeholder="Nama karyawan (min. 3 karakter)" minlength="3">
                            </div>
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
                <div class="stat-label">Karyawan Preset</div>
                <div class="stat-value"><?php echo countPresetKaryawan(); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card accent-bar">
                <div class="stat-label">Laporan Tersimpan</div>
                <div class="stat-value"><?php echo countReports(); ?></div>
            </div>
        </div>
    </div>
    
    <div class="report-section mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="accent-bar mb-0">Laporan Cuti Karyawan</h3>
            <div class="d-flex gap-2">
                <!-- Status filter dropdown -->
                <form method="GET" class="d-inline">
                    <select name="filter_status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" <?php echo $filter_status === 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                        <option value="Menunggu" <?php echo $filter_status === 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="Rencana" <?php echo $filter_status === 'Rencana' ? 'selected' : ''; ?>>Rencana</option>
                    </select>
                </form>
                <!-- Export button (links to export.php) -->
                <a href="export.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export Excel
                </a>
                <!-- Reset button -->
                <form method="POST" class="d-inline" onsubmit="return confirm('Reset semua laporan? Data demo akan dihapus.');">
                    <input type="hidden" name="action" value="reset_reports">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                </form>
            </div>
        </div>

        <!-- Info banner -->
        <div class="alert alert-info py-2 mb-3">
            <i class="bi bi-info-circle me-2"></i>
            Data demo — reset setiap refresh sesi. Gunakan Export untuk simpan sebagai Excel.
        </div>
        
        <?php if (count($reports) === 0 || empty($report_display)): ?>
            <div class="report-empty-state">
                <i class="bi bi-folder-x fs-1 text-muted mb-2 d-block"></i>
                <p class="mb-0">Belum ada laporan tersimpan.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table report-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th>Nama Karyawan</th>
                            <th class="text-center">Tahun Bergabung</th>
                            <th class="text-center">Total Hari Cuti</th>
                            <th class="text-center">Status Tahun Ini</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($report_display as $report): 
                        ?>
                            <tr>
                                <td class="text-center text-muted"><?php echo $no++; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($report['nama']); ?></td>
                                <td class="text-center"><?php echo $report['tahun_bergabung']; ?></td>
                                <td class="text-center fw-bold text-primary"><?php echo $report['total_cuti']; ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo $report['current_status_class']; ?>">
                                        <?php echo $report['current_status']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($report['is_sample']): ?>
                                        <span class="badge bg-secondary">Sample</span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal-<?php echo $report['original_index']; ?>">
                                        <i class="bi bi-eye me-1"></i>Lihat detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php foreach ($report_display as $report): 
            $detailCalc = hitungHakCuti($report['tahun_bergabung']);
        ?>
            <!-- Modal Detail for <?php echo htmlspecialchars($report['nama']); ?> -->
            <div class="modal fade" id="detailModal-<?php echo $report['original_index']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                <?php echo htmlspecialchars($report['nama']); ?> — Bergabung <?php echo $report['tahun_bergabung']; ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="calculator-summary d-flex justify-content-between align-items-center mb-3">
                                <span>
                                    <i class="bi bi-calendar-check me-2"></i>
                                    Bergabung: <strong><?php echo $detailCalc['tahun_bergabung']; ?></strong>
                                </span>
                                <span>
                                    Periode: <strong><?php echo $detailCalc['tahun_mulai']; ?> - <?php echo $detailCalc['tahun_selesai']; ?></strong>
                                </span>
                            </div>
                            
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
                                        <?php foreach ($detailCalc['data'] as $row): ?>
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.querySelectorAll('.simpan-checkbox').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var nameField = this.closest('form').querySelector('.save-name-field');
        nameField.style.display = this.checked ? 'block' : 'none';
        if (this.checked) {
            nameField.querySelector('input').focus();
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
