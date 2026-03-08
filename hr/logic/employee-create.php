<?php
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
        $check_nik_sql = 'SELECT id FROM karyawan WHERE nik = ? LIMIT 1';
        $check_nik_stmt = mysqli_prepare($koneksi, $check_nik_sql);

        if ($check_nik_stmt) {
            mysqli_stmt_bind_param($check_nik_stmt, 's', $old['nik']);
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
        $insert_sql = 'INSERT INTO karyawan (nama, nik, jabatan, tanggal_bergabung, tanggal_lahir, email, telepon, alamat, departemen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $insert_stmt = mysqli_prepare($koneksi, $insert_sql);

        if ($insert_stmt) {
            $jabatan_value = $old['jabatan'] !== '' ? $old['jabatan'] : null;
            $email_value = $old['email'] !== '' ? $old['email'] : null;
            $telepon_value = $old['telepon'] !== '' ? $old['telepon'] : null;
            $alamat_value = $old['alamat'] !== '' ? $old['alamat'] : null;
            $departemen_value = $old['departemen'] !== '' ? $old['departemen'] : null;

            mysqli_stmt_bind_param(
                $insert_stmt,
                'sssssssss',
                $old['nama'],
                $old['nik'],
                $jabatan_value,
                $old['tanggal_bergabung'],
                $old['tanggal_lahir'],
                $email_value,
                $telepon_value,
                $alamat_value,
                $departemen_value
            );

            $insert_ok = mysqli_stmt_execute($insert_stmt);
            $new_employee_id = mysqli_insert_id($koneksi);
            mysqli_stmt_close($insert_stmt);

            if ($insert_ok && $new_employee_id > 0) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Data karyawan berhasil ditambahkan. Silakan cek detail karyawan untuk review data yang baru disimpan.',
                ];
                header('Location: /hr/employee-detail.php?id=' . $new_employee_id);
                exit;
            }

            $summary_error = 'Data belum berhasil disimpan. Silakan cek lagi lalu coba ulang.';
        } else {
            $summary_error = 'Form belum bisa diproses sekarang. Silakan coba lagi sebentar.';
        }
    }

    if (!empty($errors) && $summary_error === '') {
        $summary_error = 'Mohon periksa kembali data yang ditandai agar bisa disimpan.';
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
    'page_title' => 'Create Employee - Sicuti HRD',
    'breadcrumb' => [
        ['label' => 'Dashboard HR', 'url' => '/hr/dashboard.php'],
        ['label' => 'Employees', 'url' => '/hr/employees.php'],
        ['label' => 'Add Employee', 'url' => '#'],
    ],
    'profile_label' => $profile_label,
    'profile_initials' => $profile_initials,
    'profile_role' => 'HR',
];

ob_start();
require __DIR__ . '/../views/employee-create.php';
$page_content = ob_get_clean();
