<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Sara Cabodi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="posti">
    <meta name="keywords" content="voli">
    <meta name="keywords" content="booking">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/mycss.css">
    <link rel="shortcut icon" href="airplane.ico" type="image/vnd.microsoft.icon">
    <script src="js/functions.js"></script>
    <script src="css/jquery.min.js"></script>
    <?php
    $url = $_SERVER['PHP_SELF'];
    $url = substr($url,strripos($url,"/")+1, -4);
    echo "<title>Seats booking</title>";
    ?>
</head>
<body  onload="check_cookies()" style="overflow-x: hidden">
<?php if(getNameOfPage() != "nocookie"){ ?>
    <div id="header" class="jumbotron text-center" style="padding-top: 2%; padding-bottom: 2%">
        <h1>Prenotazione posti aereo - <?php echo getNameOfPage();?></h1>
    </div>
<?php } ?>
<noscript>
    <div class="container jumbotron alert-danger text-center">
        <h3>JavaScript è necessario per far funzionare correttamente il sito.
            Si prega di abilitarlo per poter godere delle complete funzionalità del sito.</h3>
    </div>
</noscript>
