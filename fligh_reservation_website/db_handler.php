<?php
require_once('config.php');

function dbConnect(){

    $con = mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
    if (mysqli_connect_errno()) {
        $GLOBALS['errore'] = "Errore - collegamento al DB IMPOSSIBILE: ".mysqli_connect_error();
    }
    return $con;
}

function lock($query, $con){

    mysqli_prepare($con, $query);
    $result = mysqli_query($con, $query);
    $res = 0;

    if (!$result) {
        $GLOBALS['errore'] = "Errore - Query fallita: ".mysqli_error($con);
        return -1;
    } else {
        $nrow = mysqli_num_rows($result);

        if($nrow > 0){
            //lock effettuato
            $res = $nrow;
        }

        mysqli_free_result($result);
    }
    return $res;
}