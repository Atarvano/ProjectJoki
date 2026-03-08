<?php

$root = dirname(__DIR__);
$selected_group = 'all';

foreach (array_slice($argv, 1) as $argument) {
    if (strpos($argument, '--group=') === 0) {
        $selected_group = strtolower(trim(substr($argument, 8)));
    }
}

$available_groups = ['folders', 'names', 'includes', 'bridges'];

function phase21_pass($group, $case_name, $message)
{
    fwrite(STDOUT, "PASS [{$group}] [{$case_name}]: {$message}\n");
}

function phase21_fail($group, $case_name, $message)
{
    fwrite(STDERR, "FAIL [{$group}] [{$case_name}]: {$message}\n");
}

function phase21_path_exists($path)
{
    return file_exists($path) || is_dir($path);
}

function phase21_run_check($group, $case_name, $condition, $pass_message, $fail_message, &$group_failed, &$total_failed)
{
    if ($condition) {
        phase21_pass($group, $case_name, $pass_message);
        return;
    }

    phase21_fail($group, $case_name, $fail_message);
    $group_failed = true;
    $total_failed++;
}

function phase21_should_run_group($selected_group, $group)
{
    return $selected_group === 'all' || $selected_group === $group;
}

if ($selected_group !== 'all' && !in_array($selected_group, $available_groups, true)) {
    fwrite(STDERR, "FAIL: group {$selected_group} tidak dikenal. Gunakan folders, names, includes, bridges, atau all.\n");
    exit(1);
}

$total_failed = 0;
$ran_any_group = false;

if (phase21_should_run_group($selected_group, 'folders')) {
    $ran_any_group = true;
    $group_failed = false;

    phase21_run_check(
        'folders',
        'top_level_assets_folder',
        is_dir($root . '/assets'),
        'Folder assets/ tetap ada sebagai support folder top-level yang mudah ditemukan.',
        'Folder assets/ belum ditemukan di top-level proyek.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'top_level_database_folder',
        is_dir($root . '/database'),
        'Folder database/ tetap ada sebagai support folder top-level yang mudah ditemukan.',
        'Folder database/ belum ditemukan di top-level proyek.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'shared_includes_root',
        is_dir($root . '/includes'),
        'Folder includes/ tersedia sebagai root shared file.',
        'Folder includes/ belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'grouped_auth_folder',
        is_dir($root . '/includes/auth'),
        'Folder includes/auth/ sudah tersedia untuk shared auth files.',
        'Folder includes/auth/ belum tersedia sebagai target akhir grouping auth.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'grouped_layout_folder',
        is_dir($root . '/includes/layout'),
        'Folder includes/layout/ sudah tersedia untuk shared layout files.',
        'Folder includes/layout/ belum tersedia sebagai target akhir grouping layout.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'hr_logic_folder',
        is_dir($root . '/hr/logic'),
        'Folder hr/logic/ sudah tersedia untuk page logic HR.',
        'Folder hr/logic/ belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'hr_views_folder',
        is_dir($root . '/hr/views'),
        'Folder hr/views/ sudah tersedia untuk page view HR.',
        'Folder hr/views/ belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'employee_logic_folder',
        is_dir($root . '/employee/logic'),
        'Folder employee/logic/ sudah tersedia untuk page logic employee.',
        'Folder employee/logic/ belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'folders',
        'employee_views_folder',
        is_dir($root . '/employee/views'),
        'Folder employee/views/ sudah tersedia untuk page view employee.',
        'Folder employee/views/ belum tersedia.',
        $group_failed,
        $total_failed
    );

    if (!$group_failed) {
        fwrite(STDOUT, "PASS [folders]: semua cek folder target Phase 21 lolos.\n");
    }
}

if (phase21_should_run_group($selected_group, 'names')) {
    $ran_any_group = true;
    $group_failed = false;

    phase21_run_check(
        'names',
        'hr_employees_route',
        is_file($root . '/hr/employees.php'),
        'Route akhir hr/employees.php sudah tersedia.',
        'Route akhir hr/employees.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_create_route',
        is_file($root . '/hr/employee-create.php'),
        'Route akhir hr/employee-create.php sudah tersedia.',
        'Route akhir hr/employee-create.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_detail_route',
        is_file($root . '/hr/employee-detail.php'),
        'Route akhir hr/employee-detail.php sudah tersedia.',
        'Route akhir hr/employee-detail.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_edit_route',
        is_file($root . '/hr/employee-edit.php'),
        'Route akhir hr/employee-edit.php sudah tersedia.',
        'Route akhir hr/employee-edit.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_delete_route',
        is_file($root . '/hr/employee-delete.php'),
        'Route akhir hr/employee-delete.php sudah tersedia.',
        'Route akhir hr/employee-delete.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_provision_route',
        is_file($root . '/hr/employee-provision.php'),
        'Route akhir hr/employee-provision.php sudah tersedia.',
        'Route akhir hr/employee-provision.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_logic_file',
        is_file($root . '/hr/logic/employees.php'),
        'File logic hr/logic/employees.php sudah tersedia.',
        'File logic hr/logic/employees.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'names',
        'hr_employee_view_file',
        is_file($root . '/hr/views/employees.php'),
        'File view hr/views/employees.php sudah tersedia.',
        'File view hr/views/employees.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    if (!$group_failed) {
        fwrite(STDOUT, "PASS [names]: semua cek nama route akhir Phase 21 lolos.\n");
    }
}

if (phase21_should_run_group($selected_group, 'includes')) {
    $ran_any_group = true;
    $group_failed = false;

    phase21_run_check(
        'includes',
        'final_auth_guard_file',
        is_file($root . '/includes/auth/auth-guard.php'),
        'File akhir includes/auth/auth-guard.php sudah tersedia.',
        'File akhir includes/auth/auth-guard.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'includes',
        'final_layout_shell_file',
        is_file($root . '/includes/layout/dashboard-layout.php'),
        'File akhir includes/layout/dashboard-layout.php sudah tersedia.',
        'File akhir includes/layout/dashboard-layout.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'includes',
        'final_layout_topbar_file',
        is_file($root . '/includes/layout/dashboard-topbar.php'),
        'File akhir includes/layout/dashboard-topbar.php sudah tersedia.',
        'File akhir includes/layout/dashboard-topbar.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'includes',
        'final_layout_sidebar_file',
        is_file($root . '/includes/layout/dashboard-sidebar.php'),
        'File akhir includes/layout/dashboard-sidebar.php sudah tersedia.',
        'File akhir includes/layout/dashboard-sidebar.php belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'includes',
        'employee_route_contract',
        is_file($root . '/employee/dashboard.php') && strpos((string) file_get_contents($root . '/employee/dashboard.php'), "cekLogin();\ncekRole('employee');") !== false,
        'Route employee/dashboard.php masih menunjukkan langkah auth -> role secara eksplisit.',
        'Route employee/dashboard.php belum menunjukkan guard auth secara eksplisit.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'includes',
        'db_support_file',
        is_file($root . '/koneksi.php'),
        'File koneksi.php tetap tersedia sebagai DB include yang terlihat jelas.',
        'File koneksi.php tidak ditemukan.',
        $group_failed,
        $total_failed
    );

    if (!$group_failed) {
        fwrite(STDOUT, "PASS [includes]: semua cek include contract Phase 21 lolos.\n");
    }
}

if (phase21_should_run_group($selected_group, 'bridges')) {
    $ran_any_group = true;
    $group_failed = false;

    phase21_run_check(
        'bridges',
        'auth_guard_final_or_bridge',
        phase21_path_exists($root . '/includes/auth/auth-guard.php') || is_file($root . '/includes/auth-guard.php'),
        'Auth guard final path atau bridge lama tersedia selama rollout.',
        'Auth guard final path maupun bridge lama belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'bridges',
        'layout_topbar_final_or_bridge',
        phase21_path_exists($root . '/includes/layout/dashboard-topbar.php') || is_file($root . '/includes/dashboard-topbar.php'),
        'Topbar layout final path atau bridge lama tersedia selama rollout.',
        'Topbar layout final path maupun bridge lama belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'bridges',
        'employee_list_final_or_bridge',
        is_file($root . '/hr/employees.php') || is_file($root . '/hr/karyawan.php'),
        'Halaman list employee final path atau bridge lama tersedia selama rollout.',
        'Halaman list employee final path maupun bridge lama belum tersedia.',
        $group_failed,
        $total_failed
    );

    phase21_run_check(
        'bridges',
        'employee_provision_final_or_bridge',
        is_file($root . '/hr/employee-provision.php') || is_file($root . '/hr/karyawan-provision.php'),
        'Halaman provision final path atau bridge lama tersedia selama rollout.',
        'Halaman provision final path maupun bridge lama belum tersedia.',
        $group_failed,
        $total_failed
    );

    if (!$group_failed) {
        fwrite(STDOUT, "PASS [bridges]: semua cek bridge rollout Phase 21 lolos.\n");
    }
}

if (!$ran_any_group) {
    fwrite(STDERR, "FAIL: tidak ada group yang dijalankan.\n");
    exit(1);
}

if ($total_failed > 0) {
    fwrite(STDERR, "FAIL: phase21_structure_smoke menemukan {$total_failed} kegagalan.\n");
    exit(1);
}

fwrite(STDOUT, "PASS: phase21_structure_smoke\n");
exit(0);
