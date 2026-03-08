<?php
require_once __DIR__ . '/../includes/auth/auth-guard.php';
cekLogin();
cekRole('hr');
header('Location: /hr/reports.php');
exit;
