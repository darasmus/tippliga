<?php
    require('api.lib.php');

    //$usr = urldecode($_GET['usr']);
    //$pwd = urldecode($_GET['pwd']);

    $dta = json_decode($_POST['dta']);

    $usr = mysqli_real_escape_string($db, $dta->usr);
    $pwd = mysqli_real_escape_string($db, $dta->pwd);

    $query = "SELECT a.vorname, a.nachname, a.id, b.liga, a.mannschaft, b.name as manschaftsname
              FROM spieler as a,mannschaften as b
              WHERE a.mannschaft=b.id
              AND a.username='".$usr."'
              AND a.passwort='".$pwd."'";

    $sth = mysqli_query($db, $query);
    $rows = array();
    while($r = mysqli_fetch_assoc($sth)) {
        $rows[] = $r;
    }

    if(count($rows) === 1) {

        $user['user'] = $rows[0];
        $user['errors'] = false;
        $user['msg'] = 'benutzer ist eingeloggt';

        session_start();

        $_SESSION["tippliga"]="tippliga";
        $_SESSION["user"] = $user['user'];

    } else {
        $user['errors'] = array(
            'user' => true,
            'pwd' => true
        );
    }

    //send msg
    $spieler = $_SESSION["user"]["id"];

    $spieltag = getNextSpieltag($db);

    $gegner = getGegner($spieler, $spieltag, $db);

    $user = array(
        "user" => $_SESSION["user"],
        "gegner" => $gegner,
        "errors" => $user['errors'],
        "msg" => $user['msg']
    );


    $json = json_encode($user);
    print_r($json);

    //close the db connection
    mysqli_close($db);

