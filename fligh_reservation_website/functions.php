<?php
require_once('config.php');
require_once('session_handler.php');
require_once('db_handler.php');

function getNameOfPage(){

    $page = explode("/", $_SERVER['PHP_SELF']);
    $name = explode(".",$page[2]);

    if($name[0] === "register")
        return "registrazione";
    elseif ($name[0] === "index")
        return "home";
    return $name[0];
}

function resetStatoDB(){
    statoInizialeUtenti();
    statoInizialePosti();
}

function creaTablella() {
    $rows = $GLOBALS['rows'];
    $cols = $GLOBALS['cols'];

    if($cols > 12){
        $GLOBALS['errore'] = "Errore: l'aereo può avere un massimo di 12 colonne!";
        return;
    }
    $UPPERCASE_LETTERS = range(chr(65),chr(65+$cols));

    //echo "Rows = ".$rows." Cols = ".$cols."\n";
    echo "<table class='table-borderless' id='tabella' style='align-content: center'>";
    for($i=0; $i<$rows; $i++){
        echo "<tr>";
        for($j=0; $j<$cols; $j++){
            $k = $i+1;
            if($k < 10) {
                $fila = sprintf("%02d", $k);
            }else {
                $fila = sprintf("%2d", $k);
            }
            $posto =$UPPERCASE_LETTERS[$j];
            $id = $posto."".$fila;

            ?>
            <td><button id="<?php echo $id;?>" type="button" class="btn" style='background-color: green'
                    onclick="makeRequest(this.id)">
                    <?php echo $id;?></button></td>
            <?php
        }
        echo "</tr>";
    }
    echo"</table><br>";
    //resetStatoDB();
}


function statoInizialePosti(){

    $occupati = array(2=>"B", 3=>"B", 4=>"B");
    $prenotati = array("A"=>4, "D"=>4, "F"=>4);
    $u1 = "u1@p.it";
    $u2 = "u2@p.it";

    $con = dbConnect();

    if(isset($con)){

        $query = "DELETE FROM posti";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        foreach ($occupati as $fila => $posto) {
            $stato = 'o';
            $query = "INSERT INTO posti (fila, posto, stato, user) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "isss", $fila, $posto, $stato, $u2);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        foreach ($prenotati as $posto => $fila) {
            $stato = 'p';
            if($posto === "F")
                $u = $u2;
            else
                $u = $u1;
            $query = "INSERT INTO posti (fila, posto, stato, user) VALUES (?,?,?,?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "isss", $fila, $posto, $stato, $u);
            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }
        mysqli_close($con);
    }
}

function statoInizialeUtenti(){

    $u1 = "u1@p.it";
    $u2 = "u2@p.it";
    $p1 = md5("p1");
    $p2 = md5("p2");

    $con = dbConnect();

    if(isset($con)){

        $query = "DELETE FROM utenti";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $query = "INSERT INTO utenti (user, pwd) VALUES (?,?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ss", $u1, $p1);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $query = "INSERT INTO utenti (user, pwd) VALUES (?,?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ss", $u2, $p2);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_close($con);
    }

}
function aggiornaTabella(){

    $con = dbConnect();

    if(isset($con)){

        //reset variabili globali dello stato della mappa
        $GLOBALS['occupati'] = 0;
        $GLOBALS['prenotati'] = 0;

        $query_lock = "SELECT * FROM posti FOR UPDATE";

        if(lock($query_lock, $con) > 0) {

            if(is_logged()){
                //resetto i posti prenotati e acquistabili
                $_SESSION['posti'] = array();
            }

            try {
                mysqli_autocommit($con,false);

                $query = "SELECT fila, posto, stato, user FROM posti";
                mysqli_prepare($con, $query);
                $result = mysqli_query($con, $query);

                if (!$result) {
                    throw new Exception("Errore - Query fallita: " . mysqli_error($con));
                } else {
                    $nrow = mysqli_num_rows($result);

                    for ($i = 1; $i <= $nrow; $i++) {

                        $row = mysqli_fetch_assoc($result);
                        if ($row["fila"] < 10) {
                            $fila = sprintf("%02d", $row["fila"]);
                        } else {
                            $fila = sprintf("%2d", $row["fila"]);
                            //echo $fila;
                        }
                        //echo $fila;
                        $posto = htmlentities($row["posto"]);
                        $stato = htmlentities($row["stato"]);
                        $color = "green";
                        $id = sprintf("#%s", $posto . "" . $fila);
                        $user = htmlentities($row['user']);

                        //echo $fila." ".$posto." ".$stato."<br>";

                        if ($stato == 'p') {
                            if (is_logged() && $user == $_SESSION['user']) {
                                $color = "yellow";
                                array_push( $_SESSION['posti'], $id);
                                //echo count($_SESSION['posti']);
                            } else {
                                $color = "orange";
                                $GLOBALS['prenotati']++;
                            }
                        } elseif ($stato == 'o') {
                            $color = "red";
                            $GLOBALS['occupati']++;
                        } ?>
                        <script>
                            $(document).ready(function () {
                                $("<?php echo $id;?>").css("background-color", "<?php echo $color;?>");
                            });
                        </script>
                        <?php
                    }
                    mysqli_free_result($result);

                }
            }catch (Exception $e){
                mysqli_rollback($con);
                $GLOBALS['errore'] = "Rollback ".$e->getMessage();
                mysqli_autocommit($con,true);
            }
            mysqli_autocommit($con, true);
        }
        mysqli_close($con);

        $GLOBALS['liberi'] = ($GLOBALS['rows']*$GLOBALS['cols'])-($GLOBALS['occupati']+$GLOBALS['prenotati']);

        if($GLOBALS['occupati'] == ($GLOBALS['rows']*$GLOBALS['cols'])){
            $GLOBALS['errore'] = "Attenzione: tutti i posti sono stati acquistati. Non è più possibile effettuare operazioni.";
        }
    }
}

?>