<?php

    require('session.inc.php');
    require('api.lib.php');

    $sth = mysqli_query($db, "SELECT id,vorname,nachname FROM spieler");

    $rows = array();
    while($r = mysqli_fetch_assoc($sth)) {
        $rows[] = utf8ize($r);
    }

    $json = json_encode($rows);
    print_r($json);

    //close the db connection
    mysqli_close($db);

