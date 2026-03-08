<?php

$root = dirname(__DIR__);
$target_group = 'all';

foreach ($argv as $argument) {
    if (strpos($argument, '--group=') === 0) {
        $target_group = substr($argument, 8);
    }
}

$valid_groups = ['all', 'employee-self-view', 'navigation', 'retirement', 'missing-data'];
if (!in_array($target_group, $valid_groups, true)) {
    fwrite(STDERR, "FAIL: Group tidak dikenal. Pakai --group=employee-self-view, --group=navigation, --group=retirement, atau --group=missing-data.\n");
    exit(1);
}

function phase23_fail($message)
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function phase23_assert_true($condition, $message)
{
    if (!$condition) {
        phase23_fail($message);
    }
}

function phase23_load($relative_path)
{
    global $root;

    $full_path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
    phase23_assert_true(file_exists($full_path), "File {$relative_path} harus ada.");

    $content = file_get_contents($full_path);
    phase23_assert_true($content !== false, "File {$relative_path} harus bisa dibaca.");

    return $content;
}

function phase23_assert_contains($content, $needle, $message)
{
    phase23_assert_true(strpos($content, $needle) !== false, $message);
}

function phase23_assert_not_contains($content, $needle, $message)
{
    phase23_assert_true(strpos($content, $needle) === false, $message);
}

function phase23_assert_not_contains_between($content, $start, $end, $needle, $message)
{
    $start_pos = strpos($content, $start);
    phase23_assert_true($start_pos !== false, "Penanda awal tidak ditemukan untuk assertion: {$start}");

    $end_pos = strpos($content, $end, $start_pos);
    phase23_assert_true($end_pos !== false, "Penanda akhir tidak ditemukan untuk assertion: {$end}");

    $segment = substr($content, $start_pos, $end_pos - $start_pos);
    phase23_assert_true(strpos($segment, $needle) === false, $message);
}

function run_employee_self_view_group()
{
    $employee_route = phase23_load('employee/dashboard.php');
    $employee_logic = phase23_load('employee/logic/dashboard.php');
    $employee_view = phase23_load('employee/views/dashboard.php');

    phase23_assert_contains($employee_route, "cekRole('employee');", 'Route employee harus tetap dijaga khusus role employee.');
    phase23_assert_contains($employee_route, "require_once __DIR__ . '/logic/dashboard.php';", 'Route employee harus tetap memakai employee/logic/dashboard.php sebagai logic self-view.');
    phase23_assert_contains($employee_route, "require_once __DIR__ . '/../includes/layout/dashboard-layout.php';", 'Route employee harus tetap memakai dashboard layout yang sama.');

    phase23_assert_contains($employee_logic, "'active_nav' => 'hak-cuti'", 'Logic employee harus menandai Hak Cuti Saya sebagai navigasi aktif.');
    phase23_assert_contains($employee_logic, 'in_array((int) $row[\'tahun_ke\'], [6, 7, 8], true)', 'Logic employee harus fokus pada tahun kerja 6 sampai 8.');
    phase23_assert_contains($employee_logic, '$focus_rows', 'Logic employee harus menyiapkan baris fokus hak cuti untuk view.');
    phase23_assert_contains($employee_logic, '$focus_total_hari', 'Logic employee harus menyiapkan total hari fokus untuk ringkasan self-view.');
    phase23_assert_not_contains($employee_logic, '/hr/kalkulator.php', 'Logic employee tidak boleh lagi mengarahkan user ke kalkulator HR.');

    phase23_assert_contains($employee_view, 'Hak Cuti <span class="text-accent">Saya</span>', 'View employee harus tetap menonjolkan halaman Hak Cuti Saya.');
    phase23_assert_contains($employee_view, 'Fokus Hak Cuti Tahun <?php echo htmlspecialchars($tahun_fokus_label); ?>', 'View employee harus menaruh tabel hak cuti fokus sebagai blok utama.');
    phase23_assert_contains($employee_view, 'Ringkasan ini otomatis mengikuti data kepegawaian akun Anda. Jika ada data yang perlu diperbarui, silakan hubungi tim HR.', 'View employee harus memberi arahan singkat untuk hubungi HR bila data perlu diperbarui.');
    phase23_assert_contains($employee_view, '$focus_rows', 'View employee harus memakai baris fokus yang disiapkan logic self-view.');
    phase23_assert_not_contains($employee_view, 'Kalkulator', 'View employee tidak boleh lagi mengajari alur kalkulator-first.');

    fwrite(STDOUT, "PASS [employee-self-view]: route dan self-view employee sudah mengunci fokus hak cuti langsung pada halaman sendiri.\n");
}

function run_navigation_group()
{
    $employee_logic = phase23_load('employee/logic/dashboard.php');
    $sidebar = phase23_load('includes/layout/dashboard-sidebar.php');
    $footer = phase23_load('includes/layout/footer.php');
    $hr_dashboard = phase23_load('hr/dashboard.php');

    phase23_assert_contains($sidebar, "'label' => 'Hak Cuti Saya'", 'Sidebar harus tetap menampilkan menu Hak Cuti Saya untuk employee.');
    phase23_assert_contains($sidebar, "'href' => '/employee/dashboard.php'", 'Sidebar employee harus menjadikan Hak Cuti Saya sebagai link nyata ke /employee/dashboard.php.');
    phase23_assert_not_contains($sidebar, "'is_helper' => true", 'Sidebar employee tidak boleh lagi menjadikan Hak Cuti Saya sebagai label helper pasif.');
    phase23_assert_not_contains($sidebar, "'label' => 'Kalkulator Cuti'", 'Sidebar dashboard tidak boleh lagi menampilkan menu Kalkulator Cuti sebagai navigasi utama.');

    phase23_assert_contains($footer, '/hr/reports.php', 'Footer harus tetap mengarah ke laporan HR sebagai jalur review pengganti.');
    phase23_assert_contains($footer, '/employee/dashboard.php', 'Footer harus tetap memberi jalan ke self-view employee yang aktif.');
    phase23_assert_not_contains($footer, '/hr/kalkulator.php', 'Footer tidak boleh lagi memuat link kalkulator lama.');
    phase23_assert_not_contains($footer, 'HR Kalkulator', 'Footer tidak boleh lagi mengajari jalur HR Kalkulator.');
    phase23_assert_not_contains($footer, 'Kalkulator Cuti', 'Footer tidak boleh lagi mengajari link Kalkulator Cuti.');

    phase23_assert_contains($employee_logic, "'page_title' => 'Hak Cuti Saya'", 'Halaman employee harus tetap jelas sebagai tujuan self-view pengganti.');
    phase23_assert_contains($hr_dashboard, "'link' => 'reports.php'", 'Dashboard HR harus tetap memakai reports.php sebagai jalur review utama setelah kalkulator dipensiunkan.');
    phase23_assert_not_contains($hr_dashboard, "'link' => 'kalkulator.php'", 'Dashboard HR tidak boleh lagi menyimpan kalkulator sebagai link utama atau sekunder.');
    phase23_assert_not_contains($hr_dashboard, 'kalkulator', 'Dashboard HR tidak boleh lagi mengajari alur kalkulator-first setelah retirement.');

    fwrite(STDOUT, "PASS [navigation]: sidebar, footer, dan dashboard sudah bersih dari alur kalkulator-first.\n");
}

function run_retirement_group()
{
    $calculator_route = phase23_load('hr/kalkulator.php');

    phase23_assert_contains($calculator_route, "cekRole('hr');", 'Bridge kalkulator lama harus tetap dijaga untuk role HR.');
    phase23_assert_contains($calculator_route, "header('Location: /hr/reports.php');", 'Kalkulator lama harus langsung redirect ke /hr/reports.php.');
    phase23_assert_contains($calculator_route, 'exit;', 'Redirect kalkulator lama harus berhenti dengan exit.');
    phase23_assert_not_contains($calculator_route, 'require_once __DIR__ . \'/../koneksi.php\';', 'Bridge kalkulator lama tidak perlu lagi memuat koneksi database.');
    phase23_assert_not_contains($calculator_route, "require_once __DIR__ . '/../includes/cuti-calculator.php';", 'Bridge kalkulator lama tidak boleh lagi memuat engine kalkulator.');
    phase23_assert_not_contains($calculator_route, 'Kalkulator Hak Cuti', 'Bridge kalkulator lama tidak boleh lagi merender judul UI kalkulator.');
    phase23_assert_not_contains($calculator_route, 'Pilih Karyawan', 'Bridge kalkulator lama tidak boleh lagi merender form pilih karyawan.');

    fwrite(STDOUT, "PASS [retirement]: hr/kalkulator.php sudah menjadi bridge redirect saja.\n");
}

function run_missing_data_group()
{
    $auth_guard = phase23_load('includes/auth/auth-guard.php');
    $employee_logic = phase23_load('employee/logic/dashboard.php');
    $employee_view = phase23_load('employee/views/dashboard.php');

    phase23_assert_contains($auth_guard, 'if (!authGuardEmployeeExists($karyawan_id)) {', 'Auth guard employee tetap harus punya cabang cek row karyawan yang eksplisit.');
    phase23_assert_contains($auth_guard, 'self-view warning state', 'Auth guard employee harus menjelaskan bahwa row hilang dipakai untuk warning state self-view.');
    phase23_assert_not_contains_between(
        $auth_guard,
        'if (!authGuardEmployeeExists($karyawan_id)) {',
        'if (AUTH_GUARD_TEST_MODE) {',
        'return authGuardLogoutRedirect();',
        'Auth guard employee tidak boleh lagi logout paksa tepat pada cabang row karyawan hilang.'
    );

    phase23_assert_contains($employee_logic, '$load_error', 'Logic employee harus menyiapkan warning state bila data tidak lengkap atau tidak ditemukan.');
    phase23_assert_contains($employee_logic, 'Data karyawan', 'Logic employee harus punya pesan warning untuk data karyawan yang tidak dapat dimuat.');
    phase23_assert_contains($employee_logic, 'hubungi tim HR', 'Logic employee harus mengarahkan user menghubungi HR saat data bermasalah.');
    phase23_assert_contains($employee_logic, 'Tanggal bergabung pada data Anda belum valid. Silakan hubungi tim HR.', 'Logic employee harus memberi warning langsung untuk tanggal bergabung yang kosong atau tidak valid.');
    phase23_assert_not_contains($employee_logic, 'header(', 'Masalah data employee pada self-view tidak boleh diselesaikan dengan redirect dari logic halaman.');

    phase23_assert_contains($employee_view, '$load_error !== null', 'View employee harus tetap punya cabang warning inline untuk missing-data state.');
    phase23_assert_contains($employee_view, 'alert alert-warning', 'View employee harus menampilkan warning inline saat data employee bermasalah.');
    phase23_assert_contains($employee_view, '$load_error', 'View employee harus merender pesan warning dari logic self-view.');

    fwrite(STDOUT, "PASS [missing-data]: self-view employee menahan user di halaman dan menampilkan warning singkat untuk data bermasalah.\n");
}

if ($target_group === 'all' || $target_group === 'employee-self-view') {
    run_employee_self_view_group();
}

if ($target_group === 'all' || $target_group === 'navigation') {
    run_navigation_group();
}

if ($target_group === 'all' || $target_group === 'retirement') {
    run_retirement_group();
}

if ($target_group === 'all' || $target_group === 'missing-data') {
    run_missing_data_group();
}

fwrite(STDOUT, "Semua smoke group Phase 23 selesai diperiksa.\n");
