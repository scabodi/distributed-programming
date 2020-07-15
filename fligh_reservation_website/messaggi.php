<div id="messaggi">
    <?php
    if(is_logged()){
        $msg = "Totale posti prenotati = ".count($_SESSION['posti']);
        echo "<div class=\"alert alert-warning\" id=\"tot_gialli\">".$msg."</div>";
        echo "<div id=\"message\" ></div>";
    }
    elseif(isset($GLOBALS['successo']) && $GLOBALS['successo']!=""){
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
            document.write("<div class=\"alert alert-danger\" id=\"danger\">"+
                document.cookie.split("=")[1].split(";")[0]+"</div>");
            delete_cookie('errore');
        }
    </script>

</div>