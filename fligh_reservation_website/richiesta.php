<?php
require_once('config.php');
require_once('session_handler.php');
require_once('db_handler.php');
start_session();

if(is_logged()) {

    $con = dbConnect();
    if(isset($con)){

        $fila = mysqli_real_escape_string($con, $_POST['fila']);
        $posto = mysqli_real_escape_string($con, $_POST['posto']);

        if($fila < 10)
            $fila_con_zero = sprintf( "%02d",$fila);
        else
            $fila_con_zero = sprintf( "%2d",$fila);
        $id = sprintf("#%s", $posto."".$fila_con_zero);
        $color = $_POST['color'];

        $query_lock = "SELECT * FROM posti WHERE fila='".$fila."' AND posto='".$posto."' FOR UPDATE";
        $n = lock($query_lock, $con);

        if($n >= 0) {
            try {
                mysqli_autocommit($con, false);
                if ($n > 0) {
                    if ($color == "yellow") {

                        $query = "SELECT * FROM posti WHERE fila='".$fila."' AND posto='".$posto."' AND user='".$_SESSION['user']."'";
                        $stmt = mysqli_prepare($con, $query);
                        $result = mysqli_query($con, $query);
                        if(!$result){
                            throw new Exception("query selezione posto fallita");
                        }else{
                            if(mysqli_num_rows($result)===0){
                                //prenotato da qualcun altro
                                $color = "orange";
                                $msg = "Non è possibile liberare il posto " . $posto . "" . $fila_con_zero . " in quanto già prenotato da un altro utente.";
                                mysqli_free_result($result);
                            }else{
                                mysqli_free_result($result);
                                //posto liberabile in quanto ancora in possesso dell'utente loggato
                                $query = "DELETE FROM posti WHERE fila=? AND posto=?";
                                $stmt = mysqli_prepare($con, $query);
                                mysqli_stmt_bind_param($stmt, "is", $fila, $posto);
                                if(!mysqli_stmt_execute($stmt)){
                                    throw new Exception("query cancellazione prenotazione del posto fallita");
                                }
                                mysqli_stmt_close($stmt);
                                $color = "green";

                                $msg = "Posto " . $posto . "" . $fila_con_zero . " liberato con successo.";
                            }
                        }
                    } else {

                        $query = "SELECT stato FROM posti WHERE fila=? AND posto=?";
                        $stmt = mysqli_prepare($con, $query);
                        mysqli_stmt_bind_param($stmt, "is", $fila, $posto);
                        if(!mysqli_stmt_execute($stmt)){
                            throw new Exception("query selezione stato del posto fallita");
                        }
                        mysqli_stmt_store_result($stmt);

                        $row = mysqli_stmt_num_rows($stmt);

                        if ($row == 1) {
                            mysqli_stmt_bind_result($stmt, $stato);
                            mysqli_stmt_fetch($stmt);
                            if ($stato == 'o') {
                                //non è possibile prenotare il posto perchè già acquistato
                                $color = "red";
                                $msg = "Attenzione impossibile prenotare posto ".$posto ."".$fila_con_zero.
                            " in quanto già acquistato da un altro utente ";
                            } else {
                                //colorare la casella di giallo
                                $color = "yellow";
                            }
                            mysqli_stmt_close($stmt);
                            if ($color == "yellow") {
                                //cambiare l'utente legato alla prenotazione e
                                $query = "UPDATE posti SET user=? WHERE fila=? AND posto=?";
                                $stmt = mysqli_prepare($con, $query);
                                mysqli_stmt_bind_param($stmt, "sis", $_SESSION['user'], $fila, $posto);
                                if(!mysqli_stmt_execute($stmt)){
                                    throw new Exception("query modifica prenotazione del posto fallita");
                                }
                                mysqli_stmt_close($stmt);
                            }
                        }
                    }
                } else if ($n == 0) {
                    //posto libero: aggiungo il posto con stato p e user loggato
                    $stato = 'p';
                    //echo $stato;
                    $query = "INSERT INTO posti (fila, posto, stato, user) VALUES (?,?,?,?)";
                    $stmt = mysqli_prepare($con, $query);
                    //mysqli_stmt_bind_param($stmt, "isss", $fila, $posto, $stato, $_SESSION['user']);
                    $user = $_SESSION['user'];
                    mysqli_stmt_bind_param($stmt, "isss", $fila, $posto, $stato, $user);
                    if(!mysqli_stmt_execute($stmt)){
                        throw new Exception("query inserimento prenotazione del posto fallita");
                    }
                    mysqli_stmt_close($stmt);

                    $color = "yellow";
                }
            }catch(Exception $e){
                mysqli_rollback($con);
                $GLOBALS['errore'] = "Rollback ".$e->getMessage();
                mysqli_autocommit($con,true);
            }
            mysqli_autocommit($con, true);
            mysqli_close($con);

            if ($color == "yellow") {
                //aggiungo il posto nella lista di posti gialli
                if ($fila < 10) {
                    $fila_con_zero = sprintf("%02d", $fila);
                } else {
                    $fila_con_zero = sprintf("%2d", $fila);
                }

                $id = sprintf("#%s", $posto . "" . $fila_con_zero);
                array_push($_SESSION['posti'], $id);
                $msg = "Posto " . $posto . "" . $fila_con_zero . " prenotato con successo.";
            }
            echo $color."-".$msg;
        }
    }
}