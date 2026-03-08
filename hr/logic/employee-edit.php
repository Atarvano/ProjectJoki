<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
}

if ($id <= 0) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Silakan pilih data dari daftar karyawan.',
    ];
    header('Location: /hr/employees.php');
    exit;
}

$errors = [];
$summary_error = '';

$old = [
    'nama' => '',
    'nik' => '',
    'jabatan' => '',
    'tanggal_bergabung' => '',
    'tanggal_lahir' => '',
    'email' => '',
    'telepon' => '',
    'alamat' => '',
    'departemen' => '',
];

$load_sql = 'SELECT id, nama, nik, jabatan, tanggal_bergabung, tanggal_lahir, email, telepon, alamat, departemen FROM karyawan WHERE id = ? LIMIT 1';
$load_stmt = mysqli_prepare($koneksi, $load_sql);

if (!$load_stmt) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Halaman edit belum bisa dibuka sekarang. Silakan coba lagi.',
    ];
    header('Location: /hr/employees.php');
    exit;
}

mysqli_stmt_bind_param($load_stmt, 'i', $id);
mysqli_stmt_execute($load_stmt);
$load_result = mysqli_stmt_get_result($load_stmt);
$existing = $load_result ? mysqli_fetch_assoc($load_result) : null;
mysqli_stmt_close($load_stmt);

if (!$existing) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Data karyawan tidak ditemukan. Mungkin sudah dihapus sebelumnya.',
    ];
    header('Location: /hr/employees.php');
    exit;
}

foreach ($old as $field => $value) {
    $old[$field] = (string) ($existing[$field] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $field => $value) {
        $old[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if ($old['nama'] === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    }

    if ($old['nik'] === '') {
        $errors['nik'] = 'NIK wajib diisi.';
    }

    if ($old['tanggal_bergabung'] === '') {
        $errors['tanggal_bergabung'] = 'Tanggal bergabung wajib diisi.';
    }

    if ($old['tanggal_lahir'] === '') {
        $errors['tanggal_lahir'] = 'Tanggal lahir wajib diisi.';
    }

    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email belum benar. Contoh: nama@perusahaan.com';
    }

    if ($old['nik'] !== '') {
        $check_nik_sql = 'SELECT id FROM karyawan WHERE nik = ? AND id != ? LIMIT 1';
        $check_nik_stmt = mysqli_prepare($koneksi, $check_nik_sql);

        if ($check_nik_stmt) {
            mysqli_stmt_bind_param($check_nik_stmt, 'si', $old['nik'], $id);
            mysqli_stmt_execute($check_nik_stmt);
            $check_nik_result = mysqli_stmt_get_result($check_nik_stmt);

            if ($check_nik_result && mysqli_num_rows($check_nik_result) > 0) {
                $errors['nik'] = 'NIK sudah dipakai karyawan lain. Silakan gunakan NIK yang berbeda.';
            }

            mysqli_stmt_close($check_nik_stmt);
        } else {
            $summary_error = 'Validasi NIK sedang bermasalah. Silakan coba lagi.';
        }
    }

    if (empty($errors) && $summary_error === '') {
        $update_sql = 'UPDATE karyawan SET nama = ?, nik = ?, jabatan = ?, tanggal_bergabung = ?, tanggal_lahir = ?, email = ?, telepon = ?, alamat = ?, departemen = ? WHERE id = ?';
        $update_stmt = mysqli_prepare($koneksi, $update_sql);

        if ($update_stmt) {
            $jabatan_value = $old['jabatan'] !== '' ? $old['jabatan'] : null;
            $email_value = $old['email'] !== '' ? $old['email'] : null;
            $telepon_value = $old['telepon'] !== '' ? $old['telepon'] : null;
            $alamat_value = $old['alamat'] !== '' ? $old['alamat'] : null;
            $departemen_value = $old['departemen'] !== '' ? $old['departemen'] : null;

            mysqli_stmt_bind_param(
                $update_stmt,
                'sssssssssi',
                $old['nama'],
                $old['nik'],
                $jabatan_value,
                $old['tanggal_bergabung'],
                $old['tanggal_lahir'],
                $email_value,
                $telepon_value,
                $alamat_value,
                $departemen_value,
                $id
            );

            $update_ok = mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);

            if ($update_ok) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Data karyawan berhasil diperbarui. Silakan cek kembali pada daftar karyawan.',
                ];
                header('Location: /hr/employees.php');
                exit;
            }

            $summary_error = 'Perubahan belum berhasil disimpan. Silakan cek lagi lalu coba ulang.';
        } else {
            $summary_error = 'Form edit belum bisa diproses sekarang. Silakan coba lagi sebentar.';
        }
    }

    if (!empty($errors) && $summary_error === '') {
        $summary_error = 'Mohon periksa kembali data yang ditandai agar perubahan bisa disimpan.';
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
    'page_title' => 'Edit Karyawan - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Employees', 'url' => '/hr/employees.php'],
        ['label' => 'Edit Karyawan', 'url' => '#'],
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
require __DIR__ . '/../views/employee-edit.php';
$page_content = ob_get_clean();
