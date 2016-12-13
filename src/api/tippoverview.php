<?php

    require('session.inc.php');
    require('api.lib.php');

    $spieler = $_GET['spieler'];
    //not used at the moment
    $gegner = $_GET['gegner'];
    $spieltag = $_GET['spieltag'];

    $nextspieltag = getNextSpieltag($db);
    $lastspieltag = getLastSpieltag($db);

    $tippdatum = getSpieldatum($nextspieltag, $db);

    //gameday and date
    if($spieltag == "")
    {
        if(time() > $tippdatum)
        {
            $spieltag = $lastspieltag + 1;
        } else {
            $spieltag = $lastspieltag;
        }

    } elseif (time() > $tippdatum) {
        $lastspieltag = $nextspieltag;
    }

    if ($spieltag > $lastspieltag) {
        return false;
    }


    //get spieler
    if($gegner  == "")
    {
        $gegner = getGegner($spieler, $spieltag, $db);
    }

    //get tipps
    $qGamesWithTipps = "
                        SELECT
                        A.*,
                        M1.name AS mannschaftA,
                        M2.name AS mannschaftB,
                        T1.tipp1 AS tipp1A, T1.tipp2 AS tipp1B,
                        T2.tipp1 AS tipp2A, T2.tipp2 AS tipp2B
                        FROM spiele AS A
                        INNER JOIN mannschaften AS M1
                          ON A.mannschaft1 = M1.id AND M1.liga = 1
                        INNER JOIN mannschaften AS M2
                          ON A.mannschaft2 = M2.id AND M2.liga = 1
                        LEFT JOIN tipps AS T1
                          ON A.id = T1.spiel AND T1.spieler = $spieler
                        LEFT JOIN tipps AS T2
                          ON A.id = T2.spiel AND T2.spieler = ". $gegner['id'] ."
                        WHERE A.spieltag=" . intval($spieltag) . "
                        ORDER BY A.spieldatum, A.id
    ";

    $qGames = mysqli_query($db, $qGamesWithTipps);

    $gesPunkteA = 0;
    $gesPunkteB = 0;

    $dta = array();
    while($r = mysqli_fetch_assoc($qGames)) {

        if($r['tore1'] == -1) {
            $r['tore1'] = '';
        }

        if($r['tore2'] == -1) {
            $r['tore2'] = '';
        }

        $data = $r;

        $punkteA = calPoints($data['tore1'],$data['tore2'],$data['tipp1A'],$data['tipp1B']);
        $punkteB = calPoints($data['tore1'],$data['tore2'],$data['tipp2A'],$data['tipp2B']);

        $data['punkteA'] = $punkteA;
        $data['punkteB'] = $punkteB;

        $gesPunkteA += $punkteA;
        $gesPunkteB += $punkteB;

        $dta[] = $data;
    }

    $return['overview'] = $dta;
    $return['gesPunkteA'] = $gesPunkteA;
    $return['gesPunkteB'] = $gesPunkteB;
    $return['gegnerVorname'] =  $gegner["vorname"];
    $return['gegnerNachname'] =  $gegner["nachname"];

    $json = json_encode($return);
    print_r($json);

    //close the db connection
    mysqli_close($db);
