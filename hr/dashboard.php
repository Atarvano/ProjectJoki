<?php
require_once __DIR__ . '/../includes/cuti-calculator.php';
require_once __DIR__ . '/../includes/reports-data.php';

$reports = getReports() ?? [];
$preset_count = countPresetKaryawan();
$report_count = countReports();

$current_year = date('Y');
$tersedia_count = 0;
foreach ($reports as $report) {
    $calc = hitungHakCuti($report['tahun_bergabung']);
    foreach ($calc['data'] as $row) {
        if ($row['tahun_kalender'] == $current_year && strpos($row['status'], 'Tersedia') !== false) {
            $tersedia_count++;
            break;
        }
    }
}

$dashboard_context = [
    'role' => 'hr',
    'active_nav' => 'dashboard',
    'page_title' => 'Dashboard HR - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '#']
    ],
    'profile_label' => 'Admin HR',
    'profile_initials' => 'HR',
    'demo_badge' => 'Demo v1',
];

ob_start();
?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden">
            <div class="row g-0 align-items-center">
                <div class="col-md-8 p-4 p-md-5">
                    <h2 class="fw-bold mb-3">Selamat datang di Sicuti HRD</h2>
                    <p class="mb-4 lead opacity-75">
                        Anda berada dalam Mode Demo. Kelola hak cuti karyawan, simpan laporan sesi, dan unduh rekap data dalam format Excel.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="kalkulator.php" class="btn btn-light btn-lg px-4 fw-medium text-primary">
                            <i class="bi bi-calculator me-2"></i> Hitung Hak Cuti
                        </a>
                        <a href="laporan.php" class="btn btn-outline-light btn-lg px-4 fw-medium">
                            <i class="bi bi-journal-text me-2"></i> Kelola Laporan
                        </a>
                    </div>
                </div>
                <div class="col-md-4 d-none d-md-flex justify-content-center align-items-center p-4">
                    <svg width="200" height="200" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.8;">
                        <path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 11V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 8V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 13V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 16H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold text-uppercase tracking-wider small">Total Karyawan (Demo)</div>
                    <div class="text-primary bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 display-6"><?php echo $preset_count; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold text-uppercase tracking-wider small">Laporan Tersimpan</div>
                    <div class="text-success bg-success bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-file-earmark-check fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 display-6"><?php echo $report_count; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold text-uppercase tracking-wider small">Tersedia Tahun Ini</div>
                    <div class="text-warning bg-warning bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 display-6"><?php echo $tersedia_count; ?> <span class="fs-5 fw-normal text-muted">orang</span></h3>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>