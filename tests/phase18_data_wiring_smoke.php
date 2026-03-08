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
    fwrite(STDERR, "Tips cek syntax: php -l hr/kalkulator.php && php -l hr/reports.php && php -l hr/export.php && php -l hr/dashboard.php && php -l employee/dashboard.php\n");
    exit(1);
}

function assert_true($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        fwrite(STDERR, "Tips cek syntax: php -l hr/kalkulator.php && php -l hr/reports.php && php -l hr/export.php && php -l hr/dashboard.php && php -l employee/dashboard.php\n");
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
    assert_contains($content, 'karyawan_id', 'Kalkulator Phase 18 harus memakai selector karyawan dari database sebagai input utama.');
    assert_contains($content, 'tanggal_bergabung', 'Kalkulator Phase 18 harus tetap mengambil tanggal bergabung dari data karyawan.');
    assert_contains($content, 'hitungHakCuti($tahun_bergabung)', 'Kalkulator Phase 18 harus tetap mengirim tahun bergabung turunan DB ke engine cuti.');
    assert_not_contains($content, 'save_report', 'Flow simpan laporan lama harus dibersihkan dari kalkulator Phase 18.');
    assert_not_contains($content, 'name="tahun_bergabung"', 'Input manual tahun bergabung tidak boleh lagi menjadi input utama kalkulator.');

    fwrite(STDOUT, "PASS [calculator]: marker rewiring kalkulator employee-first terbaca.\n");
}

function run_reports_group()
{
    global $root;

    $laporan = load_page_text('hr/reports.php');
    $laporan_logic = load_page_text('hr/logic/reports.php');
    $laporan_view = load_page_text('hr/views/reports.php');
    $export = load_page_text('hr/export.php');

    assert_contains($laporan, 'require_once __DIR__ . \'/../koneksi.php\';', 'Laporan harus memuat koneksi database live.');
    assert_contains($laporan, 'require_once __DIR__ . \'/logic/reports.php\';', 'Route laporan akhir harus memakai logic/reports.php.');
    assert_contains($laporan_logic, 'ORDER BY nik ASC, nama ASC', 'Laporan harus mengurutkan data live berdasarkan NIK lalu nama.');
    assert_contains($laporan_logic, 'hitungHakCuti($tahun_bergabung)', 'Logic laporan harus tetap menghitung hak cuti dari tanggal bergabung.');
    assert_contains($laporan_view, 'employee-detail.php?id=', 'Laporan harus mengarahkan detail ke halaman karyawan final.');
    assert_contains($laporan_view, 'Export Excel', 'Halaman laporan harus tetap menyediakan entry export.');
    assert_not_contains($laporan, 'reset_reports', 'Laporan tidak boleh lagi memiliki reset report berbasis session.');
    assert_not_contains($laporan_logic, 'reports-data.php', 'Laporan tidak boleh lagi bergantung pada helper session reports.');
    assert_not_contains($laporan_view, 'Data Laporan', 'Label saved-report lama harus sudah dihapus dari laporan.');
    assert_contains($export, 'require_once __DIR__ . \'/../koneksi.php\';', 'Export harus memuat koneksi database live.');
    assert_contains($export, 'hitungHakCuti', 'Export harus menghitung hak cuti langsung dari data live.');
    assert_not_contains($export, 'reports-data.php', 'Export tidak boleh lagi bergantung pada helper session reports.');
    assert_not_contains($export, 'getReports()', 'Export tidak boleh lagi memakai getReports() session.');
    assert_true(!file_exists($root . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'reports-data.php'), 'File includes/reports-data.php harus sudah dihapus.');

    fwrite(STDOUT, "PASS [reports]: reports dan export live DB terdeteksi.\n");
}

function run_dashboards_group()
{
    $hr_dashboard = load_page_text('hr/dashboard.php');
    $employee_dashboard = load_page_text('employee/dashboard.php');

    assert_contains($hr_dashboard, 'SELECT COUNT(*) AS total FROM karyawan', 'Dashboard HR harus menghitung total karyawan dari database.');
    assert_contains($hr_dashboard, "role = 'employee' AND karyawan_id IS NOT NULL", 'Dashboard HR harus menghitung akun employee yang sudah terhubung ke karyawan.');
    assert_contains($hr_dashboard, 'in_array($row[\'status\'], [\'Tersedia\', \'Menunggu\'], true)', 'Dashboard HR harus menghitung siap cuti tahun ini dari status Tersedia dan Menunggu.');
    assert_not_contains($hr_dashboard, 'countPresetKaryawan()', 'Counter preset lama harus dihapus dari dashboard HR.');
    assert_not_contains($hr_dashboard, 'countReports()', 'Counter laporan session lama harus dihapus dari dashboard HR.');
    assert_contains($employee_dashboard, '$karyawan_id = isset($_SESSION[\'karyawan_id\']) ? (int) $_SESSION[\'karyawan_id\'] : 0;', 'Dashboard employee harus tetap terhubung ke session karyawan sebagai dasar rewiring Plan 18-04.');
    assert_contains($employee_dashboard, 'in_array((int) $row[\'tahun_ke\'], [6, 7, 8], true)', 'Dashboard employee harus memfilter fokus tahun ke-6, 7, dan 8.');
    assert_contains($employee_dashboard, 'Fokus Hak Cuti Tahun', 'Dashboard employee harus menampilkan judul fokus tahun 6/7/8.');
    assert_not_contains($employee_dashboard, 'Rincian Hak Cuti 8 Tahun', 'Label 8 tahun penuh harus dihapus dari dashboard employee.');

    fwrite(STDOUT, "PASS [dashboards]: marker dashboard live DB dan focus year 6/7/8 terbaca.\n");
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
