<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../includes/cuti-calculator.php';
require_once __DIR__ . '/../koneksi.php';

$total_karyawan = 0;
$total_akun_employee = 0;
$siap_cuti_tahun_ini = 0;
$stat_note = 'Ringkasan dihitung langsung dari data karyawan dan akun login yang sudah tersimpan di sistem.';

$total_karyawan_result = mysqli_query($koneksi, 'SELECT COUNT(*) AS total FROM karyawan');
if ($total_karyawan_result instanceof mysqli_result) {
    $total_karyawan_row = mysqli_fetch_assoc($total_karyawan_result);
    $total_karyawan = isset($total_karyawan_row['total']) ? (int) $total_karyawan_row['total'] : 0;
    mysqli_free_result($total_karyawan_result);
}

$total_akun_result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role = 'employee' AND karyawan_id IS NOT NULL");
if ($total_akun_result instanceof mysqli_result) {
    $total_akun_row = mysqli_fetch_assoc($total_akun_result);
    $total_akun_employee = isset($total_akun_row['total']) ? (int) $total_akun_row['total'] : 0;
    mysqli_free_result($total_akun_result);
}

$current_year = (int) date('Y');
$karyawan_result = mysqli_query($koneksi, 'SELECT id, nama, tanggal_bergabung FROM karyawan ORDER BY nama ASC');

if ($karyawan_result instanceof mysqli_result) {
    while ($karyawan = mysqli_fetch_assoc($karyawan_result)) {
        $tanggal_bergabung = $karyawan['tanggal_bergabung'] ?? '';

        if ($tanggal_bergabung === '' || strtotime($tanggal_bergabung) === false) {
            continue;
        }

        $tahun_bergabung = (int) date('Y', strtotime($tanggal_bergabung));
        $hasil_cuti = hitungHakCuti($tahun_bergabung);

        foreach ($hasil_cuti['data'] as $row) {
            if ((int) $row['tahun_kalender'] === $current_year && in_array($row['status'], ['Tersedia', 'Menunggu'], true)) {
                $siap_cuti_tahun_ini++;
                break;
            }
        }
    }

    mysqli_free_result($karyawan_result);
}

if ($total_karyawan === 0) {
    $stat_note = 'Belum ada data karyawan. Tambahkan data karyawan lebih dulu agar ringkasan dashboard terisi otomatis.';
} elseif ($total_akun_employee === 0) {
    $stat_note = 'Data karyawan sudah tersedia. Buat akun login karyawan agar akses employee dashboard dapat digunakan.';
} elseif ($siap_cuti_tahun_ini === 0) {
    $stat_note = 'Belum ada karyawan yang masuk status siap cuti tahun ini. Periksa kembali data tanggal bergabung bila diperlukan.';
}

$siap_cuti_copy = 'Status Tersedia atau Menunggu pada tahun kalender ' . $current_year;

$hero_copy = 'Pantau jumlah karyawan aktif, akun employee yang sudah diprovisikan, dan kesiapan cuti tahun ini dari data yang tersimpan saat ini.';

$helper_cards = [
    [
        'label' => 'Data Karyawan',
        'text' => 'Kelola data induk karyawan sebagai dasar seluruh perhitungan dan akses login.',
        'link' => 'karyawan.php',
        'icon' => 'bi-people',
    ],
    [
        'label' => 'Periksa Hak Cuti',
        'text' => 'Buka kalkulator untuk melihat rincian hak cuti berdasarkan data tanggal bergabung karyawan.',
        'link' => 'kalkulator.php',
        'icon' => 'bi-calendar-check',
    ],
    [
        'label' => 'Lihat Rekap',
        'text' => 'Gunakan halaman laporan untuk meninjau ringkasan hak cuti semua karyawan dari database.',
        'link' => 'laporan.php',
        'icon' => 'bi-journal-text',
    ],
];

$dashboard_context = [
    'role' => 'hr',
    'active_nav' => 'dashboard',
    'page_title' => 'Dashboard HR - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '#']
    ],
    'profile_label' => $_SESSION['nama'],
    'profile_initials' => strtoupper(substr($_SESSION['nama'], 0, 2)),
];

ob_start();
?>
<div class="row mb-4">
    <div class="col-12">
        <div class="gradient-card border-0 overflow-hidden" style="background-color: var(--color-surface);">
            <div class="row g-0 align-items-center">
                <div class="col-md-8 p-4 p-md-5">
                    <h1 class="hero-title mb-2 text-primary-dark" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 800;">Selamat datang di <span class="text-accent">Sicuti HRD</span></h1>
                    <p class="hero-subtitle mb-4 text-secondary" style="font-size: 1.125rem;">
                        <?php echo htmlspecialchars($hero_copy); ?>
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="kalkulator.php" class="btn btn-primary btn-lg px-4" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light)); border: none; box-shadow: 0 4px 15px rgba(15, 76, 92, 0.3); font-weight: 700;">
                            <i class="bi bi-calculator me-2"></i> Hitung Hak Cuti
                        </a>
                        <a href="laporan.php" class="btn btn-outline-primary btn-lg px-4 fw-bold">
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
        <div class="dashboard-stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold text-uppercase tracking-wider small">Total Karyawan</div>
                    <div class="dashboard-stat-icon">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 display-6"><?php echo $total_karyawan; ?></h3>
                <p class="text-secondary small mb-0 mt-2">Data pegawai yang saat ini tersimpan pada tabel karyawan.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold text-uppercase tracking-wider small">Akun Employee</div>
                    <div class="dashboard-stat-icon" style="color: var(--color-success); background-color: rgba(5, 150, 105, 0.1);">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 display-6"><?php echo $total_akun_employee; ?></h3>
                <p class="text-secondary small mb-0 mt-2">Akun login karyawan dengan role <code>employee</code> yang sudah terhubung ke data pegawai.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dashboard-stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted fw-semibold text-uppercase tracking-wider small">Siap Cuti Tahun Ini</div>
                    <div class="dashboard-stat-icon" style="color: var(--color-warning); background-color: rgba(217, 119, 6, 0.1);">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 display-6"><?php echo $siap_cuti_tahun_ini; ?> <span class="fs-5 fw-normal text-muted">orang</span></h3>
                <p class="text-secondary small mb-0 mt-2"><?php echo htmlspecialchars($siap_cuti_copy); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-stat-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
            <div class="dashboard-stat-icon mt-1" style="width: 46px; height: 46px;">
                <i class="bi bi-info-circle fs-4"></i>
            </div>
            <div>
                <h2 class="h5 fw-bold text-primary-dark mb-2">Ringkasan Data Saat Ini</h2>
                <p class="text-secondary mb-0"><?php echo htmlspecialchars($stat_note); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($helper_cards as $card): ?>
        <div class="col-md-4">
            <div class="dashboard-stat-card h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="dashboard-stat-icon">
                            <i class="bi <?php echo htmlspecialchars($card['icon']); ?> fs-4"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0 text-primary-dark"><?php echo htmlspecialchars($card['label']); ?></h2>
                    </div>
                    <p class="text-secondary flex-grow-1 mb-3"><?php echo htmlspecialchars($card['text']); ?></p>
                    <a href="<?php echo htmlspecialchars($card['link']); ?>" class="btn btn-outline-primary fw-semibold align-self-start">
                        Buka Halaman
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
?>
