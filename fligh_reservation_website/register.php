<?php
require_once('config.php');
require_once('session_handler.php');
require_once('functions.php');
start_session();
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $user = trim($_POST['name']);
    $pass = trim($_POST['pwd']);

    //controlli di scrittura di user e pwd
    $re1 = "/[a-z]+/";
    $re2 = "/[A-Z0-9]+/";

    if (!filter_var($user, FILTER_VALIDATE_EMAIL)) {
        $GLOBALS['errore'] = "Username non corretto! Deve essere un indirizzo email valido nel formato 'username@dominio'!";
    } elseif (!preg_match($re1, $pass) || !preg_match($re2, $pass)) {
        $GLOBALS['errore'] = " Password non corretta! Deve contenere almeno un carattere minuscolo, un carattere maiuscolo o uno numerico!";
    } else {
        $con = mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
        if (mysqli_connect_errno()) {
            $GLOBALS['errore'] = "Errore - collegamento al DB IMPOSSIBILE: ".mysqli_connect_error();
        } else {

            //controllo sullo user già presente nel db
            $query = "SELECT user,pwd FROM utenti WHERE user='".$user."'";
            mysqli_prepare($con, $query);
            $result = mysqli_query($con, $query);

            if (!$result) {
                $GLOBALS['errore'] = "Errore - Query fallita: ".mysqli_error($con);
            } else {
                $nrow = mysqli_num_rows($result);
                mysqli_free_result($result);
            }

            if($nrow > 0){
                $GLOBALS['errore'] = "Errore: username già presente nel database.<br>Per favore scegliere un nuovo username.";
            }else {
                $hash = md5($pass);

                $query = "INSERT INTO utenti ( user, pwd) VALUES (?,?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ss", $user, $hash);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);
                mysqli_close($con);

                if(!isset($_SESSION['user']))
                    $GLOBALS['successo'] = "Registrazione avvenuta con successo per l'utente '" . $user .
                        "'<br>Clicca <a href='login.php'>qui</a> per effettuare il login";
                else
                    $GLOBALS['successo'] = "Registrazione avvenuta con successo per l'utente '" . $user .
                            "'<br>Utente '".$_SESSION['user']."' attualmente loggato.
                            <br>Clicca <a href='logout.php'>qui</a> per effettuare il logout.";
                //header_remove();
                //header("location:register.php");
            }
        }
    }
}
require_once('header.php');
?>
    <div class="row">
        <div class="col-sm-4" style="padding-left: 2%">
            <?php include('menu.php');?>
        </div>
        <div id="contenuto" class="col-sm-5">
            <script src="css/jquery.min.js"></script>
            <script>
                $(document).ready(function(){
                    $('[data-toggle="tooltip"]').tooltip();
                });
            </script>
            <div id="h3"><h3> Registrati qui </h3></div>

            <form class="form-group" id="myform" method="post"  onsubmit="return verifica(name.value, pwd.value); "
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label>Username*:
                    <div data-tip="tooltip"
                         title="Inserire uno Username che sia un'email valida ne formato username@dominio!">
                        <input type="email" class="form-control" name="name" id="name" required>
                    </div>
                </label><br>
                <label>Password*:
                    <div data-tip="tooltip"
                    title="Inserire una Password che contenga ALMENO un carattere minuscolo ed uno maiuscolo o un numero">
                        <input type="password" class="form-control" name="pwd" id="pwd" required>
                    </div>
                </label>
                <p><small>(*): campi obbligatori</small></p>
                <p><input class="btn btn-primary" type="submit" value="Registrati" >
                    <input class="btn btn-primary" type="reset" value="Cancella"></p>
            </form>
            <?php require_once('messaggi.php'); ?>
        </div>
    </div>
<?php require_once('footer.php'); ?>