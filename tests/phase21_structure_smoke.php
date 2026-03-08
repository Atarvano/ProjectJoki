<?php

$root = dirname(__DIR__);
$target_group = 'all';

foreach ($argv as $argument) {
    if (strpos($argument, '--group=') === 0) {
        $target_group = substr($argument, 8);
    }
}

$valid_groups = ['all', 'folders', 'includes'];
if (!in_array($target_group, $valid_groups, true)) {
    fwrite(STDERR, "FAIL: Group tidak dikenal. Pakai --group=folders atau --group=includes.\n");
    exit(1);
}

function phase21_fail($message)
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function phase21_assert_true($condition, $message)
{
    if (!$condition) {
        phase21_fail($message);
    }
}

function phase21_load($relative_path)
{
    global $root;

    $full_path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
    phase21_assert_true(file_exists($full_path), "File {$relative_path} harus ada.");

    $content = file_get_contents($full_path);
    phase21_assert_true($content !== false, "File {$relative_path} harus bisa dibaca.");

    return $content;
}

function phase21_assert_contains($content, $needle, $message)
{
    phase21_assert_true(strpos($content, $needle) !== false, $message);
}

function phase21_assert_not_contains($content, $needle, $message)
{
    phase21_assert_true(strpos($content, $needle) === false, $message);
}

function run_folders_group()
{
    $new_auth_guard = phase21_load('includes/auth/auth-guard.php');
    $new_layout = phase21_load('includes/layout/dashboard-layout.php');
    $new_header = phase21_load('includes/layout/header.php');
    $new_footer = phase21_load('includes/layout/footer.php');
    $new_sidebar = phase21_load('includes/layout/dashboard-sidebar.php');
    $new_topbar = phase21_load('includes/layout/dashboard-topbar.php');

    phase21_assert_contains($new_auth_guard, 'function cekLogin()', 'Auth guard baru harus tetap punya fungsi cekLogin.');
    phase21_assert_contains($new_layout, "require_once __DIR__ . '/header.php';", 'Layout baru harus memanggil header dari folder layout.');
    phase21_assert_contains($new_layout, "include __DIR__ . '/dashboard-sidebar.php';", 'Layout baru harus memanggil sidebar dari folder layout.');
    phase21_assert_contains($new_layout, "include __DIR__ . '/dashboard-topbar.php';", 'Layout baru harus memanggil topbar dari folder layout.');
    phase21_assert_contains($new_layout, "include __DIR__ . '/footer.php';", 'Layout baru harus memanggil footer dari folder layout.');
    phase21_assert_contains($new_header, '<!DOCTYPE html>', 'Header layout baru harus tetap berisi template HTML.');
    phase21_assert_contains($new_footer, 'Bootstrap Bundle with Popper', 'Footer layout baru harus tetap memuat script footer.');
    phase21_assert_contains($new_sidebar, '$nav_items = [', 'Sidebar layout baru harus tetap punya daftar navigasi.');
    phase21_assert_contains($new_topbar, '$profile_label', 'Topbar layout baru harus tetap menyiapkan label profil.');

    $shim_auth = phase21_load('includes/auth-guard.php');
    $shim_layout = phase21_load('includes/dashboard-layout.php');
    $shim_header = phase21_load('includes/header.php');
    $shim_footer = phase21_load('includes/footer.php');
    $shim_sidebar = phase21_load('includes/dashboard-sidebar.php');
    $shim_topbar = phase21_load('includes/dashboard-topbar.php');

    phase21_assert_contains($shim_auth, "require_once __DIR__ . '/auth/auth-guard.php';", 'Shim auth guard harus menunjuk ke folder auth baru.');
    phase21_assert_contains($shim_layout, "require_once __DIR__ . '/layout/dashboard-layout.php';", 'Shim dashboard layout harus menunjuk ke folder layout baru.');
    phase21_assert_contains($shim_header, "require_once __DIR__ . '/layout/header.php';", 'Shim header harus menunjuk ke folder layout baru.');
    phase21_assert_contains($shim_footer, "require_once __DIR__ . '/layout/footer.php';", 'Shim footer harus menunjuk ke folder layout baru.');
    phase21_assert_contains($shim_sidebar, "require_once __DIR__ . '/layout/dashboard-sidebar.php';", 'Shim sidebar harus menunjuk ke folder layout baru.');
    phase21_assert_contains($shim_topbar, "require_once __DIR__ . '/layout/dashboard-topbar.php';", 'Shim topbar harus menunjuk ke folder layout baru.');

    phase21_assert_not_contains($shim_auth, 'function cekLogin()', 'Shim auth guard lama tidak boleh lagi menyimpan implementasi penuh.');
    phase21_assert_not_contains($shim_layout, '$dashboard_context', 'Shim dashboard layout lama tidak boleh lagi menyimpan layout penuh.');
    phase21_assert_not_contains($shim_header, '<!DOCTYPE html>', 'Shim header lama tidak boleh lagi menyimpan markup penuh.');

    fwrite(STDOUT, "PASS [folders]: folder include baru dan shim kompatibilitas terdeteksi.\n");
}

function run_includes_group()
{
    $index = phase21_load('index.php');
    $login = phase21_load('login.php');
    $layout = phase21_load('includes/layout/dashboard-layout.php');

    phase21_assert_contains($index, "include 'includes/layout/header.php';", 'index.php harus langsung memakai header dari includes/layout.');
    phase21_assert_contains($index, "include 'includes/layout/footer.php';", 'index.php harus langsung memakai footer dari includes/layout.');
    phase21_assert_not_contains($index, "include 'includes/header.php';", 'index.php tidak boleh lagi bergantung ke header shim lama.');
    phase21_assert_not_contains($index, "include 'includes/footer.php';", 'index.php tidak boleh lagi bergantung ke footer shim lama.');

    phase21_assert_contains($login, "include 'includes/layout/header.php';", 'login.php harus langsung memakai header dari includes/layout.');
    phase21_assert_contains($login, "include 'includes/layout/footer.php';", 'login.php harus langsung memakai footer dari includes/layout.');
    phase21_assert_not_contains($login, "include 'includes/header.php';", 'login.php tidak boleh lagi bergantung ke header shim lama.');
    phase21_assert_not_contains($login, "include 'includes/footer.php';", 'login.php tidak boleh lagi bergantung ke footer shim lama.');

    phase21_assert_contains($layout, "require_once __DIR__ . '/header.php';", 'Layout grouped harus memakai header grouped.');
    phase21_assert_contains($layout, "include __DIR__ . '/dashboard-sidebar.php';", 'Layout grouped harus memakai sidebar grouped.');
    phase21_assert_contains($layout, "include __DIR__ . '/dashboard-topbar.php';", 'Layout grouped harus memakai topbar grouped.');
    phase21_assert_contains($layout, "include __DIR__ . '/footer.php';", 'Layout grouped harus memakai footer grouped.');

    fwrite(STDOUT, "PASS [includes]: halaman publik dan layout utama sudah menunjuk ke path grouped.\n");
}

if ($target_group === 'all' || $target_group === 'folders') {
    run_folders_group();
}

if ($target_group === 'all' || $target_group === 'includes') {
    run_includes_group();
}

fwrite(STDOUT, "PASS: phase21_structure_smoke\n");
exit(0);
