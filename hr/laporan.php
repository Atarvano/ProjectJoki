<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
$redirect_url = '/hr/reports.php';

if ($query !== '') {
    $redirect_url .= '?' . $query;
}

header('Location: ' . $redirect_url);
exit;
