<?php
require_once '../_config.php';
require_once '../_auth.php';

if (
    !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    !(array_key_exists($_SERVER['PHP_AUTH_USER'], $admins) &&
        password_verify($_SERVER['PHP_AUTH_PW'], $admins[$_SERVER['PHP_AUTH_USER']]['password']))
) {
    header('WWW-Authenticate: Basic realm="Username and password are required to use ca sign."');
    header('Content-Type: text/plain; charset=utf-8');
    die('Auth required');
}

if (!isset($_POST['name']) || !preg_match('/^([A-Za-z0-9]|-|_)+$/i', $_POST['name'])) {
    http_response_code(400);
    die('Invalid name. You can only use A-Z, a-z, 0-9, -, _');
}

$privkey = openssl_pkey_new(array(
    "private_key_bits" => $_POST['bits'] ?? $default_key_bits,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
));

if (!isset($_POST['cn']) || !isset($_POST['country']) || !isset($_POST['state']) || !isset($_POST['organization'])) {
    http_response_code(400);
    die('cn / country / state / organization are required');
}

$dn = array(
    "countryName" => $_POST['country'],
    "stateOrProvinceName" => $_POST['state'],
    "organizationName" => $_POST['organization'],
    "commonName" => $_POST['cn'],
);

isset($_POST['email']) and $dn['emailAddress'] = $_POST['email'];
isset($_POST['locality']) and $dn['localityName'] = $_POST['locality'];
isset($_POST['organizationUnit']) and $dn['organizationalUnitName'] = $_POST['organizationUnit'];

$csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));

$exp_day = $default_exp_day;

if (isset($_POST['exp']) && is_numeric($_POST['exp'])) {
    $exp_day = intval($_POST['exp']);
}

$x509 = openssl_csr_sign(
    $csr,
    null,
    $privkey,
    $exp_day,
    array('digest_alg' => $_POST['digest'] ?? $default_digest_argo),
);

$name = $_POST['name'];

openssl_x509_export_to_file($x509, $cert_dir . '/i-' . $name . '.crt');
openssl_pkey_export_to_file($privkey, $cert_dir . '/i-' . $name . '.key');
