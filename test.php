<!DOCTYPE html>
<html>
    <head>
        <title>TEST</title>
    </head>
    <body>
        <div>
        <?php
            $authOK = false;
            $user = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            if (isset($user) && isset($password) && $user === strrev($password)) {
            $authOK = true;
            }
            if (!$authOK) {
            header('WWW-Authenticate: Basic realm="Top Secret Files"');
            header('HTTP/1.0 401 Unauthorized');
            // все остальное, что здесь выводится, будет видимым
            // только в том случае, если клиент нажал кнопку отмены
            exit;
            }?>
        </div>
    </body>
</html>
