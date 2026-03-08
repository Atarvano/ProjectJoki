<?php
if (!defined('KARYAWAN_PROVISION_TEST_MODE')) {
    require_once __DIR__ . '/../includes/auth/auth-guard.php';
    cekLogin();
    cekRole('hr');
    require_once __DIR__ . '/../koneksi.php';
}

require_once __DIR__ . '/logic/employee-provision.php';
