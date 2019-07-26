<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

    require('session.inc.php');
    require('api.lib.php');

    $liga = $_GET['liga'];
    $spieltag = $_GET['spieltag'];
    //$mylpace = $_GET['myplace'];

    //auswertung
    $summery = getUserSummary($_SESSION["user"]["id"], $db);
    //$return['summery'] = $summery;
    $return['summery'] = $summery;

    $lastPlayday = array_pop($summery);

    //all
    $return['standing'] = getSummary($_SESSION["user"]["id"], $db);

    //tabelle
    $return['tabelle'] = getUserTableSmall($spieltag, $liga, $lastPlayday['platz'], $db);

    //ergebnisse
    $return['ergebnisse'] = getResult($spieltag, $liga, $db);

    //gameday-winner
    $return['gewinner1'] = getBestSpieler($spieltag, 1, $db);
    $return['gewinner2'] = getBestSpieler($spieltag, 2, $db);
    $return['gewinner3'] = getBestSpieler($spieltag, 3, $db);

    $json = json_encode($return);
    print_r($json);

    //close the db connection
    mysqli_close($db);