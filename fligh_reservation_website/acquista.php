<?php
require_once('config.php');
require_once('session_handler.php');
require_once('db_handler.php');
start_session();
if(is_logged()) {

    $con = dbConnect();
    if(isset($con)){

        $stato = 'p';
        $query_lock = "SELECT fila, posto FROM posti WHERE stato='" . $stato . "' AND user='" . $_SESSION['user'] . "' FOR UPDATE";
        $n = lock($query_lock, $con);

        if($n > 0) {

            try{
                mysqli_autocommit($con, false);

                $stato = 'p';
                $query = "SELECT fila, posto FROM posti WHERE stato='" . $stato . "' AND user='" . $_SESSION['user'] . "'";
                mysqli_prepare($con, $query);
                $result = mysqli_query($con, $query);

                if (!$result) {
                   throw new Exception("Errore - Query fallita: " . mysqli_error($con));
                } else {
                    $nrow = mysqli_num_rows($result);
                    $acquistati = array();

                    for ($i = 0; $i < $nrow; $i++) {

                        $row = mysqli_fetch_assoc($result);
                        if ($row["fila"] < 10)
                            $fila = sprintf("%02d", htmlentities($row["fila"]));
                        else
                            $fila = sprintf("%2d", htmlentities($row["fila"]));
                        $posto = htmlentities($row["posto"]);
                        $id = sprintf("#%s", $posto . "" . $fila);

                        if (in_array($id, $_SESSION['posti'])) {

                            //se il posto è ancora prenotato dall'utente loggato -> acquisto
                            $key = array_search($id, $_SESSION['posti']);
                            //unset($_SESSION['posti'][$key]);
                            $coppia = $fila . "," . $posto;
                            array_push($acquistati, $coppia);

                        }
                    }
                    mysqli_free_result($result);

                    $str = "";
                    //echo count($acquistati)." ".count($_SESSION['posti']);
                    if(count($acquistati) !== count($_SESSION['posti'])){
                        //non tutti i posti sono disponibili --> liberare tutti quelli
                        //nel vettore acquistati
                        foreach ($acquistati as $coppia) {
                            $fields = explode(",", $coppia);
                            $fila = intval($fields[0]);
                            $posto = $fields[1];
                            $query = "DELETE FROM posti WHERE fila=? AND posto=?";
                            $stmt = mysqli_prepare($con, $query);
                            mysqli_stmt_bind_param($stmt, "is", $fila, $posto);
                            if(!mysqli_stmt_execute($stmt)){
                                throw  new Exception("query eliminazione posti fallita");
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }else {
                        foreach ($acquistati as $coppia) {
                            $fields = explode(",", $coppia);
                            $fila = intval($fields[0]);
                            if ($fila < 10)
                                $fila_con_zero = sprintf("%02d", $fila);
                            else
                                $fila_con_zero = sprintf("%2d", $fila);
                            $posto = $fields[1];
                            $stato = "o";
                            $query = "UPDATE posti SET stato=? WHERE fila=? AND posto=?";
                            $stmt = mysqli_prepare($con, $query);
                            mysqli_stmt_bind_param($stmt, "sis", $stato, $fila, $posto);
                            if (!mysqli_stmt_execute($stmt)) {
                                throw  new Exception("query aggiornamento stato posto fallita");
                            }
                            mysqli_stmt_close($stmt);

                            $str .= $posto . "" . $fila_con_zero;
                            if (next($acquistati) == true) $str .= ", ";
                        }
                    }
                    if ($str !== "") {
                        echo "si- Acquistati con successo i posti " . $str;
                    } else {
                        echo "no- Spiacenti almeno uno dei posti prenotati non è più disponibile per l'acquisto.";
                    }

                }
            }catch(Exception $e){
                mysqli_rollback($con);
                $GLOBALS['errore'] = "Rollback ".$e->getMessage();
                mysqli_autocommit($con,true);
            }
            mysqli_autocommit($con, true);

        }else{
            echo "no- Non ci sono posti prenotati per essere acquistati.";
        }
        mysqli_close($con);
    }
}else{
    echo "";
}