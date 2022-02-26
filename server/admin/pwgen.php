<?php

if (isset($_POST['pw'])) {
    $hash = password_hash($_POST['pw'], PASSWORD_DEFAULT);
    print($hash);
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Password Hash Generator</title>
</head>

<body>
    <form action="pwgen.php" method="post">
        <label for="f-pw">Password</label>
        <input type="password" name="pw" id="f-pw" /><br />
        <input type="submit" value="Generate Hash" />
    </form>
</body>

</html>