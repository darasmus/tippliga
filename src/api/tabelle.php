<?php

    require('session.inc.php');
    require('api.lib.php');

    $table["table"] = getRealTable($db);

    $json = json_encode($table);
    print_r($json);

    //close the db connection
    mysqli_close($db);