<?php

    require('session.inc.php');
    require('api.lib.php');

    $spieler = $_GET['spieler'];
    $spieltag = $_GET['spieltag'];
    $liga = $_GET['liga'];

    $tippdatum = getSpieldatum($spieltag, $db);

    $query = "SELECT b.name,a.vorname,a.nachname
              FROM spieler as a,mannschaften as b
              WHERE a.id=$spieler AND a.mannschaft=b.id";

    $q1 = mysqli_query($db, $query);
    while($r = mysqli_fetch_assoc($q1)) {
        $result = utf8ize($r);
    }

    $query3 = "SELECT
                  A.*,
                  M1.name AS mannschaftA,
                  M2.name AS mannschaftB
                FROM spiele AS A
                INNER JOIN mannschaften AS M1
                  ON A.mannschaft1 = M1.id
                INNER JOIN mannschaften AS M2
                  ON A.mannschaft2 = M2.id
                WHERE A.spieltag=$spieltag
                AND M1.liga=1
                ORDER BY A.spieldatum";

    $q3 = mysqli_query($db, $query3);

    $ges_punkte = 0;
    $i = 0;
    $dta = array();
    while($r = mysqli_fetch_assoc($q3)) {

        if($r['tore1'] == -1) {
            $r['tore1'] = '';
        }

        if($r['tore2'] == -1) {
            $r['tore2'] = '';
        }

        $data = getSpielerSpiel($spieler,$r['id'],$spieltag,$db);

        if($data['tipp1'] == "n/t") {
            $r['tipp1'] = "+";
            $r['tipp2'] = "+";
            $r['tipp'] = "+";
        } else {
            $r['tipp1'] = $data['tipp1'];
            $r['tipp2'] = $data['tipp2'];
            $r['tipp'] = $data['tipp1']." : ".$data['tipp2'];
        }

        $r['punkte'] = $data['punkte'];

        $dta[] = $r;

        $ges_punkte = $ges_punkte + $data['punkte'];

        $i++;
    }


    $datum = date("d.m.Y H:i", $tippdatum);

    $return = array(
        "dta" => $dta,
        "gesamtpunkte" => $ges_punkte,
        "mannschaft" => $result['name'],
        "vorname" => $result['vorname'],
        "nachname" => $result['nachname'],
        "tippdatum" => $datum
    );

    $json = json_encode($return);
    print_r($json);

    //close the db connection
    mysqli_close($db);
