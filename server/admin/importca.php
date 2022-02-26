<?php

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

if (isset($_POST['x509']) && isset($_POST['privkey']) && isset($_POST['name'])) {

    if (!preg_match('/^([A-Za-z0-9]|-|_)+$/i', $_POST['name'])) {
        http_response_code(400);
        die('Invalid name you can only use A-Z, a-z, 0-9, -, _');
    }

    if (openssl_x509_read($_POST['x509']) === false) {
        http_response_code(400);
        die('Invalid certificate');
    }

    if (openssl_pkey_get_private($_POST['privkey']) === false) {
        http_response_code(400);
        die('Invalid private key');
    }

    require_once '../_config.php';

    $x509 = $_POST['x509'];
    $privkey = $_POST['privkey'];
    $name = $_POST['name'];

    file_put_contents($cert_dir . '/i-' . $name . '.crt', $x509);
    file_put_contents($cert_dir . '/i-' . $name . '.key', $privkey);

    print('ok');

    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Import CA</title>
</head>

<body>
    <form action="importca.php" method="post">
        <label for="f-name">Cert Name</label>
        <input type="text" id="f-name" name="name" placeholder="london-headquarters" /><br />

        <label for="f-x509">X509 Certificate</label><br />
        <textarea id="f-x509" name="x509" rows="30" cols="70" placeholder="X509"></textarea><br />

        <label for="f-privkey">Private Key</label><br />
        <textarea id="f-privkey" name="privkey" rows="30" cols="70" placeholder="Private Key"></textarea><br />
        <input type="submit" value="Import" />
    </form>
</body>

</html>