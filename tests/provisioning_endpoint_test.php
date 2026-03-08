<?php

$endpoint_path = __DIR__ . '/../hr/karyawan-provision.php';
$list_page_path = __DIR__ . '/../hr/karyawan.php';

function assert_true($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

assert_true(file_exists($endpoint_path), 'Endpoint file hr/karyawan-provision.php harus ada.');
assert_true(file_exists($list_page_path), 'Halaman list hr/karyawan.php harus ada.');

define('KARYAWAN_PROVISION_TEST_MODE', true);
require $endpoint_path;

assert_true(function_exists('build_provision_password'), 'Fungsi build_provision_password() harus tersedia.');

$password = build_provision_password('EMP001', '1990-12-31');
assert_true($password === 'EMP00131121990', 'Rumus password harus NIK + DDMMYYYY(tanggal_lahir).');

$invalid_password = build_provision_password('EMP001', '31-12-1990');
assert_true($invalid_password === null, 'Tanggal lahir tidak valid harus menghasilkan null.');

$content = file_get_contents($endpoint_path);
$list_content = file_get_contents($list_page_path);
assert_true(strpos($content, "REQUEST_METHOD'] !== 'POST'") !== false, 'Endpoint wajib POST-only.');
assert_true(strpos($content, 'Aksi hanya bisa lewat tombol Buat Akun Login.') !== false, 'Copy penolakan non-POST harus informatif dan konsisten.');
assert_true(strpos($content, 'password_hash') !== false, 'Endpoint wajib menggunakan password_hash().');
assert_true(strpos($content, 'INSERT INTO users') !== false, 'Endpoint wajib memiliki INSERT INTO users.');
assert_true(strpos($content, "role, is_active") !== false, 'Insert wajib set role dan is_active.');
assert_true(strpos($content, "employee") !== false, 'Role akun baru wajib employee.');
assert_true(strpos($content, "'credentials' => [") !== false, 'Flash sukses wajib memakai payload credentials terstruktur.');
assert_true(strpos($content, "'username' => \$username") !== false, 'Payload credentials wajib menyimpan username terstruktur.');
assert_true(strpos($content, "'password_awal' => \$password_plain") !== false, 'Payload credentials wajib menyimpan password_awal terstruktur.');
assert_true(strpos($content, "'pattern_example' => \$password_plain") !== false, 'Payload credentials wajib menyimpan pattern_example untuk bantuan pola password.');
assert_true(strpos($content, "header('Location: /hr/karyawan.php')") !== false, 'Endpoint wajib redirect ke /hr/karyawan.php.');
assert_true(strpos($list_content, "NIK (Username)") !== false, 'Renderer sukses wajib tetap menampilkan label NIK (Username).');
assert_true(strpos($list_content, "Password awal") !== false, 'Renderer sukses wajib tetap menampilkan label Password awal.');
assert_true(strpos($list_content, 'Kredensial ini hanya ditampilkan sekali.') !== false, 'Copy warning sekali tampil harus tetap ada di renderer.');
assert_true(strpos($list_content, 'Pola password awal: NIK + tanggal lahir (DDMMYYYY)') !== false, 'Catatan rumus password awal harus tetap ada di renderer.');

fwrite(STDOUT, "PASS: provisioning endpoint checks\n");
exit(0);
