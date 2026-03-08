<?php

$root = dirname(__DIR__);

define('AUTH_GUARD_TEST_MODE', true);

require_once $root . '/includes/auth-guard.php';

function phase19_fail($message)
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

function phase19_assert_true($condition, $message)
{
    if (!$condition) {
        phase19_fail($message);
    }
}

function phase19_assert_case_name($content, $case_name)
{
    phase19_assert_true(
        strpos($content, $case_name) !== false,
        "Case {$case_name} harus ada di smoke test Phase 19."
    );
}

function phase19_assert_equals($expected, $actual, $message)
{
    phase19_assert_true($expected === $actual, $message);
}

function phase19_reset_test_data()
{
    $GLOBALS['AUTH_GUARD_TEST_USERS'] = [];
    $GLOBALS['AUTH_GUARD_TEST_KARYAWAN'] = [];
    $_SESSION = [];
}

$_SESSION = [];
$missing_login = cekLogin();
phase19_assert_true(is_array($missing_login), 'cekLogin test mode harus mengembalikan array hasil.');
phase19_assert_true($missing_login['allowed'] === false, 'Session tanpa user_id harus ditolak.');
phase19_assert_true($missing_login['redirect'] === '/login.php', 'Session tanpa user_id harus diarahkan ke /login.php.');

phase19_reset_test_data();
$GLOBALS['AUTH_GUARD_TEST_USERS'][10] = [
    'id' => 10,
    'username' => 'EMP001',
    'karyawan_id' => 99,
    'is_active' => 1,
];
$_SESSION = [
    'user_id' => 10,
    'role' => 'employee',
    'username' => 'EMP001',
    'karyawan_id' => 99,
    'nama' => 'Budi',
];
$valid_login = cekLogin();
phase19_assert_true($valid_login['allowed'] === true, 'Session dengan users row aktif harus lolos cekLogin.');
phase19_assert_true($valid_login['redirect'] === null, 'Session valid tidak boleh punya redirect.');

phase19_reset_test_data();
$_SESSION = [
    'user_id' => 22,
    'role' => 'employee',
    'username' => 'EMP022',
    'karyawan_id' => 220,
    'nama' => 'Sinta',
];
$case_missing_user_row = cekLogin();
phase19_assert_equals(false, $case_missing_user_row['allowed'], 'User row yang hilang harus ditolak.');
phase19_assert_equals('/login.php', $case_missing_user_row['redirect'], 'User row yang hilang harus diarahkan ke login.');
phase19_assert_equals(true, $case_missing_user_row['session_cleared'], 'User row yang hilang harus membersihkan session.');
phase19_assert_equals([], $_SESSION, 'Session harus kosong setelah user row hilang.');

phase19_reset_test_data();
$GLOBALS['AUTH_GUARD_TEST_USERS'][23] = [
    'id' => 23,
    'username' => 'EMP023',
    'karyawan_id' => 223,
    'is_active' => 0,
];
$_SESSION = [
    'user_id' => 23,
    'role' => 'employee',
    'username' => 'EMP023',
    'karyawan_id' => 223,
    'nama' => 'Rina',
];
$case_inactive_user_row = cekLogin();
phase19_assert_equals(false, $case_inactive_user_row['allowed'], 'User inactive harus ditolak.');
phase19_assert_equals('/login.php', $case_inactive_user_row['redirect'], 'User inactive harus diarahkan ke login.');
phase19_assert_equals(true, $case_inactive_user_row['session_cleared'], 'User inactive harus membersihkan session.');
phase19_assert_equals([], $_SESSION, 'Session inactive harus dikosongkan.');

phase19_reset_test_data();
$GLOBALS['AUTH_GUARD_TEST_USERS'][24] = [
    'id' => 24,
    'username' => 'EMP024',
    'karyawan_id' => 224,
    'is_active' => 1,
];
$_SESSION = [
    'user_id' => 24,
    'role' => 'employee',
    'username' => 'EMP024',
    'karyawan_id' => 224,
    'nama' => 'Dewi',
];
$case_missing_employee_link = cekRole('employee');
phase19_assert_equals(false, $case_missing_employee_link['allowed'], 'Employee tanpa row karyawan harus ditolak.');
phase19_assert_equals('/login.php', $case_missing_employee_link['redirect'], 'Employee tanpa row karyawan harus diarahkan ke login.');
phase19_assert_equals(true, $case_missing_employee_link['session_cleared'], 'Employee tanpa row karyawan harus membersihkan session.');
phase19_assert_equals([], $_SESSION, 'Session employee tanpa row karyawan harus kosong.');

phase19_reset_test_data();
$GLOBALS['AUTH_GUARD_TEST_USERS'][25] = [
    'id' => 25,
    'username' => 'EMP025',
    'karyawan_id' => 225,
    'is_active' => 1,
];
$GLOBALS['AUTH_GUARD_TEST_KARYAWAN'][225] = [
    'id' => 225,
    'nama' => 'Fajar',
];
$_SESSION = [
    'user_id' => 25,
    'role' => 'employee',
    'username' => 'EMP025',
    'karyawan_id' => 225,
    'nama' => 'Fajar',
];
$case_valid_employee_session = cekRole('employee');
phase19_assert_equals(true, $case_valid_employee_session['allowed'], 'Employee valid harus boleh lanjut.');
phase19_assert_equals(null, $case_valid_employee_session['redirect'], 'Employee valid tidak boleh redirect.');

phase19_reset_test_data();
$GLOBALS['AUTH_GUARD_TEST_USERS'][1] = [
    'id' => 1,
    'username' => 'HR0001',
    'karyawan_id' => null,
    'is_active' => 1,
];
$_SESSION = [
    'user_id' => 1,
    'role' => 'hr',
    'username' => 'HR0001',
    'karyawan_id' => null,
    'nama' => 'HR0001',
];
$case_wrong_role_redirect = cekRole('employee');
phase19_assert_equals(false, $case_wrong_role_redirect['allowed'], 'HR yang buka halaman employee harus ditolak.');
phase19_assert_equals('/hr/dashboard.php', $case_wrong_role_redirect['redirect'], 'Wrong-role HR harus diarahkan ke dashboard HR.');
phase19_assert_equals(false, $case_wrong_role_redirect['session_cleared'], 'Wrong-role valid tidak boleh dianggap logout.');
phase19_assert_true(isset($_SESSION['user_id']), 'Wrong-role valid harus mempertahankan session.');

$scaffold_text = file_get_contents(__FILE__);
phase19_assert_true($scaffold_text !== false, 'Smoke file Phase 19 harus bisa dibaca.');

phase19_assert_case_name($scaffold_text, 'case_missing_user_row');
phase19_assert_case_name($scaffold_text, 'case_inactive_user_row');
phase19_assert_case_name($scaffold_text, 'case_missing_employee_link');
phase19_assert_case_name($scaffold_text, 'case_valid_employee_session');
phase19_assert_case_name($scaffold_text, 'case_wrong_role_redirect');
phase19_assert_case_name($scaffold_text, 'case_session_identity_markers');

$auth_guard_text = file_get_contents($root . '/includes/auth-guard.php');
phase19_assert_true($auth_guard_text !== false, 'File auth guard harus bisa dibaca.');
phase19_assert_true(strpos($auth_guard_text, 'SELECT id, username, karyawan_id, is_active FROM users WHERE id = ? LIMIT 1') !== false, 'Guard harus query users live setiap request.');
phase19_assert_true(strpos($auth_guard_text, 'SELECT id FROM karyawan WHERE id = ? LIMIT 1') !== false, 'Guard employee harus cek row karyawan live.');

$employee_dashboard_text = file_get_contents($root . '/employee/dashboard.php');
phase19_assert_true($employee_dashboard_text !== false, 'File dashboard employee harus bisa dibaca.');
phase19_assert_true(strpos($employee_dashboard_text, "cekLogin();\ncekRole('employee');") !== false, 'Dashboard employee harus tetap guard-first.');
phase19_assert_true(strpos($employee_dashboard_text, 'Akun Anda belum terhubung ke data karyawan') === false, 'Dashboard employee tidak boleh lagi mengandalkan fallback akun belum terhubung sebagai proteksi utama.');
phase19_assert_true(strpos($employee_dashboard_text, 'Data karyawan untuk akun ini tidak ditemukan') === false, 'Dashboard employee tidak boleh lagi memakai fallback row hilang sebagai proteksi utama.');
phase19_assert_true(strpos($employee_dashboard_text, 'profile_label') !== false, 'Marker profile_label harus tetap ada di dashboard employee.');
phase19_assert_true(strpos($employee_dashboard_text, 'profile_role') !== false, 'Marker profile_role harus tetap ada di dashboard employee.');
phase19_assert_true(strpos($employee_dashboard_text, '$_SESSION[\'nama\']') !== false, 'Dashboard employee harus tetap memakai nama dari session.');

fwrite(STDOUT, "PASS [case_missing_user_row]: user row hilang dipaksa logout ke /login.php.\n");
fwrite(STDOUT, "PASS [case_inactive_user_row]: user inactive dipaksa logout ke /login.php.\n");
fwrite(STDOUT, "PASS [case_missing_employee_link]: employee tanpa row karyawan dipaksa logout ke /login.php.\n");
fwrite(STDOUT, "PASS [case_valid_employee_session]: employee valid tetap boleh lanjut.\n");
fwrite(STDOUT, "PASS [case_wrong_role_redirect]: wrong-role valid tetap redirect ke dashboard sendiri.\n");
fwrite(STDOUT, "PASS [case_session_identity_markers]: marker identity session tetap ada di dashboard employee.\n");

fwrite(STDOUT, "PASS: phase19_auth_session_smoke\n");
exit(0);
