<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../includes/cuti-calculator.php';
require_once __DIR__ . '/../includes/reports-data.php';

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_reports') {
    resetReports();
    $_SESSION['flash'] = ['type' => 'info', 'message' => 'Semua laporan telah direset.'];
    header('Location: laporan.php');
    exit;
}

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$reports = getReports() ?? [];
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

    $report['current_status'] = $current_status;
    $report['current_status_class'] = $current_status_class;
    $report['original_index'] = $index;

    if (empty($filter_status) || $current_status === $filter_status) {
        $report_display[] = $report;
    }
}

$profile_label = trim((string) ($_SESSION['nama'] ?? ''));
if ($profile_label === '') {
    $profile_label = trim((string) ($_SESSION['username'] ?? 'HR'));
}
if ($profile_label === '') {
    $profile_label = 'HR';
}
$profile_initials = strtoupper(substr($profile_label, 0, 2));

$dashboard_context = [
    'role' => 'hr',
    'active_nav' => 'laporan',
    'page_title' => 'Laporan Hak Cuti - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => 'dashboard.php'],
        ['label' => 'Laporan Hak Cuti', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="h3 mb-0 text-gray-800">Laporan Cuti Karyawan</h2>
    
    <div class="d-flex gap-2 flex-wrap">
        <form method="GET" class="d-inline-block">
            <select name="filter_status" class="form-select border-secondary text-secondary fw-medium" onchange="this.form.submit()" style="width: auto;">
                <option value="">Semua Status</option>
                <option value="Tersedia" <?php echo $filter_status === 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                <option value="Menunggu" <?php echo $filter_status === 'Menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                <option value="Rencana" <?php echo $filter_status === 'Rencana' ? 'selected' : ''; ?>>Rencana</option>
            </select>
        </form>
        
        <?php if (count($reports) > 0): ?>
            <a href="export.php" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
        <?php else: ?>
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                  title="Simpan hasil kalkulator terlebih dahulu untuk mengekspor">
                <a class="btn btn-success disabled" style="pointer-events: none;" aria-disabled="true">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                </a>
            </span>
        <?php endif; ?>

        <?php if (count($reports) > 0): ?>
            <form method="POST" class="d-inline-block" onsubmit="return confirm('Semua data laporan akan dihapus dan export tidak tersedia sampai Anda menyimpan hasil kalkulator baru. Lanjutkan?');">
                <input type="hidden" name="action" value="reset_reports">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show mb-4 shadow-sm border-0">
        <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle-fill text-success' : ($flash['type'] === 'danger' ? 'exclamation-circle-fill text-danger' : 'info-circle-fill text-info'); ?> me-2 fs-5 align-middle"></i>
        <span class="align-middle"><?php echo htmlspecialchars($flash['message']); ?></span>
        <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom p-4">
        <div class="d-flex align-items-center gap-2 text-muted">
            <i class="bi bi-info-circle text-primary"></i>
            <small>Data laporan menampilkan hasil perhitungan hak cuti terbaru. Gunakan fitur Export Excel untuk menyimpan rekap data.</small>
        </div>
    </div>
    
    <div class="card-body p-0">
        <?php if (count($reports) === 0 || empty($report_display)): ?>
            <div class="p-5 text-center text-muted bg-light border-bottom">
                <div class="mb-3">
                    <i class="bi bi-folder2-open text-secondary opacity-50" style="font-size: 4rem;"></i>
                </div>
                <h4 class="h5 mb-2">Tidak Ada Data Laporan</h4>
                <p class="mb-4">
                    <?php if (count($reports) === 0): ?>
                        Anda belum menyimpan laporan kalkulator hak cuti.
                    <?php else: ?>
                        Tidak ada laporan yang sesuai dengan filter status "<?php echo htmlspecialchars($filter_status); ?>".
                    <?php endif; ?>
                </p>
                <?php if (count($reports) === 0): ?>
                    <a href="kalkulator.php" class="btn btn-primary px-4">
                        <i class="bi bi-calculator me-2"></i>Buka Kalkulator
                    </a>
                <?php else: ?>
                    <a href="laporan.php" class="btn btn-outline-secondary px-4">
                        Hapus Filter
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="text-center py-3 ps-4" style="width: 60px;">#</th>
                            <th class="py-3">Karyawan</th>
                            <th class="text-center py-3">Bergabung</th>
                            <th class="text-center py-3">Hak Cuti 8 Tahun</th>
                            <th class="text-center py-3">Status Tahun Ini</th>
                            <th class="text-center py-3 pe-4" style="width: 140px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($report_display as $report):
                            ?>
                            <tr>
                                <td class="text-center text-muted fw-medium ps-4"><?php echo $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3 py-1">
                                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <span class="fw-bold fs-6"><?php echo strtoupper(substr(htmlspecialchars($report['nama']), 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($report['nama']); ?></div>
                                            <div class="text-muted small d-flex align-items-center gap-1">
                                                <span class="badge bg-secondary opacity-75 fw-normal rounded-pill" style="font-size: 0.65rem;">Data Laporan</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-medium"><?php echo $report['tahun_bergabung']; ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-bold">
                                        <?php echo $report['total_cuti']; ?> Hari
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo $report['current_status_class']; ?> px-3 py-2">
                                        <?php echo $report['current_status']; ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#detailModal-<?php echo $report['original_index']; ?>">
                                        <i class="bi bi-eye me-2"></i>Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php foreach ($report_display as $report):
    $detailCalc = hitungHakCuti($report['tahun_bergabung']);
?>
<!-- Modal Detail for <?php echo htmlspecialchars($report['nama']); ?> -->
<div class="modal fade" id="detailModal-<?php echo $report['original_index']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body pt-0 px-4 px-md-5">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 64px; height: 64px;">
                        <span class="fw-bold fs-2"><?php echo strtoupper(substr(htmlspecialchars($report['nama']), 0, 1)); ?></span>
                    </div>
                    <h4 class="mb-1"><?php echo htmlspecialchars($report['nama']); ?></h4>
                    <div class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                        Data Laporan
                    </div>
                </div>

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body d-flex justify-content-around text-center py-3">
                        <div>
                            <div class="text-muted small mb-1">Tahun Bergabung</div>
                            <div class="fw-bold fs-5 text-dark"><?php echo $detailCalc['tahun_bergabung']; ?></div>
                        </div>
                        <div class="border-end border-secondary opacity-25"></div>
                        <div>
                            <div class="text-muted small mb-1">Periode Perhitungan</div>
                            <div class="fw-bold fs-5 text-dark"><?php echo $detailCalc['tahun_mulai']; ?> - <?php echo $detailCalc['tahun_selesai']; ?></div>
                        </div>
                        <div class="border-end border-secondary opacity-25"></div>
                        <div>
                            <div class="text-muted small mb-1">Total Hak Cuti</div>
                            <div class="fw-bold fs-5 text-primary"><?php echo $report['total_cuti']; ?> Hari</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-2">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Detail Hak Cuti 8 Tahun</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table entitlement-table align-middle mb-0">
                                <thead class="table-light text-muted small">
                                    <tr>
                                        <th class="text-center py-2 ps-3" style="width: 80px;">Tahun</th>
                                        <th class="py-2">Tahun Kalender</th>
                                        <th class="text-center py-2">Jumlah Hari</th>
                                        <th class="text-center py-2 pe-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($detailCalc['data'] as $row): ?>
                                        <tr class="<?php echo ($row['tahun_kalender'] == date('Y')) ? 'table-primary bg-opacity-10' : ''; ?>">
                                            <td class="text-center text-muted fw-medium ps-3">Ke-<?php echo $row['tahun_ke']; ?></td>
                                            <td class="fw-bold"><?php echo $row['tahun_kalender']; ?></td>
                                            <td class="text-center">
                                                <span class="fs-5 text-primary fw-bold"><?php echo $row['hari_cuti']; ?></span>
                                            </td>
                                            <td class="text-center pe-3">
                                                <span class="badge rounded-pill <?php echo $row['status_class']; ?> px-3">
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
            
            <div class="modal-footer border-top-0 pt-0 px-4 px-md-5 pb-4">
                <button type="button" class="btn btn-secondary px-4 w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(el) { new bootstrap.Tooltip(el); });
});
</script>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>
