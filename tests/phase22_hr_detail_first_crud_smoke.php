<?php

$root = dirname(__DIR__);
$target_group = 'all';

foreach ($argv as $argument) {
    if (strpos($argument, '--group=') === 0) {
        $target_group = substr($argument, 8);
    }
}

$valid_groups = ['all', 'crud-flow', 'detail-view', 'navigation', 'leave-focus'];
if (!in_array($target_group, $valid_groups, true)) {
    fwrite(STDERR, "FAIL: Group tidak dikenal. Pakai --group=crud-flow, --group=detail-view, --group=navigation, atau --group=leave-focus.\n");
    exit(1);
}

function phase22_fail($message)
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function phase22_assert_true($condition, $message)
{
    if (!$condition) {
        phase22_fail($message);
    }
}

function phase22_load($relative_path)
{
    global $root;

    $full_path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
    phase22_assert_true(file_exists($full_path), "File {$relative_path} harus ada.");

    $content = file_get_contents($full_path);
    phase22_assert_true($content !== false, "File {$relative_path} harus bisa dibaca.");

    return $content;
}

function phase22_assert_contains($content, $needle, $message)
{
    phase22_assert_true(strpos($content, $needle) !== false, $message);
}

function phase22_assert_not_contains($content, $needle, $message)
{
    phase22_assert_true(strpos($content, $needle) === false, $message);
}

function run_crud_flow_group()
{
    $employee_create_logic = phase22_load('hr/logic/employee-create.php');
    $employee_edit_logic = phase22_load('hr/logic/employee-edit.php');
    $employee_detail_view = phase22_load('hr/views/employee-detail.php');

    phase22_assert_contains($employee_create_logic, "mysqli_insert_id(", 'Create employee harus mengambil id baru dengan mysqli_insert_id agar bisa pindah ke detail karyawan yang baru dibuat.');
    phase22_assert_contains($employee_create_logic, "header('Location: /hr/employee-detail.php?id=' . ", 'Create employee harus redirect ke employee-detail.php?id=... setelah simpan berhasil.');
    phase22_assert_not_contains($employee_create_logic, "header('Location: /hr/employees.php');", 'Create employee tidak boleh lagi kembali ke daftar sebagai alur utama setelah simpan.');

    phase22_assert_contains($employee_edit_logic, "header('Location: /hr/employee-detail.php?id=' . $id);", 'Edit employee harus kembali ke employee-detail.php?id=... setelah perubahan disimpan.');
    phase22_assert_not_contains($employee_edit_logic, "header('Location: /hr/employees.php');", 'Edit employee tidak boleh lagi kembali ke daftar sebagai alur utama setelah simpan.');

    phase22_assert_contains($employee_detail_view, '/hr/employee-edit.php?id=', 'Detail employee harus punya link edit langsung.');
    phase22_assert_contains($employee_detail_view, '/hr/employee-delete.php', 'Detail employee harus tetap punya form delete langsung.');
    phase22_assert_contains($employee_detail_view, '/hr/employee-provision.php', 'Detail employee harus tetap punya form provision langsung.');

    fwrite(STDOUT, "PASS [crud-flow]: alur create/edit/detail/provision/delete sudah mengarah ke detail-first CRUD.\n");
}

function run_detail_view_group()
{
    $employee_detail_logic = phase22_load('hr/logic/employee-detail.php');
    $employee_detail_view = phase22_load('hr/views/employee-detail.php');

    phase22_assert_contains($employee_detail_logic, "require_once __DIR__ . '/../../includes/cuti-calculator.php';", 'Logic detail harus memakai engine cuti yang sudah ada dari includes/cuti-calculator.php.');
    phase22_assert_contains($employee_detail_logic, '$leave_error', 'Logic detail harus menyiapkan pesan inline untuk masalah tanggal bergabung.');
    phase22_assert_contains($employee_detail_logic, '$leave_rows', 'Logic detail harus menyiapkan baris hak cuti untuk view detail.');
    phase22_assert_contains($employee_detail_logic, '$leave_snapshot', 'Logic detail harus menyiapkan ringkasan hak cuti tahun berjalan.');
    phase22_assert_contains($employee_detail_logic, 'hitungHakCuti', 'Logic detail harus menghitung hak cuti lewat engine yang sama dengan halaman lain.');
    phase22_assert_not_contains($employee_detail_logic, "header('Location: /hr/kalkulator.php", 'Logic detail tidak boleh mengalihkan HR ke kalkulator saat meninjau hak cuti.');

    phase22_assert_contains($employee_detail_view, 'Profil Karyawan', 'View detail harus menampilkan blok profil karyawan lebih dulu.');
    phase22_assert_contains($employee_detail_view, 'Ringkasan Hak Cuti', 'View detail harus menampilkan blok ringkasan hak cuti pada halaman yang sama.');
    phase22_assert_contains($employee_detail_view, 'Hak cuti di bawah ini dihitung dari tanggal bergabung dan engine cuti yang sama dengan halaman lain.', 'View detail harus memberi catatan sederhana bahwa angka cuti berasal dari join date dan engine yang sudah ada.');
    phase22_assert_not_contains($employee_detail_view, '/hr/kalkulator.php', 'View detail tidak boleh lagi menjadikan kalkulator sebagai tombol utama untuk review cuti.');

    fwrite(STDOUT, "PASS [detail-view]: detail employee sudah memuat profil dan leave block pada halaman yang sama.\n");
}

function run_navigation_group()
{
    $reports_view = phase22_load('hr/views/reports.php');
    $dashboard = phase22_load('hr/dashboard.php');
    $employee_detail_logic = phase22_load('hr/logic/employee-detail.php');

    phase22_assert_contains($reports_view, 'employee-detail.php?id=<?php echo $report[\'id\']; ?>&from=reports', 'Laporan harus membuka detail employee dengan penanda sumber reports.');
    phase22_assert_contains($reports_view, 'Buka Detail & Hak Cuti', 'Laporan harus menonjolkan detail employee sebagai jalur review hak cuti.');
    phase22_assert_contains($employee_detail_logic, "if ($from === 'reports') {", 'Logic detail harus membaca sumber reports untuk tombol kembali.');
    phase22_assert_contains($employee_detail_logic, "$back_url = '/hr/reports.php';", 'Logic detail harus bisa kembali ke reports.php bila datang dari laporan.');
    phase22_assert_contains($employee_detail_logic, "$back_label = 'Kembali ke Laporan';", 'Logic detail harus memberi label kembali ke laporan bila datang dari reports.');

    phase22_assert_contains($dashboard, "'link' => 'employees.php'", 'Dashboard HR harus tetap mendorong HR ke employees.php sebagai hub utama.');
    phase22_assert_contains($dashboard, 'Kelola Data Karyawan', 'Dashboard HR harus punya ajakan utama untuk kelola data karyawan.');
    phase22_assert_contains($dashboard, 'Buka detail karyawan untuk melihat profil dan hak cuti tanpa mulai dari kalkulator.', 'Dashboard HR harus menekankan detail-first flow untuk review hak cuti.');
    phase22_assert_contains($dashboard, "'link' => 'reports.php'", 'Dashboard HR harus tetap memberi akses ke laporan sebagai jalur sekunder.');
    phase22_assert_contains($dashboard, "'link' => 'kalkulator.php'", 'Dashboard HR masih boleh menyimpan kalkulator sebagai jalur sekunder fase 22.');
    phase22_assert_not_contains($dashboard, 'Hitung Hak Cuti', 'Dashboard HR tidak boleh lagi memakai copy calculator-first sebagai CTA utama.');

    fwrite(STDOUT, "PASS [navigation]: dashboard dan laporan sudah menaruh employee-detail sebagai jalur review utama.\n");
}

function run_leave_focus_group()
{
    $employee_detail_logic = phase22_load('hr/logic/employee-detail.php');
    $employee_detail_view = phase22_load('hr/views/employee-detail.php');

    phase22_assert_contains($employee_detail_logic, 'in_array((int) $row[\'tahun_ke\'], [6, 7, 8], true)', 'Logic detail hanya boleh mengambil tahun ke-6 sampai ke-8 untuk tabel hak cuti.');
    phase22_assert_contains($employee_detail_view, 'Tahun ke-6', 'View detail harus menampilkan label Tahun ke-6.');
    phase22_assert_contains($employee_detail_view, 'Tahun ke-7', 'View detail harus menampilkan label Tahun ke-7.');
    phase22_assert_contains($employee_detail_view, 'Tahun ke-8', 'View detail harus menampilkan label Tahun ke-8.');
    phase22_assert_not_contains($employee_detail_view, 'Tahun ke-1', 'View detail tidak boleh lagi menampilkan timeline penuh dari Tahun ke-1.');
    phase22_assert_not_contains($employee_detail_view, 'Tahun ke-2', 'View detail tidak boleh lagi menampilkan timeline penuh dari Tahun ke-2.');
    phase22_assert_not_contains($employee_detail_view, 'Tahun ke-3', 'View detail tidak boleh lagi menampilkan timeline penuh dari Tahun ke-3.');
    phase22_assert_not_contains($employee_detail_view, 'Tahun ke-4', 'View detail tidak boleh lagi menampilkan timeline penuh dari Tahun ke-4.');
    phase22_assert_not_contains($employee_detail_view, 'Tahun ke-5', 'View detail tidak boleh lagi menampilkan timeline penuh dari Tahun ke-5.');
    phase22_assert_contains($employee_detail_view, '$leave_error', 'View detail harus menampilkan area warning inline saat tanggal bergabung belum valid.');
    phase22_assert_not_contains($employee_detail_logic, "header('Location: /hr/employees.php');", 'Masalah tanggal bergabung tidak boleh membuat halaman detail redirect ke daftar.');

    fwrite(STDOUT, "PASS [leave-focus]: detail employee fokus pada tahun 6-8 dan warning tanggal bergabung inline.\n");
}

if ($target_group === 'all' || $target_group === 'crud-flow') {
    run_crud_flow_group();
}

if ($target_group === 'all' || $target_group === 'detail-view') {
    run_detail_view_group();
}

if ($target_group === 'all' || $target_group === 'navigation') {
    run_navigation_group();
}

if ($target_group === 'all' || $target_group === 'leave-focus') {
    run_leave_focus_group();
}

fwrite(STDOUT, "Semua smoke group Phase 22 selesai diperiksa.\n");
