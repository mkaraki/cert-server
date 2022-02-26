<?php
require_once '../_config.php';

$privkey = openssl_pkey_new(array(
    "private_key_bits" => $_POST['bits'] ?? $default_key_bits,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
));

header("Content-type: application/pkcs8; charset=utf-8");
header('Content-Disposition: attachment; filename="privkey.key"');

openssl_pkey_export($privkey, $pkeyout) and print($pkeyout);
