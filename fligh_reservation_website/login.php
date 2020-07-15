<?php
require_once('config.php');
require_once('session_handler.php');
require_once('functions.php');
start_session();
if(is_logged()){
    header("location:index.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(isset($_POST["submit"])) {
        //controlli sull'input ---> devono essere presenti sia user che password
        if (empty($_POST['name'])) {
            //echo "user ".$_POST['name'];
            $GLOBALS['errore'] = "Inserire uno username";
        } elseif (empty($_POST['pwd'])) {
            //echo " pass ".$_POST['pwd'];
            $GLOBALS['errore'] = "Inserire una password";
        } else {

            $user = trim($_POST['name']);
            $pass = trim($_POST['pwd']);

            //controlli di scrittura di user e pwd
            $re1 = "/[a-z]+/";
            $re2 = "/[A-Z0-9]+";

            if (!filter_var($user, FILTER_VALIDATE_EMAIL))
                $GLOBALS['errore'] = "Username non corretto! Deve essere un indirizzo email valido nel formato 'username@dominio'!";
            elseif (!preg_match($re1, $pass) && !preg_match($re2, $pass))
                $GLOBALS['errore'] = " Password non corretta! Deve contenere almeno un carattere minuscolo, un carattere maiuscolo o uno numerico!";
            else {
                $con = mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
                if (mysqli_connect_errno()) {
                    $GLOBALS['errore'] = "Errore - collegamento al DB IMPOSSIBILE: ".mysqli_connect_error();
                } else {

                    $query = "SELECT user,pwd FROM utenti WHERE user=? AND pwd=?";
                    $hash = md5($pass);
                    $stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($stmt, "ss", $user, $hash);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    $rows = mysqli_stmt_num_rows($stmt);

                    if ($rows == 1) {

                        mysqli_stmt_bind_result($stmt, $user, $pass);
                        mysqli_stmt_fetch($stmt);
                        set_session_fields($user);
                        mysqli_stmt_close($stmt);
                        //redirect alla pagina per la prenotazione dei posti index.php
                        header("location:index.php");

                    } elseif ($rows == 0) {

                        $query = "SELECT user FROM utenti WHERE user=?";
                        $stmt = mysqli_prepare($con, $query);
                        mysqli_stmt_bind_param($stmt, "s", $user);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                        $rows = mysqli_stmt_num_rows($stmt);

                        if($rows == 1){
                            //utente presente ma password sbagliata
                            $GLOBALS['errore'] = "L'utente richiesto è presente, ma la password inserita è sbagliata";

                        }else {
                            //utente non presente
                            $GLOBALS['errore'] = "L'utente richiesto non è presente nel database.<br>
                                                Per registrarsi cliccare <a href='register.php'>qui</a>";
                        }
                    }
                    mysqli_close($con);
                }
            }
        }
    }
}
?>
<?php require_once('header.php');?>
    <div class="row">
        <div class="col-sm-4" style="padding-left: 2%">
            <?php require_once('menu.php');?>
        </div>
        <div id="contenuto" class="col-sm-5">
            <form class="form-group" id="f" method="post" onsubmit="return verifica(name.value, pwd.value);"
                  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> ">
                    <label>Username:
                        <div data-tip="tooltip"
                             title="Inserire uno Username che sia un'email valida ne formato username@dominio!">
                            <input type="email" class="form-control" id="name" name="name">
                        </div>
                    </label><br>
                    <label>Password:
                        <div data-tip="tooltip"
                             title="Inserire una Password che contenga ALMENO un carattere minuscolo ed uno maiuscolo o un numero">
                        <input type="password" class="form-control" id="pwd" name="pwd">
                        </div>
                    </label>
                    <p><input type="submit" class="btn btn-primary" name="submit" id="submit" value="INVIA" >
                    <input type="reset" class="btn btn-primary" name="cancel" id="cancel" value="CANCELLA" ><br></p>
            </form>
            <?php require_once('messaggi.php'); ?>
        </div>
    </div>
<?php require_once('footer.php');?>