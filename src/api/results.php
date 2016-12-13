<?php

    //error_reporting(E_ALL);
    //ini_set("display_errors", 1);

    require('session.inc.php');
    require('api.lib.php');

    $liga = $_GET['liga'];
    $spieltag = $_GET['spieltag'];

    //tabelle
    $return['tabelle'] = getUserTable($spieltag, $liga, $db);

    //ergebnisse
    $return['ergebnisse'] = getResult($spieltag, $liga, $db);

    $json = json_encode($return);
    print_r($json);

    //close the db connection
    mysqli_close($db);
