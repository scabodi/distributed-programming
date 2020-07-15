<?php
function start_session()
{
    if (!isset($_SESSION)) {
        session_start();
    }
}

function set_session_fields($user)
{
    $_SESSION['user'] = $user;
    $_SESSION['interaction'] = time();
    $_SESSION['logged'] = true;
    $_SESSION['posti'] = array();
}

function is_logged()
{
    $scadenza = 2 * 60; //2 minuti
    if (isset($_SESSION['user'])) {
        $now = time();
        // Se sono trascorsi più di 2 minuti dall'ultima interazione --> LOGOUT
        if ($now - $_SESSION['interaction'] > $scadenza) {
            logout();
            return false;
        }
        // Aggiorno l'interazione
        $_SESSION['interaction'] = $now;
        return true;
    }
    return false;
}

function is_page($page_to_check){
    $current_page = basename($_SERVER['PHP_SELF']);
    if(strcmp($page_to_check,$current_page)==0){
        return true;
    }
    return false;
}

function logout(){
    //cancello le variabili della sessione
    $_SESSION = array();
    //setcookie('info', null, -1, '/');
    //echo "<script type='text/javascript'>delete_cookie('acquista');delete_cookie('aggiorna');</script>";
    //cancella il cookie
    //$params = session_get_cookie_params();
    //setcookie(session_name(),'',time()-3600*24,$params["path"],$params["domain"],$params["secure"],$params["httponly"]);
    //cancello la sessione da disco
    session_destroy();
    //header("location:index.php");
}

