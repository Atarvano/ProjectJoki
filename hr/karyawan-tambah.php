<?php
$query_string = $_SERVER['QUERY_STRING'] ?? '';
$redirect_to = '/hr/employee-create.php';

if ($query_string !== '') {
    $redirect_to .= '?' . $query_string;
}

header('Location: ' . $redirect_to);
exit;
