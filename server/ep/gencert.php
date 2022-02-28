<?php

require_once '../_config.php';
require_once '../_auth.php';

if (!isset($_POST['csr']) || openssl_csr_get_subject($_POST['csr']) === false) {
    header("Content-type: application/json; charset=utf-8");
    http_response_code(400);

    die(json_encode(array(
        "error" => "User Error"
    )));
}

$serial = intval(time() . rand(0, 999));

file_put_contents($cert_dir . '/u-' . $serial . '.csr', $_POST['csr']);

$privkey = null;

$ca = null;
$use_ca = false;

if (
    isset($_POST['ca']) &&
    file_exists($cert_dir . '/i-' . $_POST['ca'] . '.crt') &&
    file_exists($cert_dir . '/i-' . $_POST['ca'] . '.key')
) {

    if (
        (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) ||
        !(array_key_exists($_SERVER['PHP_AUTH_USER'], $auth) &&
            password_verify($_SERVER['PHP_AUTH_PW'], $auth[$_SERVER['PHP_AUTH_USER']]['password']))
    ) {
        header('WWW-Authenticate: Basic realm="Username and password are required to use ca sign."');
        header('Content-Type: text/plain; charset=utf-8');
        die('Auth required');
    }

    if (!in_array($_POST['ca'], $auth[$_SERVER['PHP_AUTH_USER']]['accepted_ca'], true)) {
        http_response_code(403);
        die('Forbidden');
    }

    $use_ca = true;

    $ca = openssl_x509_read(file_get_contents($cert_dir . '/i-' . $_POST['ca'] . '.crt'));
    $privkey = openssl_pkey_get_private(file_get_contents($cert_dir . '/i-' . $_POST['ca'] . '.key'));
} else {
    header("Content-type: application/json; charset=utf-8");
    http_response_code(400);

    die(json_encode(array(
        "error" => "Wrong CA"
    )));
}

$exp_day = $default_exp_day;

if ($use_ca && isset($forced_expire_days[$_POST['ca']])) {
    $exp_day = $forced_expire_days[$_POST['ca']];
} else if (isset($_POST['exp']) && is_numeric($_POST['exp'])) {
    $exp_day = intval($_POST['exp']);
}

$x509 = openssl_csr_sign(
    $_POST['csr'],
    $ca,
    $privkey,
    $days = $exp_day,
    array('digest_alg' => $_POST['digest'] ?? $default_digest_argo),
    $serial = $serial
);

if ($x509 === false) {
    http_response_code(403);

    die('Unable to sign CSR');
}

if (
    openssl_x509_export($x509, $certout) === false
) {
    header("Content-type: application/json; charset=utf-8");
    http_response_code(400);

    die(json_encode(array(
        "error" => "Failed to generate certificate"
    )));
}


header("Content-type: application/x-x509-user-cert; charset=utf-8");
header('Content-Disposition: attachment; filename="cert.crt"');

print($certout);
