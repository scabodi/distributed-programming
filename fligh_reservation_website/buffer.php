function prova(){
xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function () {
if (this.readyState == 4 && this.status == 200) {
if(this.responseText != null)
document.getElementById(id).innerHTML = this.responseText;
else
alert('non ricevuto nulla');
}
};
xhttp.open("GET", "richiesta.php?fila=" + fila + "&posto=" + posto, true);
xhttp.send();
}

function ajaxRequest() {
altert('entrato in ajax request');
try { // Non IE Browser?
var request = new XMLHttpRequest();
} catch(e1){ // No
try { // IE 6+?
request = new ActiveXObject("Msxml2.XMLHTTP");
} catch(e2){ // No
try { // IE 5?
request = new ActiveXObject("Microsoft.XMLHTTP");
} catch(e3){ // No AJAX Support
request = false;
}
}
}
return request;
}

function processRequest () {
altert('entrato in process request');
if (req.readyState === 4) {
if (req.status === 200|| req.status===0) {
if(this.responseText != null)
alert(this.responseText);
else
alert('non ricevuto nulla');
//document.getElementById(id).innerHTML = this.responseText;
}
}
}

var req = ajaxRequest();
req.onreadystatechange = processRequest();
req.open("POST", "richiesta.php", true);
req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
req.send("fila="+fila+"&posto="+posto);



<script src="css/jquery.min.js"></script>
<script>

    $("<?php echo $id;?>").css("background-color", "<?php echo $color;?>");

</script>

<nav class="navbar bg-light">
    <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
        <li class="nav-item"><a class="nav-link" href='register.php'>REGISTRAZIONE</a></li>
        <?php if(empty($_SESSION['user'])): ?>
            <li class="nav-item"><a class="nav-link" href='login.php'>LOGIN</a></li>
            <li class="nav-item"><a class="nav-link disabled" href='#' onclick='<?php logout();?>' class='disabled' >LOGOUT</a></li>
        <?php endif; ?>
        <?php if(!empty($_SESSION['user'])): ?>
            <li class="nav-item"><a class="nav-link disabled" href='login.php' class='disabled' >LOGIN</a></li>
            <li class="nav-item"><a class="nav-link" href='#' onclick='<?php logout();?>'>LOGOUT</a></li>";
        <?php endif; ?>
    </ul>
</nav>


<script src="css/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $("<?php echo $id;?>").css("background-color", "<?php echo $color;?>");
    });
</script>

<?php aggiornaTabella();?>


<input id="info_pwd" class="btn btn-outline-info" type="button" value="?" onclick="alert('Info PASSWORD:\n' +
                                ' - deve contenere almeno un carattere minuscolo \n' +
                                ' - deve contenere un carattere maiuscolo o un numero')"></p>


data-toggle="tooltip" title="Inserire una Password che contenga ALMENO
un carattere minuscolo ed uno maiuscolo o un numero"


//TODO debug purpose
$query = "SELECT stato FROM posti WHERE fila=? AND posto=?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "is", $fila, $posto);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
mysqli_stmt_bind_result($stmt, $stato);
mysqli_stmt_fetch($stmt);

echo $stato;
mysqli_stmt_close($stmt);


function acquistaPosti(){

if(is_logged()){
//controllare che i posti nell'array siano ancora dell'utente loggato
//in caso affermativo cambiarne il colore da giallo a rosso
$con = mysqli_connect(DB_HOST, DB_USER, DB_PWD, DB_NAME);
if (mysqli_connect_errno()) {
printf("<p class='err'>errore - collegamento al DB IMPOSSIBILE: %s</p>\n", mysqli_connect_error());
} else {
$stato = 'p';
$query = "SELECT fila, posto FROM posti WHERE stato='".$stato."' AND user='".$_SESSION['user']."'";
mysqli_prepare($con, $query);
$result = mysqli_query($con, $query);

if (!$result) {
printf("<p class='err'>Errore - query fallita: %s</p>\n", mysqli_error($con));
} else {
$nrow = mysqli_num_rows($result);
$acquistati = array();

for ($i=0; $i<$nrow; $i++) {
//echo "alert('entrata ".count($_SESSION['posti'])."');";
$row = mysqli_fetch_assoc($result);
$fila = sprintf("%02d", $row["fila"]);
$posto = htmlentities($row["posto"]);
$id = sprintf("#%s", $posto . "" . $fila);

if(in_array($id, $_SESSION['posti'])){
//echo "alert('entrata');";
//se il posto è ancora prenotato dall'utente loggato -> acquisto
$key = array_search($id, $_SESSION['posti']);
unset($_SESSION['posti'][$key]);
$coppia = $fila.",".$posto;
array_push( $acquistati, $coppia);

}
}
mysqli_free_result($result);

foreach ($acquistati as $coppia ) {
$fields = explode(",", $coppia);
$fila = intval($fields[0]);
$posto = $fields[1];
$stato = "o";

//echo $fila." ".$posto." ".$stato;
echo "alert('non devo enytrare');";

$query = "UPDATE posti SET stato=? WHERE fila=? AND posto=?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "sis", $stato, $fila, $posto);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
}
}
mysqli_close($con);
}
}
}

<p id="errore"><?php echo $errore;?></p>

<footer class="py-2 bg-dark" >
    <div class="container">
        <p class="m-0 text-right text-white">
            <small>
                <?php
                $url = $_SERVER['PHP_SELF'];
                $url = substr($url,strripos($url,"/")+1, strlen($url));
                echo $url." &copy ";
                $tags = get_meta_tags("header.php");
                echo $tags['author'];
                ?>
            </small>
        </p>
    </div>
</footer>
</body>
</html>


<div class="alert alert-success" id="success">successo</div>


<?php
if(isset($GLOBALS['successo']) && $GLOBALS['successo']!=""){
    echo "<div class=\"alert alert-success\" id=\"success\">".$GLOBALS['successo']."</div>";
}
elseif (isset($GLOBALS['errore']) && $GLOBALS['errore']!=""){
    echo "<div class=\"alert alert-danger\" id=\"danger\">".$GLOBALS['errore']."</div>";
}
?>

if(text === "successo") {
document.getElementById(text).style.display = "block";
}

<div class="alert alert-success" id="successo" style="display: none"><?php echo $GLOBALS['successo'];?></div>
<div class="alert alert-danger" id="errore" style="display: none"><?php echo $GLOBALS['errore'];?></div>

<?php if(isset ($GLOBALS['successo'])) echo $GLOBALS['successo'];?>


<script type="text/javascript">
    if(document.cookie.split("=")[0] === "info"){
        document.write("<div class=\"alert alert-success\" id=\"success\">"+document.cookie+"</div>");
    }
</script>

elseif (isset($_COOKIE['successo']) && $_COOKIE['successo']!=""){
echo "<div class=\"alert alert-success\" id=\"success\">".$_COOKIE['successo']."</div>";
}

function check_https()
{
//TODO verificare funzionamento
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
// La richiesta e' stata fatta su HTTPS
} else {
// Redirect su HTTPS
// eventuale distruzione sessione e cookie relativo
$redirect = 'https://' . $_SERVER['HTTP_HOST'] .
$_SERVER['REQUEST_URI'];
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $redirect);
exit();
}
}

<div id="messaggi">
    <?php
    if(is_logged()){
        $msg = "Totale posti prenotati = ".count($_SESSION['posti']);
        echo "<div class=\"alert alert-warning\" id=\"tot_gialli\">".$msg."</div>";
        echo "<div id=\"message\" ></div>";
    }
    if(isset($GLOBALS['successo']) && $GLOBALS['successo']!=""){
        echo "<div class=\"alert alert-success\" id=\"success\">".$GLOBALS['successo']."</div>";
    }
    elseif (isset($GLOBALS['errore']) && $GLOBALS['errore']!=""){
        echo "<div class=\"alert alert-danger\" id=\"danger\">".$GLOBALS['errore']."</div>";
    }
    ?>
    <script type="text/javascript">
        if(document.cookie.split("=")[0] === "acquista"){
            document.write("<div class=\"alert alert-success\" id=\"success\">"+
                document.cookie.split("=")[1].split(";")[0]+"</div>");
            delete_cookie('acquista');
        }
        else if(document.cookie.split("=")[0] === "aggiorna"){
            document.write("<div class=\"alert alert-success\" id=\"success\">"+
                document.cookie.split("=")[1].split(";")[0]+"</div>");
            delete_cookie('aggiorna');
        }
        if(document.cookie.split("=")[0] === "errore"){
            document.write("<div class=\"alert alert-danger\" id=\"error\">"+
                document.cookie.split("=")[1].split(";")[0]+"</div>");
            delete_cookie('errore');
        }
    </script>

</div>
