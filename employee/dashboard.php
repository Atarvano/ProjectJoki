<?php
require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('employee');
require_once __DIR__ . '/../includes/cuti-calculator.php';
require_once __DIR__ . '/../koneksi.php';

$page_class = 'page-dashboard role-employee';

$profile_label = trim((string) ($_SESSION['nama'] ?? ''));
if ($profile_label === '') {
    $profile_label = trim((string) ($_SESSION['username'] ?? 'Karyawan'));
}
if ($profile_label === '') {
    $profile_label = 'Karyawan';
}

$dashboard_context = [
    'role' => 'employee',
    'active_nav' => 'hak-cuti',
    'page_title' => 'Hak Cuti Saya',
    'breadcrumb' => [
        ['label' => 'Dashboard'],
        ['label' => 'Hak Cuti Saya'],
    ],
    'profile_label' => $profile_label,
    'profile_initials' => strtoupper(substr($profile_label, 0, 2)),
    'profile_role' => 'Karyawan',
];

$karyawan_id = isset($_SESSION['karyawan_id']) ? (int) $_SESSION['karyawan_id'] : 0;
$employee_name = $profile_label;
$tahun_bergabung = null;
$hasil = null;
$load_error = null;
$focus_rows = [];
$focus_total_hari = 0;
$tahun_fokus_label = '6, 7, dan 8';
$status_sekarang = 'Belum aktif';
$status_class_sekarang = 'bg-info text-dark';
$tahun_sekarang = (int) date('Y');
$periode_message = 'Ringkasan ini menyorot masa hak cuti utama pada tahun kerja ke-6 sampai ke-8.';

if ($karyawan_id <= 0) {
    $load_error = 'Akun Anda belum terhubung ke data karyawan. Silakan hubungi tim HR.';
} else {
    $stmt = mysqli_prepare($koneksi, 'SELECT nama, tanggal_bergabung FROM karyawan WHERE id = ? LIMIT 1');

    if (!$stmt) {
        $load_error = 'Data karyawan tidak dapat dimuat saat ini. Silakan coba lagi nanti.';
    } else {
        mysqli_stmt_bind_param($stmt, 'i', $karyawan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $karyawan = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$karyawan) {
            $load_error = 'Data karyawan untuk akun ini tidak ditemukan. Silakan hubungi tim HR.';
        } else {
            $employee_name = trim((string) ($karyawan['nama'] ?? $profile_label));
            $tanggal_bergabung = $karyawan['tanggal_bergabung'] ?? '';

            if ($tanggal_bergabung === '' || strtotime($tanggal_bergabung) === false) {
                $load_error = 'Tanggal bergabung pada data Anda belum valid. Silakan hubungi tim HR.';
            } else {
                $tahun_bergabung = (int) date('Y', strtotime($tanggal_bergabung));
                $hasil = hitungHakCuti($tahun_bergabung);
            }
        }
    }
}

if ($hasil) {
    foreach ($hasil['data'] as $row) {
        if (in_array((int) $row['tahun_ke'], [6, 7, 8], true)) {
            $focus_rows[] = $row;
            $focus_total_hari += (int) $row['hari_cuti'];
        }

        if ((int) $row['tahun_kalender'] === $tahun_sekarang) {
            $status_sekarang = $row['status'];
            $status_class_sekarang = $row['status_class'];
        }
    }

    if ($tahun_sekarang < (int) $hasil['tahun_mulai']) {
        $periode_message = 'Hak cuti Anda belum aktif pada tahun ini. Hak cuti pertama mulai berlaku pada tahun ' . $hasil['tahun_mulai'] . '.';
        $status_sekarang = 'Belum aktif';
        $status_class_sekarang = 'bg-info text-dark';
    } elseif ($tahun_sekarang > (int) $hasil['tahun_selesai']) {
        $periode_message = 'Periode 8 tahun pada simulasi ini sudah lewat. Silakan hubungi HR bila Anda memerlukan peninjauan data terbaru.';
        $status_sekarang = 'Periode selesai';
        $status_class_sekarang = 'bg-secondary';
    } elseif ($status_sekarang === 'Menunggu') {
        $periode_message = 'Hak cuti tahun ini sedang berada pada status menunggu dan akan bergeser menjadi tersedia sesuai periode kalender berjalan.';
    } elseif ($status_sekarang === 'Tersedia') {
        $periode_message = 'Hak cuti untuk tahun berjalan sudah tersedia. Gunakan ringkasan ini sebagai acuan saat berkoordinasi dengan HR.';
    }
}

ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="gradient-card border-0 mb-4 overflow-hidden" style="background-color: var(--color-surface);">
            <div class="card-body p-4 p-md-5 position-relative z-1 d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div>
                    <h1 class="hero-title mb-2 text-primary-dark" style="font-size: clamp(2rem, 4vw, 3rem); font-family: var(--font-display); font-weight: 800;">
                        Hak Cuti <span class="text-accent">Saya</span>
                    </h1>
                    <p class="hero-subtitle mb-0 text-secondary" style="font-size: 1.125rem; max-width: 550px;">
                        Ringkasan hak cuti Anda ditampilkan otomatis berdasarkan data kepegawaian akun yang sedang login.
                    </p>
                </div>

                <div class="d-none d-md-block mt-4 mt-md-0" style="opacity: 0.9;">
                    <svg viewBox="0 0 120 100" width="120" height="100" aria-hidden="true">
                        <rect x="20" y="20" width="80" height="60" rx="6" fill="var(--color-primary-subtle)" />
                        <rect x="20" y="20" width="80" height="15" rx="4" fill="var(--color-primary)" />
                        <rect x="30" y="45" width="60" height="8" rx="2" fill="var(--color-surface)" />
                        <circle cx="40" cy="49" r="2" fill="var(--color-text-muted)" />
                        <polygon points="65,55 75,70 70,72 75,80 72,82 67,73 60,78" fill="var(--color-accent)" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-0">
    <?php if ($load_error !== null): ?>
        <div class="alert alert-warning shadow-sm border-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($load_error); ?>
        </div>
    <?php else: ?>
        <div class="dashboard-stat-card mb-4" style="background-color: var(--color-primary-subtle); border-color: var(--color-info);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-primary-dark fw-bold" style="font-family: var(--font-display);"><?php echo htmlspecialchars($employee_name); ?></h4>
                        <div class="text-secondary">Bergabung: <strong><?php echo htmlspecialchars((string) $tahun_bergabung); ?></strong></div>
                    </div>
                </div>
                    <div class="d-flex gap-4 align-items-center">
                        <div class="text-end">
                            <div class="small text-secondary fw-semibold mb-1 text-uppercase">Status Tahun Ini</div>
                            <span class="badge rounded-pill <?php echo $status_class_sekarang; ?> fs-6"><?php echo htmlspecialchars($status_sekarang); ?></span>
                        </div>
                        <div class="border-start border-secondary border-opacity-25 ps-4 text-end">
                            <div class="small text-secondary fw-semibold mb-1 text-uppercase">Total Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?></div>
                            <h3 class="mb-0 text-primary fw-bold" style="font-family: var(--font-display);"><?php echo (int) $focus_total_hari; ?> <small class="text-secondary fs-6 fw-normal">hari</small></h3>
                        </div>
                    </div>
                </div>
        </div>

        <div class="alert alert-info mb-4 border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i>
            <?php echo htmlspecialchars($periode_message); ?>
        </div>

        <div class="dashboard-table-shell mb-4">
            <div class="dashboard-table-header">
                <div>
                    <h5 class="mb-1 text-primary-dark fw-bold" style="font-family: var(--font-display);">Fokus Hak Cuti Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?></h5>
                    <p class="mb-0 text-secondary small">Tabel ini menampilkan masa yang paling relevan untuk ringkasan hak cuti Anda saat ini.</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">No.</th>
                            <th>Tahun Kalender</th>
                            <th class="text-center">Hari Cuti</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($focus_rows === []): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary">
                                    Data fokus tahun 6 sampai 8 belum tersedia untuk ditampilkan dari riwayat kerja Anda saat ini.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($focus_rows as $row): ?>
                            <tr>
                                <td class="text-center text-muted"><?php echo $row['tahun_ke']; ?></td>
                                <td class="fw-bold"><?php echo $row['tahun_kalender']; ?></td>
                                <td class="text-center">
                                    <span class="fs-5 text-primary fw-bold"><?php echo $row['hari_cuti']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo $row['status_class']; ?> fw-semibold px-3 py-2">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-center border-top bg-light">
                <p class="employee-support-hint text-muted small mb-0 fw-medium">
                    <i class="bi bi-question-circle-fill me-1 text-primary"></i>Ringkasan ini otomatis mengikuti data kepegawaian akun Anda. Jika ada data yang perlu diperbarui, silakan hubungi tim HR.
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$page_content = ob_get_clean();
require __DIR__ . '/../includes/dashboard-layout.php';
