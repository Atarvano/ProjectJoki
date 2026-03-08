<?php

function provisioning_pick_existing_path($root, $paths)
{
    foreach ($paths as $path) {
        if (file_exists($root . '/' . $path)) {
            return $path;
        }
    }

    return $paths[0];
}

function assert_true($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

function assert_contains_any($content, $needles, $message)
{
    foreach ($needles as $needle) {
        if (strpos($content, $needle) !== false) {
            return;
        }
    }

    assert_true(false, $message);
}

$root = dirname(__DIR__);
$endpoint_relative_path = provisioning_pick_existing_path($root, [
    'hr/employee-provision.php',
    'hr/karyawan-provision.php',
]);
$list_page_relative_path = provisioning_pick_existing_path($root, [
    'hr/views/employees.php',
    'hr/employees.php',
    'hr/karyawan.php',
]);

$endpoint_path = $root . '/' . $endpoint_relative_path;
$list_page_path = $root . '/' . $list_page_relative_path;

assert_true(file_exists($endpoint_path), 'Endpoint file final atau bridge provision harus ada.');
assert_true(file_exists($list_page_path), 'Renderer list final atau bridge harus ada.');

define('KARYAWAN_PROVISION_TEST_MODE', true);
require $endpoint_path;

assert_true(function_exists('build_provision_password'), 'Fungsi build_provision_password() harus tersedia.');

$password = build_provision_password('EMP001', '1990-12-31');
assert_true($password === 'EMP00131121990', 'Rumus password harus NIK + DDMMYYYY(tanggal_lahir).');

$invalid_password = build_provision_password('EMP001', '31-12-1990');
assert_true($invalid_password === null, 'Tanggal lahir tidak valid harus menghasilkan null.');

$content = file_get_contents($endpoint_path);
$list_content = file_get_contents($list_page_path);
$logic_content = file_get_contents($root . '/hr/logic/employee-provision.php');
assert_true($content !== false, 'Isi endpoint provision harus bisa dibaca.');
assert_true($list_content !== false, 'Isi renderer list employee harus bisa dibaca.');
assert_true($logic_content !== false, 'Isi logic provision final harus bisa dibaca.');
assert_true(strpos($content, "require_once __DIR__ . '/logic/employee-provision.php';") !== false, 'Route provision final harus memanggil logic/employee-provision.php.');
assert_true(strpos($logic_content, "REQUEST_METHOD'] !== 'POST'") !== false, 'Endpoint wajib POST-only.');
assert_true(strpos($logic_content, 'Aksi hanya bisa lewat tombol Buat Akun Login.') !== false, 'Copy penolakan non-POST harus informatif dan konsisten.');
assert_true(strpos($logic_content, 'password_hash') !== false, 'Endpoint wajib menggunakan password_hash().');
assert_true(strpos($logic_content, 'INSERT INTO users') !== false, 'Endpoint wajib memiliki INSERT INTO users.');
assert_true(strpos($logic_content, 'role, is_active') !== false, 'Insert wajib set role dan is_active.');
assert_true(strpos($logic_content, 'employee') !== false, 'Role akun baru wajib employee.');
assert_true(strpos($logic_content, "'credentials' => [") !== false, 'Flash sukses wajib memakai payload credentials terstruktur.');
assert_true(strpos($logic_content, "'username' => \$username") !== false, 'Payload credentials wajib menyimpan username terstruktur.');
assert_true(strpos($logic_content, "'password_awal' => \$password_plain") !== false, 'Payload credentials wajib menyimpan password_awal terstruktur.');
assert_true(strpos($logic_content, "'pattern_example' => \$password_plain") !== false, 'Payload credentials wajib menyimpan pattern_example untuk bantuan pola password.');
assert_contains_any(
    $logic_content,
    [
        "header('Location: /hr/employee-provision.php')",
        "header('Location: /hr/employees.php')",
        "header('Location: /hr/karyawan-provision.php')",
        "header('Location: /hr/karyawan.php')",
    ],
    'Endpoint wajib mengarah ke route akhir employee atau bridge lama selama rollout.'
);
assert_true(strpos($list_content, 'NIK (Username)') !== false, 'Renderer sukses wajib tetap menampilkan label NIK (Username).');
assert_true(strpos($list_content, 'Password awal') !== false, 'Renderer sukses wajib tetap menampilkan label Password awal.');
assert_true(strpos($list_content, 'Kredensial ini hanya ditampilkan sekali.') !== false, 'Copy warning sekali tampil harus tetap ada di renderer.');
assert_true(strpos($list_content, 'Pola password awal: NIK + tanggal lahir (DDMMYYYY)') !== false, 'Catatan rumus password awal harus tetap ada di renderer.');
assert_true(strpos(file_get_contents(__FILE__), 'employee-provision.php') !== false, 'Smoke provisioning harus menyebut route akhir employee-provision.php.');
assert_true(strpos(file_get_contents(__FILE__), 'hr/employees.php') !== false, 'Smoke provisioning harus menyebut route akhir hr/employees.php.');

fwrite(STDOUT, "PASS: provisioning endpoint checks\n");
exit(0);
