<?php

$root = dirname(__DIR__);
$target_group = 'all';

foreach ($argv as $argument) {
    if (strpos($argument, '--group=') === 0) {
        $target_group = substr($argument, 8);
    }
}

$valid_groups = ['all', 'calculator', 'reports', 'dashboards'];
if (!in_array($target_group, $valid_groups, true)) {
    fwrite(STDERR, "FAIL: Group tidak dikenal. Pakai --group=calculator, --group=reports, atau --group=dashboards.\n");
    fwrite(STDERR, "Tips cek syntax: php -l hr/kalkulator.php && php -l hr/laporan.php && php -l hr/export.php && php -l hr/dashboard.php && php -l employee/dashboard.php\n");
    exit(1);
}

function assert_true($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        fwrite(STDERR, "Tips cek syntax: php -l hr/kalkulator.php && php -l hr/laporan.php && php -l hr/export.php && php -l hr/dashboard.php && php -l employee/dashboard.php\n");
        exit(1);
    }
}

function load_page_text($relative_path)
{
    global $root;

    $full_path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
    assert_true(file_exists($full_path), "File {$relative_path} harus ada.");

    $content = file_get_contents($full_path);
    assert_true($content !== false, "File {$relative_path} harus bisa dibaca.");

    return $content;
}

function assert_contains($content, $needle, $message)
{
    assert_true(strpos($content, $needle) !== false, $message);
}

function assert_not_contains($content, $needle, $message)
{
    assert_true(strpos($content, $needle) === false, $message);
}

function run_calculator_group()
{
    $content = load_page_text('hr/kalkulator.php');

    assert_contains($content, 'require_once __DIR__ . \'/../includes/cuti-calculator.php\';', 'Kalkulator harus tetap memakai engine cuti yang ada.');
    assert_contains($content, 'Kalkulator Hak Cuti', 'Halaman kalkulator harus tetap ada.');
    assert_contains($content, 'tahun_bergabung', 'Baseline Wave 0 harus masih bisa mendeteksi input tahun bergabung sebelum rewiring Plan 18-02.');
    assert_contains($content, 'save_report', 'Baseline Wave 0 harus masih bisa mendeteksi alur save report lama sebelum dibersihkan Plan 18-02.');

    fwrite(STDOUT, "PASS [calculator]: baseline marker kalkulator terbaca; siap dipakai sebagai smoke check Phase 18.\n");
}

function run_reports_group()
{
    $laporan = load_page_text('hr/laporan.php');
    $export = load_page_text('hr/export.php');

    assert_contains($laporan, 'require_once __DIR__ . \'/../includes/reports-data.php\';', 'Baseline laporan harus masih menunjukkan dependensi reports-data sebelum Plan 18-03.');
    assert_contains($laporan, 'reset_reports', 'Baseline laporan harus masih punya marker reset reports untuk dibersihkan di Plan 18-03.');
    assert_contains($laporan, 'Export Excel', 'Halaman laporan harus tetap menyediakan entry export.');
    assert_contains($export, 'require_once __DIR__ . \'/../includes/reports-data.php\';', 'Baseline export harus masih menunjukkan sumber reports-data sebelum Plan 18-03.');
    assert_contains($export, 'getReports()', 'Export baseline harus masih memakai getReports() agar cleanup Plan 18-03 bisa dibuktikan.');

    fwrite(STDOUT, "PASS [reports]: baseline marker laporan/export terbaca; siap dipakai sebagai smoke check Phase 18.\n");
}

function run_dashboards_group()
{
    $hr_dashboard = load_page_text('hr/dashboard.php');
    $employee_dashboard = load_page_text('employee/dashboard.php');

    assert_contains($hr_dashboard, 'countPresetKaryawan()', 'Baseline dashboard HR harus masih menandai counter preset sebelum Plan 18-04.');
    assert_contains($hr_dashboard, 'countReports()', 'Baseline dashboard HR harus masih menandai counter laporan session sebelum Plan 18-04.');
    assert_contains($employee_dashboard, '$karyawan_id = isset($_SESSION[\'karyawan_id\']) ? (int) $_SESSION[\'karyawan_id\'] : 0;', 'Dashboard employee harus tetap terhubung ke session karyawan sebagai dasar rewiring Plan 18-04.');
    assert_contains($employee_dashboard, 'Rincian Hak Cuti 8 Tahun', 'Baseline dashboard employee harus masih menampilkan label 8 tahun penuh sebelum difokuskan di Plan 18-04.');

    fwrite(STDOUT, "PASS [dashboards]: baseline marker dashboard terbaca; siap dipakai sebagai smoke check Phase 18.\n");
}

if ($target_group === 'all' || $target_group === 'calculator') {
    run_calculator_group();
}

if ($target_group === 'all' || $target_group === 'reports') {
    run_reports_group();
}

if ($target_group === 'all' || $target_group === 'dashboards') {
    run_dashboards_group();
}

fwrite(STDOUT, "PASS: phase18_data_wiring_smoke\n");
exit(0);
