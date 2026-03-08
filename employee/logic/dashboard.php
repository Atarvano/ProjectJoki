<?php
require_once __DIR__ . '/../../includes/cuti-calculator.php';

$page_class = 'page-dashboard role-employee';

$profile_label = trim((string) ($_SESSION['nama'] ?? ''));
if ($profile_label === '') {
    $profile_label = trim((string) ($_SESSION['username'] ?? 'Karyawan'));
}
if ($profile_label === '') {
    $profile_label = 'Karyawan';
}

$profile_initials = strtoupper(substr($profile_label, 0, 2));
if ($profile_initials === '') {
    $profile_initials = 'KR';
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
    'profile_initials' => $profile_initials,
    'profile_role' => 'Karyawan',
];

$karyawan_id = isset($_SESSION['karyawan_id']) ? (int) $_SESSION['karyawan_id'] : 0;
$employee_name = $profile_label;
$employee_join_date_label = '-';
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

if ($karyawan_id > 0) {
    $stmt = mysqli_prepare($koneksi, 'SELECT nama, tanggal_bergabung FROM karyawan WHERE id = ? LIMIT 1');

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $karyawan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $karyawan = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($karyawan) {
            $employee_name = trim((string) ($karyawan['nama'] ?? $profile_label));
            $tanggal_bergabung = $karyawan['tanggal_bergabung'] ?? '';

            if ($employee_name === '') {
                $employee_name = $profile_label;
            }

            if ($tanggal_bergabung !== '' && strtotime($tanggal_bergabung) !== false) {
                $employee_join_date_label = date('d M Y', strtotime($tanggal_bergabung));
            }

            if ($tanggal_bergabung === '' || strtotime($tanggal_bergabung) === false) {
                $load_error = 'Tanggal bergabung pada data Anda belum valid. Silakan hubungi tim HR.';
            } else {
                $tahun_bergabung = (int) date('Y', strtotime($tanggal_bergabung));
                $hasil = hitungHakCuti($tahun_bergabung);
            }
        } else {
            $load_error = 'Data karyawan Anda belum ditemukan. Silakan hubungi tim HR.';
        }
    } else {
        $load_error = 'Data karyawan Anda belum ditemukan. Silakan hubungi tim HR.';
    }
} else {
    $load_error = 'Data karyawan Anda belum ditemukan. Silakan hubungi tim HR.';
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
require __DIR__ . '/../views/dashboard.php';
$page_content = ob_get_clean();
