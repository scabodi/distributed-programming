<?php
require_once 'config.php';
require_once('session_handler.php');
start_session();

require_once('functions.php');
require_once('header.php');
?>
<div id="contenuto" >
    <div class="row">
        <div class="col-sm-3" style="padding-left: 2%">
            <?php require_once('menu.php'); ?>
        </div>
        <?php if($GLOBALS['cols'] > 7){ ?>
            <div class="col-sm-5">
        <?php } else {?>
            <div class="col-sm-1"></div>
            <div class="col-sm-4">
        <?php }?>
            <div style="overflow: auto">
            <?php
                creaTablella();
                aggiornaTabella();
            ?>
            </div>
            <form class="form-group" id="form_index" method="post"
                  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> ">
                <?php if(is_logged()){?>
                <input type="button" id="acquista" class="btn btn-primary" name="input1" value="Acquista"
                       onclick="makeRequest(input1.value); ">
                <input type="button" id="aggiorna" class="btn btn-primary" name="input2" value="Aggiorna"
                       onclick="document.cookie = 'acquista=Mappa dei posti aggiornata con successo';
                       window.location.reload();">
                <?php } ?>

            </form>
        </div>
        <div class="col-sm-3">
            <?php
                require_once('info_utente.php');
                require_once('messaggi.php');
            ?>
        </div>
    </div>
</div>
<?php require_once('footer.php');?>
