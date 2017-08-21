<?php

    require('session.inc.php');
    require('api.lib.php');

    $spiel = $_GET['spiel'];
    $tipps['tipps'] = getAllTippsFromGame($spiel, $db);

    $json = json_encode($tipps);
    print_r($json);

    //close the db connection
    mysqli_close($db);