<?php

    require('session.inc.php');
    require('api.lib.php');

    $spiel = $_GET['spiel'];
    $tipps['tipps'] = getAllTippsFromGame($spiel, $db);
    $tipps['spiel'] = getSpielDetail($spiel, $db);
    $tipps['stats'] = getGameStats($spiel, $db);

    if(time() >= $tipps['spiel']['spieldatum']) {
        $json = json_encode($tipps);
        print_r($json);
    }

    //close the db connection
    mysqli_close($db);