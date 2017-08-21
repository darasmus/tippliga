<?php
    require('session.inc.php');
    require('api.lib.php');

    $spieler = $_SESSION["user"]["id"];

    $sess_user = $_SESSION["user"];

    $spieltag = getNextSpieltag($db);

    if(!$spieltag) {
        $spieltag = 1;
    }

    $gegner = getGegner($spieler, $spieltag, $db);


    $user = array(
        "user" => $sess_user,
        "gegner" => $gegner
    );

    $json = json_encode($user);
    print_r($json);