<?php
//cancello le variabili della sessione
$_SESSION = array();
//cancella il cookie
$params = session_get_cookie_params();
setcookie(session_name(),'',time()-3600*24,$params["path"],$params["domain"],
    $params["secure"],$params["httponly"]);
//cancello la sessione da disco
session_destroy();
header("location:index.php");