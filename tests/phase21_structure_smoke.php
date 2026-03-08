<?php

$root = dirname(__DIR__);
$target_group = 'all';

foreach ($argv as $argument) {
    if (strpos($argument, '--group=') === 0) {
        $target_group = substr($argument, 8);
    }
}

$valid_groups = ['all', 'folders', 'includes', 'names', 'bridges'];
if (!in_array($target_group, $valid_groups, true)) {
    fwrite(STDERR, "FAIL: Group tidak dikenal. Pakai --group=folders, --group=includes, --group=names, atau --group=bridges.\n");
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
    $employee_dashboard = phase21_load('employee/dashboard.php');
    $employee_logic = phase21_load('employee/logic/dashboard.php');
    $employee_view = phase21_load('employee/views/dashboard.php');
    $hr_dashboard = phase21_load('hr/dashboard.php');
    $hr_calculator = phase21_load('hr/kalkulator.php');
    $hr_export = phase21_load('hr/export.php');

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

    phase21_assert_contains($employee_dashboard, "require_once __DIR__ . '/../includes/auth/auth-guard.php';", 'Dashboard employee harus memakai auth guard grouped final.');
    phase21_assert_contains($employee_dashboard, "cekLogin();", 'Dashboard employee harus tetap memanggil cekLogin.');
    phase21_assert_contains($employee_dashboard, "cekRole('employee');", 'Dashboard employee harus tetap memanggil cekRole employee.');
    phase21_assert_contains($employee_dashboard, "require_once __DIR__ . '/../koneksi.php';", 'Dashboard employee harus tetap menampilkan include koneksi.');
    phase21_assert_contains($employee_dashboard, "require_once __DIR__ . '/logic/dashboard.php';", 'Dashboard employee harus memanggil logic dashboard final.');
    phase21_assert_contains($employee_dashboard, "require_once __DIR__ . '/../includes/layout/dashboard-layout.php';", 'Dashboard employee harus memanggil layout grouped final.');
    phase21_assert_not_contains($employee_dashboard, "require_once __DIR__ . '/../includes/cuti-calculator.php';", 'Route dashboard employee tidak boleh lagi memuat kalkulator langsung.');

    phase21_assert_contains($employee_logic, "require_once __DIR__ . '/../../includes/cuti-calculator.php';", 'Logic dashboard employee harus memuat kalkulator dari logic file.');
    phase21_assert_contains($employee_logic, "'role' => 'employee',", 'Logic dashboard employee harus tetap menyiapkan role employee.');
    phase21_assert_contains($employee_logic, "'active_nav' => 'hak-cuti',", 'Logic dashboard employee harus tetap menyiapkan nav hak-cuti.');
    phase21_assert_contains($employee_logic, "'profile_label' => " . '$profile_label,', 'Logic dashboard employee harus tetap meneruskan profile_label.');
    phase21_assert_contains($employee_logic, "'profile_role' => 'Karyawan',", 'Logic dashboard employee harus tetap meneruskan profile_role.');
    phase21_assert_contains($employee_logic, 'hitungHakCuti', 'Logic dashboard employee harus tetap menghitung hak cuti.');
    phase21_assert_contains($employee_logic, "require __DIR__ . '/../views/dashboard.php';", 'Logic dashboard employee harus memanggil view dashboard final.');

    phase21_assert_contains($employee_view, 'Hak Cuti', 'View dashboard employee harus tetap berisi markup hak cuti.');
    phase21_assert_not_contains($employee_view, 'mysqli_prepare', 'View dashboard employee tidak boleh lagi menyimpan query SQL.');
    phase21_assert_not_contains($employee_view, 'cekRole(', 'View dashboard employee tidak boleh menyimpan guard role.');

    phase21_assert_contains($hr_dashboard, "require_once __DIR__ . '/../includes/auth/auth-guard.php';", 'Dashboard HR harus memakai auth guard grouped final.');
    phase21_assert_contains($hr_dashboard, "require_once __DIR__ . '/../koneksi.php';", 'Dashboard HR harus tetap memuat koneksi secara eksplisit.');
    phase21_assert_contains($hr_dashboard, "require __DIR__ . '/../includes/layout/dashboard-layout.php';", 'Dashboard HR harus memakai layout grouped final.');
    phase21_assert_not_contains($hr_dashboard, "require_once __DIR__ . '/../includes/auth-guard.php';", 'Dashboard HR tidak boleh kembali ke shim auth lama.');
    phase21_assert_not_contains($hr_dashboard, "require __DIR__ . '/../includes/dashboard-layout.php';", 'Dashboard HR tidak boleh kembali ke shim layout lama.');

    phase21_assert_contains($hr_calculator, "require_once __DIR__ . '/../includes/auth/auth-guard.php';", 'Kalkulator HR harus memakai auth guard grouped final.');
    phase21_assert_contains($hr_calculator, "header('Location: /hr/reports.php');", 'Kalkulator HR harus menjadi bridge redirect ke reports.php.');
    phase21_assert_contains($hr_calculator, 'exit;', 'Kalkulator HR bridge harus berhenti setelah redirect.');
    phase21_assert_not_contains($hr_calculator, "require_once __DIR__ . '/../includes/auth-guard.php';", 'Kalkulator HR tidak boleh kembali ke shim auth lama.');
    phase21_assert_not_contains($hr_calculator, "require __DIR__ . '/../includes/dashboard-layout.php';", 'Kalkulator HR tidak boleh kembali ke shim layout lama.');
    phase21_assert_not_contains($hr_calculator, "require_once __DIR__ . '/../koneksi.php';", 'Kalkulator HR bridge tidak perlu lagi memuat koneksi database.');

    phase21_assert_contains($hr_export, "require_once __DIR__ . '/../includes/auth/auth-guard.php';", 'Export HR harus memakai auth guard grouped final.');
    phase21_assert_contains($hr_export, "require_once __DIR__ . '/../koneksi.php';", 'Export HR harus tetap memuat koneksi secara eksplisit.');
    phase21_assert_not_contains($hr_export, "require_once __DIR__ . '/../includes/auth-guard.php';", 'Export HR tidak boleh kembali ke shim auth lama.');

    fwrite(STDOUT, "PASS [includes]: halaman publik dan layout utama sudah menunjuk ke path grouped.\n");
}

function run_names_group()
{
    $employees_route = phase21_load('hr/employees.php');
    $employee_create_route = phase21_load('hr/employee-create.php');
    $employee_detail_route = phase21_load('hr/employee-detail.php');
    $employee_edit_route = phase21_load('hr/employee-edit.php');
    $employee_delete_route = phase21_load('hr/employee-delete.php');
    $reports_route = phase21_load('hr/reports.php');
    $employees_logic = phase21_load('hr/logic/employees.php');
    $employee_create_logic = phase21_load('hr/logic/employee-create.php');
    $employee_detail_logic = phase21_load('hr/logic/employee-detail.php');
    $employee_edit_logic = phase21_load('hr/logic/employee-edit.php');
    $employee_delete_logic = phase21_load('hr/logic/employee-delete.php');
    $reports_logic = phase21_load('hr/logic/reports.php');
    $employees_view = phase21_load('hr/views/employees.php');
    $employee_create_view = phase21_load('hr/views/employee-create.php');
    $employee_detail_view = phase21_load('hr/views/employee-detail.php');
    $employee_edit_view = phase21_load('hr/views/employee-edit.php');
    $reports_view = phase21_load('hr/views/reports.php');

    phase21_assert_contains($employees_route, "require_once __DIR__ . '/logic/employees.php';", 'Route employees harus memanggil logic/employees.php.');
    phase21_assert_contains($employees_route, "require_once __DIR__ . '/../includes/layout/dashboard-layout.php';", 'Route employees harus memanggil dashboard layout grouped.');
    phase21_assert_contains($employee_create_route, "require_once __DIR__ . '/logic/employee-create.php';", 'Route employee-create harus memanggil logic/employee-create.php.');
    phase21_assert_contains($employee_detail_route, "require_once __DIR__ . '/logic/employee-detail.php';", 'Route employee-detail harus memanggil logic/employee-detail.php.');
    phase21_assert_contains($employee_edit_route, "require_once __DIR__ . '/logic/employee-edit.php';", 'Route employee-edit harus memanggil logic/employee-edit.php.');
    phase21_assert_contains($employee_delete_route, "require_once __DIR__ . '/logic/employee-delete.php';", 'Route employee-delete harus memanggil logic/employee-delete.php.');
    phase21_assert_contains($reports_route, "require_once __DIR__ . '/logic/reports.php';", 'Route reports harus memanggil logic/reports.php.');
    phase21_assert_contains($reports_route, "require_once __DIR__ . '/../includes/layout/dashboard-layout.php';", 'Route reports harus memanggil dashboard layout grouped.');

    phase21_assert_contains($employees_logic, 'LEFT JOIN users u ON u.karyawan_id = k.id', 'Logic employees harus tetap memuat join user untuk status akun login.');
    phase21_assert_contains($employees_logic, "require __DIR__ . '/../views/employees.php';", 'Logic employees harus merender view employees.');
    phase21_assert_contains($employee_create_logic, 'INSERT INTO karyawan', 'Logic employee-create harus menyimpan data karyawan.');
    phase21_assert_contains($employee_create_logic, "header('Location: /hr/employees.php');", 'Logic employee-create harus kembali ke route akhir employees.php.');
    phase21_assert_contains($employee_detail_logic, "require __DIR__ . '/../views/employee-detail.php';", 'Logic employee-detail harus merender view employee-detail.');
    phase21_assert_contains($employee_edit_logic, "require __DIR__ . '/../views/employee-edit.php';", 'Logic employee-edit harus merender view employee-edit.');
    phase21_assert_contains($employee_edit_logic, "header('Location: /hr/employees.php');", 'Logic employee-edit harus kembali ke route akhir employees.php.');
    phase21_assert_contains($employee_delete_logic, 'DELETE FROM karyawan', 'Logic employee-delete harus menghapus data karyawan.');
    phase21_assert_contains($employee_delete_logic, "header('Location: /hr/employees.php');", 'Logic employee-delete harus kembali ke route akhir employees.php.');
    phase21_assert_contains($reports_logic, "require_once __DIR__ . '/../../includes/cuti-calculator.php';", 'Logic reports harus memuat kalkulator dari logic file.');
    phase21_assert_contains($reports_logic, 'ORDER BY nik ASC, nama ASC', 'Logic reports harus tetap mengurutkan laporan berdasarkan NIK lalu nama.');
    phase21_assert_contains($reports_logic, "'active_nav' => 'reports',", 'Logic reports harus menandai nav reports sebagai aktif.');
    phase21_assert_contains($reports_logic, "require __DIR__ . '/../views/reports.php';", 'Logic reports harus merender view reports.');

    phase21_assert_contains($employees_view, '/hr/employee-create.php', 'View employees harus menaut ke route create English.');
    phase21_assert_contains($employees_view, '/hr/employee-detail.php?id=', 'View employees harus menaut ke route detail English.');
    phase21_assert_contains($employees_view, '/hr/employee-edit.php?id=', 'View employees harus menaut ke route edit English.');
    phase21_assert_contains($employees_view, '/hr/employee-delete.php', 'View employees harus submit ke route delete English.');
    phase21_assert_contains($employee_create_view, 'action="/hr/employee-create.php"', 'View create harus submit ke route create English.');
    phase21_assert_contains($employee_detail_view, 'Detail Karyawan', 'View detail harus tetap memuat judul Detail Karyawan.');
    phase21_assert_contains($employee_detail_view, '/hr/employee-provision.php', 'View detail harus submit provision ke route English final.');
    phase21_assert_contains($employee_edit_view, 'action="/hr/employee-edit.php?id=', 'View edit harus submit ke route edit English.');
    phase21_assert_contains($reports_view, 'employee-detail.php?id=', 'View reports harus menaut ke route detail English.');
    phase21_assert_contains($reports_view, 'href="reports.php"', 'View reports harus memakai route reports.php saat reset filter.');
    phase21_assert_not_contains($reports_view, 'mysqli_query', 'View reports tidak boleh menyimpan query SQL.');

    fwrite(STDOUT, "PASS [names]: route English dan split route/logic/view terdeteksi.\n");
}

function run_bridges_group()
{
    $old_list = phase21_load('hr/karyawan.php');
    $old_create = phase21_load('hr/karyawan-tambah.php');
    $old_detail = phase21_load('hr/karyawan-detail.php');
    $old_edit = phase21_load('hr/karyawan-edit.php');
    $old_delete = phase21_load('hr/karyawan-hapus.php');
    $sidebar = phase21_load('includes/layout/dashboard-sidebar.php');
    $dashboard = phase21_load('hr/dashboard.php');
    $calculator = phase21_load('hr/kalkulator.php');
    $report = phase21_load('hr/laporan.php');
    $provision = phase21_load('hr/karyawan-provision.php');
    $employees_view = phase21_load('hr/views/employees.php');

    phase21_assert_contains($old_list, "/hr/employees.php", 'Bridge karyawan.php harus mengarah ke employees.php.');
    phase21_assert_contains($old_create, "/hr/employee-create.php", 'Bridge karyawan-tambah.php harus mengarah ke employee-create.php.');
    phase21_assert_contains($old_detail, "/hr/employee-detail.php", 'Bridge karyawan-detail.php harus mengarah ke employee-detail.php.');
    phase21_assert_contains($old_edit, "/hr/employee-edit.php", 'Bridge karyawan-edit.php harus mengarah ke employee-edit.php.');
    phase21_assert_contains($old_delete, "require_once __DIR__ . '/employee-delete.php';", 'Bridge karyawan-hapus.php harus handoff ke employee-delete.php.');
    phase21_assert_contains($sidebar, "'href' => '/hr/employees.php'", 'Sidebar HR harus memakai route employees.php.');
    phase21_assert_contains($dashboard, "'link' => 'reports.php'", 'Dashboard HR helper card harus memakai route reports.php sebagai jalur review utama.');
    phase21_assert_contains($calculator, "header('Location: /hr/reports.php');", 'Bridge kalkulator HR harus mengarah ke reports.php.');
    phase21_assert_contains($calculator, 'exit;', 'Bridge kalkulator HR harus berhenti setelah redirect.');
    phase21_assert_not_contains($calculator, '/hr/employees.php', 'Bridge kalkulator HR tidak boleh lagi mengarahkan ke employees.php.');
    phase21_assert_not_contains($calculator, '/hr/employee-detail.php?id=', 'Bridge kalkulator HR tidak boleh lagi merender link detail English.');
    phase21_assert_contains($report, "/hr/reports.php", 'Bridge laporan.php harus mengarah ke reports.php.');
    phase21_assert_contains($provision, "require_once __DIR__ . '/employee-provision.php';", 'Bridge karyawan-provision.php harus handoff ke employee-provision.php.');
    phase21_assert_contains($employees_view, '/hr/employee-provision.php', 'View employees harus submit provision ke route English final.');

    fwrite(STDOUT, "PASS [bridges]: bridge file dan link rollout ke route English terdeteksi.\n");
}

if ($target_group === 'all' || $target_group === 'folders') {
    run_folders_group();
}

if ($target_group === 'all' || $target_group === 'includes') {
    run_includes_group();
}

if ($target_group === 'all' || $target_group === 'names') {
    run_names_group();
}

if ($target_group === 'all' || $target_group === 'bridges') {
    run_bridges_group();
}

fwrite(STDOUT, "PASS: phase21_structure_smoke\n");
exit(0);
