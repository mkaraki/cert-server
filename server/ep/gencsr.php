<?php

if (!isset($_POST['privkey']) || ($pkey = openssl_pkey_get_private($_POST['privkey'])) === false) {
    http_response_code(400);
    die('Invalid private key');
}

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

$csr = openssl_csr_new($dn, $pkey, array('digest_alg' => 'sha256'));

header("Content-type: application/pkcs10; charset=utf-8");
header('Content-Disposition: attachment; filename="request.csr"');

openssl_csr_export($csr, $csrout) and print($csrout);
