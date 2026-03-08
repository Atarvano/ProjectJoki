<?php
require_once __DIR__ . '/../includes/auth/auth-guard.php';
cekLogin();
cekRole('employee');
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/logic/dashboard.php';
require_once __DIR__ . '/../includes/layout/dashboard-layout.php';
