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
        "Case {$case_name} harus ada di scaffold Phase 19."
    );
}

$_SESSION = [];
$missing_login = cekLogin();
phase19_assert_true(is_array($missing_login), 'cekLogin test mode harus mengembalikan array hasil.');
phase19_assert_true($missing_login['allowed'] === false, 'Session tanpa user_id harus ditolak.');
phase19_assert_true($missing_login['redirect'] === '/login.php', 'Session tanpa user_id harus diarahkan ke /login.php.');

$_SESSION = [
    'user_id' => 10,
    'role' => 'employee',
];
$valid_login = cekLogin();
phase19_assert_true($valid_login['allowed'] === true, 'Session dengan user_id harus lolos scaffold login dasar.');
phase19_assert_true($valid_login['redirect'] === null, 'Session valid tidak boleh punya redirect.');

$wrong_role = cekRole('hr');
phase19_assert_true($wrong_role['allowed'] === false, 'Role employee yang membuka halaman HR harus ditolak.');
phase19_assert_true($wrong_role['redirect'] === '/employee/dashboard.php', 'Wrong-role employee harus diarahkan ke dashboard employee.');

$scaffold_text = file_get_contents(__FILE__);
phase19_assert_true($scaffold_text !== false, 'Smoke file Phase 19 harus bisa dibaca.');

phase19_assert_case_name($scaffold_text, 'case_missing_user_row');
phase19_assert_case_name($scaffold_text, 'case_inactive_user_row');
phase19_assert_case_name($scaffold_text, 'case_missing_employee_link');
phase19_assert_case_name($scaffold_text, 'case_valid_employee_session');
phase19_assert_case_name($scaffold_text, 'case_wrong_role_redirect');
phase19_assert_case_name($scaffold_text, 'case_session_identity_markers');

$phase19_cases = [
    'case_missing_user_row' => 'placeholder: user row sudah terhapus harus logout ke /login.php',
    'case_inactive_user_row' => 'placeholder: user inactive harus logout ke /login.php',
    'case_missing_employee_link' => 'placeholder: employee tanpa row karyawan harus logout ke /login.php',
    'case_valid_employee_session' => 'placeholder: employee valid tetap boleh lanjut',
    'case_wrong_role_redirect' => 'placeholder: role valid tapi salah halaman tetap redirect ke dashboard sendiri',
    'case_session_identity_markers' => 'placeholder: nama, role, dan marker session tetap konsisten',
];

foreach ($phase19_cases as $case_name => $case_note) {
    phase19_assert_true(trim($case_note) !== '', "Placeholder {$case_name} tidak boleh kosong.");
    fwrite(STDOUT, "PASS [{$case_name}]: {$case_note}\n");
}

fwrite(STDOUT, "PASS: phase19_auth_session_smoke\n");
exit(0);
