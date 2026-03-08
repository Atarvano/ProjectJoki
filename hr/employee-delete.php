<?php
require_once __DIR__ . '/../includes/auth/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/logic/employee-delete.php';
