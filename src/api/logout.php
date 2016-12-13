<?php
    require('session.inc.php');
    session_destroy();

    $data = array("msg" => "Du bist ausgeloggt");
    $json = json_encode($data);

    print_r($json);
