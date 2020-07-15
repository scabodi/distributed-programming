<div id="info_utente">
    <table class="table table-hover">
        <?php
        //require_once('session_handler.php');
        if (is_logged()) { // se loggato
            printf("<tr><td id='username'>Username</td><td>%s</td></tr>",$_SESSION['user']);
            //printf("<tr style='color: yellow'><td>Totale prenotati da %s</td><td id='tot_gialli'>%s</td></tr>",$_SESSION['user'], count($_SESSION['posti']));
        } else {
            printf("<tr><td id='username'>Username</td><td><span class='grassetto'>anonimo</span></td></tr>");
        }
        ?>
        <tr>
            <td>Totale posti</td>
            <td id="tot_posti"><?php echo $GLOBALS['rows']*$GLOBALS['cols'];?></td>
        </tr>
        <tr style="color: red">
            <td>Tolale posti acquistati</td>
            <td id="tot_acquistati"><?php echo $GLOBALS['occupati'];?></td>
        </tr>
        <tr style="color: orange;">
            <td>Totale posti prenotati</td>
            <td id="tot_prenotati"><?php echo $GLOBALS['prenotati'];?></td>
        </tr>
        <tr style="color: green;">
            <td>Totale posti liberi</td>
            <td id="tot_liberi"><?php echo $GLOBALS['liberi'];?></td>
        </tr>
    </table>
</div>