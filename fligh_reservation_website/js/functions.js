function check_cookies() {
    if (!navigator.cookieEnabled) {
        if (window.location.toString().indexOf("nocookie.php") === -1) {
            window.location = "./nocookie.php";
        }

    }
    else {
        // Se sono in nocookie.php e i cookies vengono abilitati, Tornare a index.php
        if (window.location.toString().indexOf("nocookie.php") !== -1) {
            window.location = "./index.php";
        }
    }
}


function set_color(id, colour){
    document.getElementById(id).style.backgroundColor = colour;
}

function get_color(id){
    return document.getElementById(id).style.backgroundColor;
}

function delete_cookie( cname ) {
    document.cookie = cname + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function update_statistics(color, color_before) {

    var previous_yellow = $('#tot_gialli').text();
    var previous_green = $('#tot_liberi').text();
    var previous_orange = $('#tot_prenotati').text();
    var num_yellow = parseInt(previous_yellow.trim().split('=')[1]);
    var num_green = parseInt(previous_green);
    var num_orange = parseInt(previous_orange);

    if(color === "yellow" && color_before === "green") {
        num_yellow++;
        num_green--;
    }else if(color === "yellow" && color_before === "orange"){
        num_yellow++;
        num_orange--;
    }else if(color === "green") {
        num_yellow--;
        num_green++;
    }

    var current_yellow = "Totale posti prenotati = "+num_yellow;
    $('#tot_gialli').text(current_yellow);
    $('#tot_liberi').text(num_green);
    $('#tot_prenotati').text(num_orange);
}

function AJAXRequestObject(url, callback)
{
    function ajaxRequest() {
        try { // Non IE Browser?
            var request = new XMLHttpRequest()
        } catch(e1){ // No
            try { // IE 6+?
                request = new ActiveXObject("Msxml2.XMLHTTP")
            } catch(e2){ // No
                try { // IE 5?
                    request = new ActiveXObject("Microsoft.XMLHTTP")
                } catch(e3){ // No AJAX Support
                    request = false
                }
            }
        }
        return request
    }
    function processRequest () {
        if (req.readyState === 4) {
            if (req.status === 200|| req.status === 0) {
                if (callback) callback(req.responseText);
            }
        }
    }
    var req = ajaxRequest();
    req.onreadystatechange = processRequest;
    this.doPost =
        function(body) {
            req.open("POST", url, true);
            req.setRequestHeader("Content-Type",
                "application/x-www-form-urlencoded");
            req.send(body);
        }
}

function makeRequest(id) {
    var ai;
    if(id === "Acquista"){
        //alert("cliccato acquista");
        ai = new AJAXRequestObject("acquista.php",
            function(text) {
                //alert(text);
                if(text != "") {
                    //alert(text);
                    var str = text.split("-");
                    var ok = str[0];
                    var msg = str[1];
                    if (ok === "si") {
                        document.cookie = "acquista=" + msg;
                    } else if (ok === "no") {
                        document.cookie = "errore=" + msg;
                    }
                }else{
                    alert('Attenzione: occorre essere loggati per poter prenotare i posti.');
                }
                document.location.reload();
            }
        );
        ai.doPost();
    }else {
        //var fila = id[2];
        var fila = parseInt(id[1]+id[2]);
        //alert("fila = "+fila);
        var posto = id[0];
        //alert("cliccato "+fila+" "+posto);
        ai = new AJAXRequestObject("richiesta.php",
            function (text) {
                //alert("Response OK: " + text);
                if (text !== "") {
                    var str = text.split("-");
                    var color = str[0];
                    var msg = str[1];
                    var color_before = get_color(id);
                    //alert(color_before);
                    set_color(id, color);

                    if (color === "red" || color === "orange") {
                        //alert(msg);
                        if($('#danger').length === 0) {
                            mess = "<div class=\"alert alert-danger\">" + msg + "</div>";
                            $('#message').html(mess);
                        }else{
                            $('#danger').text(msg);
                        }
                    }else {
                        if(msg!=="undefined") {
                            if ($('#success').length === 0) {
                                mess = "<div class=\"alert alert-success\">" + msg + "</div>";
                                $('#message').html(mess);
                            } else {
                                $('#success').text(msg);
                            }
                            if ($('#danger').length !== 0) {
                                $('#danger').remove();
                            }
                            update_statistics(color, color_before);
                        }
                    }
                }
                else {
                    alert('Attenzione: occorre essere loggati per poter prenotare i posti.');
                    document.location.reload();
                }
            }
        );
        var color = get_color(id);
        ai.doPost("color=" + color + "&fila=" + fila + "&posto=" + posto);
    }
}

function verifica(user, pass) {

    var ruser = /^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/;
    var re1 = /[a-z]+/;
    var re2 = /[A-Z0-9]+/;
    var re3 = /\s+/;

    //controlli sullo USERNAME
    if (!user) {
        window.alert("Inserire uno username!");
        return false;
    } else if (!ruser.test(user)) {
        window.alert("Lo user deve essere un indirizzo email valido nel formato 'username@dominio'\n" +
            "Il dominio deve essere formato da una parola seguita da un punto ed almeno 2 lettere\n" +
            "Es: prova@esempio.es");
        return false;
    }
    //controlli sulle PASSWORD
    if (!pass) {
        window.alert("Inserire la password!");
        return false;
    } else if (!re1.test(pass)) {
        window.alert("La password deve contenere almeno un carattere minuscolo");
        return false;
    }
    else if (!re2.test(pass)) {
        window.alert("La password deve contenere almeno un carattere maiuscolo o un numero");
        return false;
    }
    if(re3.test(pass)){
        window.alert("La password NON può contenere spazi");
        return false;
    }
}
