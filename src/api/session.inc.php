<?php
    session_start();
    if(!isset($_SESSION['tippliga'])) {
        header("HTTP/1.0 401 Not Authorized");
        //$msg = array('msg' => 'session ist abgelaufen');
        //print_r( json_encode($msg) );
        exit;
    }