<?php

require_once '_config.php';

$files = glob($cert_dir . '/i-*.crt');

$certs = array();

foreach ($files as $f) {
    $fileary = explode('/', $f);
    $cert = openssl_x509_read(file_get_contents($f));
    $certs[end($fileary)] = openssl_x509_parse($cert);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Root CAs</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 2px 4px;
        }
    </style>
</head>

<body>
    <a href="admin/importca.php">Import</a>
    <a href="admin/newca.html">New</a>
    <table>
        <tr>
            <th>Common Name</th>
            <th>Valid From</th>
            <th>Valid To</th>
            <th>Issuer</th>
            <th>File</th>
        </tr>
        <?php
        foreach ($certs as $name => $cert) {
            $cname = explode('.', $name)[0];
        ?>
            <tr>
                <?php
                if ($cert === false) {
                ?>
                    <td colspan="3">Failed to parse certificate</td>
                <?php
                } else {
                ?>
                    <td><?= $cert['subject']['CN']; ?></td>
                    <td><?= date('Y-m-d H:i:s', $cert['validFrom_time_t']); ?></td>
                    <td><?= date('Y-m-d H:i:s', $cert['validTo_time_t']); ?></td>
                    <td><?= $cert['issuer']['CN'] ?></td>
                <?php
                }
                ?>
                <td><a href="ep/dlcert.php?f=<?= $cname ?>"><?= $name ?></a></td>
            </tr>
        <?php
        }
        ?>
    </table>
</body>

</html>