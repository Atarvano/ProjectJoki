<?php

$endpoint_path = __DIR__ . '/../hr/karyawan-provision.php';

function assert_true($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

assert_true(file_exists($endpoint_path), 'Endpoint file hr/karyawan-provision.php harus ada.');

define('KARYAWAN_PROVISION_TEST_MODE', true);
require $endpoint_path;

assert_true(function_exists('build_provision_password'), 'Fungsi build_provision_password() harus tersedia.');

$password = build_provision_password('EMP001', '1990-12-31');
assert_true($password === 'EMP00131121990', 'Rumus password harus NIK + DDMMYYYY(tanggal_lahir).');

$invalid_password = build_provision_password('EMP001', '31-12-1990');
assert_true($invalid_password === null, 'Tanggal lahir tidak valid harus menghasilkan null.');

$content = file_get_contents($endpoint_path);
assert_true(strpos($content, "REQUEST_METHOD'] !== 'POST'") !== false, 'Endpoint wajib POST-only.');
assert_true(strpos($content, 'Aksi hanya bisa lewat tombol Buat Akun Login.') !== false, 'Copy penolakan non-POST harus informatif dan konsisten.');
assert_true(strpos($content, 'password_hash') !== false, 'Endpoint wajib menggunakan password_hash().');
assert_true(strpos($content, 'INSERT INTO users') !== false, 'Endpoint wajib memiliki INSERT INTO users.');
assert_true(strpos($content, "role, is_active") !== false, 'Insert wajib set role dan is_active.');
assert_true(strpos($content, "employee") !== false, 'Role akun baru wajib employee.');
assert_true(strpos($content, "NIK (Username)") !== false, 'Flash sukses wajib memuat label NIK (Username).');
assert_true(strpos($content, "Password awal") !== false, 'Flash sukses wajib memuat label Password awal.');
assert_true(strpos($content, "ditampilkan sekali") !== false, 'Flash sukses wajib memberi warning sekali tampil.');
assert_true(strpos($content, "header('Location: /hr/karyawan.php')") !== false, 'Endpoint wajib redirect ke /hr/karyawan.php.');

fwrite(STDOUT, "PASS: provisioning endpoint checks\n");
exit(0);
