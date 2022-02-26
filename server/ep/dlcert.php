<?php
require_once '../_config.php';
require_once '../_auth.php';

if (!isset($_GET['f']) || !file_exists($cert_dir . '/' . $_GET['f'] . '.crt')) {
    http_response_code(404);
    die('File not found');
}

header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename="' . $_GET['f'] . '.crt"');

readfile($cert_dir . '/' . $_GET['f'] . '.crt');
