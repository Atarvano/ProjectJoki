<?php
require_once __DIR__ . '/../includes/auth/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/logic/employee-detail.php';
require_once __DIR__ . '/../includes/layout/dashboard-layout.php';
