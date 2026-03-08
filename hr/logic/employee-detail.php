<?php
require_once __DIR__ . '/../../includes/cuti-calculator.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Silakan pilih data dari daftar karyawan.'
    ];
    header("Location: /hr/employees.php");
    exit;
}

$employee = null;
$query = 'SELECT k.id, k.nama, k.nik, k.jabatan, k.tanggal_bergabung, k.tanggal_lahir, k.email, k.telepon, k.alamat, k.departemen, u.id AS user_id
          FROM karyawan k
          LEFT JOIN users u ON u.karyawan_id = k.id
          WHERE k.id = ?
          LIMIT 1';
$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $employee = mysqli_fetch_assoc($result);
    }

    mysqli_stmt_close($stmt);
}

if (!$employee) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan atau sudah dihapus.'
    ];
    header("Location: /hr/employees.php");
    exit;
}

$account_status = !empty($employee['user_id']) ? 'Sudah ada' : 'Belum dibuat';

$from = isset($_GET['from']) ? trim((string) $_GET['from']) : 'employees';
if (!in_array($from, ['employees', 'reports', 'dashboard'], true)) {
    $from = 'employees';
}

$back_url = '/hr/employees.php';
$back_label = 'Kembali ke Daftar';

if ($from === 'reports') {
    $back_url = '/hr/reports.php';
    $back_label = 'Kembali ke Laporan';
}

if ($from === 'dashboard') {
    $back_url = '/hr/dashboard.php';
    $back_label = 'Kembali ke Dashboard';
}

$leave_error = '';
$leave_note = 'Hak cuti di bawah ini dihitung dari tanggal bergabung dan engine cuti yang sama dengan halaman lain.';
$leave_snapshot = [
    'title' => 'Data hak cuti belum siap',
    'text' => 'Periksa tanggal bergabung karyawan untuk melihat ringkasan hak cuti.'
];
$leave_rows = [];

$join_date = trim((string) ($employee['tanggal_bergabung'] ?? ''));
$join_timestamp = false;

if ($join_date === '') {
    $leave_error = 'Tanggal bergabung belum diisi, jadi ringkasan hak cuti belum bisa ditampilkan.';
} else {
    $join_timestamp = strtotime($join_date);

    if ($join_timestamp === false) {
        $leave_error = 'Tanggal bergabung tidak valid, jadi ringkasan hak cuti belum bisa ditampilkan.';
    }
}

if ($leave_error === '') {
    $join_year = (int) date('Y', $join_timestamp);
    $leave_result = hitungHakCuti($join_year);
    $current_year = (int) date('Y');
    $current_row = null;
    $next_row = null;

    if (isset($leave_result['data']) && is_array($leave_result['data'])) {
        foreach ($leave_result['data'] as $row) {
            if (!is_array($row)) {
                continue;
            }

            if (in_array((int) $row['tahun_ke'], [6, 7, 8], true)) {
                $leave_rows[] = $row;
            }

            if ((int) ($row['tahun_kalender'] ?? 0) === $current_year) {
                $current_row = $row;
            }

            if ($next_row === null && (int) ($row['tahun_kalender'] ?? 0) >= $current_year) {
                $next_row = $row;
            }
        }
    }

    if ($current_row) {
        $leave_snapshot = [
            'title' => 'Hak cuti tahun ini: ' . (int) ($current_row['hari_cuti'] ?? 0) . ' hari',
            'text' => 'Status saat ini: ' . (string) ($current_row['status'] ?? '-') . ' pada kalender ' . (int) ($current_row['tahun_kalender'] ?? 0) . '.'
        ];
    } elseif ($next_row) {
        $leave_snapshot = [
            'title' => 'Hak cuti berikutnya: ' . (int) ($next_row['hari_cuti'] ?? 0) . ' hari',
            'text' => 'Baris terdekat ada pada kalender ' . (int) ($next_row['tahun_kalender'] ?? 0) . ' dengan status ' . (string) ($next_row['status'] ?? '-') . '.'
        ];
    } elseif (!empty($leave_rows)) {
        $first_row = $leave_rows[0];
        $leave_snapshot = [
            'title' => 'Hak cuti fokus detail sudah disiapkan',
            'text' => 'Review dimulai dari Tahun ke-' . (int) ($first_row['tahun_ke'] ?? 0) . ' pada kalender ' . (int) ($first_row['tahun_kalender'] ?? 0) . '.'
        ];
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
    'active_nav' => 'kelola-karyawan',
    'page_title' => 'Employee Detail - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Employees', 'url' => $back_url],
        ['label' => 'Detail Karyawan', 'url' => '#']
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

$fields = [
    'nama' => 'Nama',
    'nik' => 'NIK',
    'jabatan' => 'Jabatan',
    'tanggal_bergabung' => 'Tanggal Bergabung',
    'tanggal_lahir' => 'Tanggal Lahir',
    'email' => 'Email',
    'telepon' => 'Telepon',
    'alamat' => 'Alamat',
    'departemen' => 'Departemen',
];

ob_start();
require __DIR__ . '/../views/employee-detail.php';
$page_content = ob_get_clean();
