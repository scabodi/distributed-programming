<?php
define('DB_HOST', 'localhost');
define('DB_USER', 's257809');
define('DB_PWD', 'mbicalli');
define('DB_NAME', 's257809');
$rows = 10;
$cols = 6;
$occupati = 0;
$prenotati = 0;
$liberi = 0;

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    // La richiesta e' stata fatta su HTTPS
} else {
    // Redirect su HTTPS
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
