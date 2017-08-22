<?php

    //error_reporting(E_ALL);
    ini_set("display_errors", 0);

    //define
    define ("_DEV", ($_SERVER['REMOTE_ADDR']=='127.0.0.1'));

    //set date
    ini_set('date.timezone', 'Europe/Berlin');
    date_default_timezone_set('Europe/Berlin');

    //Datenbank-Stuff
    if(_DEV)
    {
        $db_host = "localhost";
        $db_user = "root";
        $db_passwd = "admin";
        $db_name = "dadeva_db1";
    }
    else
    {
        $db_host = "sql81.your-server.de";
        $db_user = "dadeva_1";
        $db_passwd = "SE80zNk8c56ymEA0";
        $db_name = "dadeva_db1";
    }

    $db = new mysqli($db_host, $db_user, $db_passwd, $db_name);
    mysqli_set_charset($db, "utf8");

    if ($db->connect_errno) {
        echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error . " / " . $db_host;
    }

    //JSON stuff
    function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }

    //Berechnung
    function getSpielerSpiel($spieler,$spiel,$spieltag,$db)
    {
        $query = "SELECT * FROM spiele WHERE id=$spiel ORDER BY spieldatum";
        $q1 = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($q1)) {
            $row= $r;
        }

        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];

        $tippdatum = getSpieldatum($spieltag, $db);

        if( ($tore1 == "") || ($tore1 < 0) )
        {
            $tore1 = "-";
        }
        if( ($tore2 == "") || ($tore2 < 0) )
        {
            $tore2 = "-";
        }

        $query2 = "SELECT tipp1, tipp2 FROM tipps WHERE spieler=$spieler AND spiel=$spiel";
        $q2 = mysqli_query($db, $query2);
        while($r = mysqli_fetch_assoc($q2)) {
            $result_tipp= $r;
        }

        $punkte = 0;


        if(count($result_tipp)>0)
        {
            $tipp1 = $result_tipp['tipp1'];
            $tipp2 = $result_tipp['tipp2'];
            $tipp = "$tipp1 : $tipp2";
            //punkte berechnen

            if($tore1 != "-" && $tore2 != "-") {
                $punkte = calPoints($tore1,$tore2,$tipp1,$tipp2);
            }
        }
        elseif(time() > $tippdatum)
        {
            $tipp1 = "n/t";
            $tipp2 = "n/t";
        }
        else
        {
            $tipp1 = "-";
            $tipp2 = "-";
        }

        $return['tipp1'] = $tipp1;
        $return['tipp2'] = $tipp2;
        $return['punkte'] = $punkte;

        $query3 = "SELECT vorname,nachname,id FROM spieler WHERE id=$spieler";
        $q3 = mysqli_query($db, $query3);
        while($r = mysqli_fetch_assoc($q3)) {
            $result_user= $r;
        }

        $u_vorname = $result_user['vorname'];
        $u_nachname = $result_user['nachname'];
        $u_1 = $u_vorname." ".$u_nachname;

        $return['name'] = $u_1;

        return $return;
    }

    function getGegner($spieler, $spieltag, $db)
    {
        $qSearchGegner = "SELECT A.*,
                          U1.id as gegnerID1, U1.vorname as gegenerVorname1, U1.nachname as gegenerNachname1, U1.mannschaft as gegnerTeam1,
                          U2.id as gegnerID2, U2.vorname as gegenerVorname2, U2.nachname as gegenerNachname2, U2.mannschaft as gegnerTeam2,
                          M1.name as gegnerMannschaft1, M2.name as gegnerMannschaft2
                          FROM spiele as A

                          INNER JOIN spieler AS U1
                              ON A.mannschaft1 = U1.mannschaft
                          INNER JOIN spieler AS U2
                              ON A.mannschaft2 = U2.mannschaft

                          INNER JOIN mannschaften AS M1
                              ON U1.mannschaft = M1.id
                          INNER JOIN mannschaften AS M2
                              ON U2.mannschaft = M2.id

                          WHERE A.spieltag=$spieltag
                          AND (U1.id=$spieler OR U2.id=$spieler)
              ";

        $qGegner = mysqli_query($db, $qSearchGegner);

        while($r = mysqli_fetch_assoc($qGegner)) {

            if($r['gegnerID1'] == $spieler)
            {
                $gegner['id'] = $r['gegnerID2'];
                $gegner['vorname'] = $r['gegenerVorname2'];
                $gegner['nachname'] = $r['gegenerNachname2'];
                $gegner['team'] = $r['gegnerTeam2'];
                $gegner['mannschaft'] = $r['gegnerMannschaft2'];
                $gegner['home'] = false;
            }
            else {
                $gegner['id'] = $r['gegnerID1'];
                $gegner['vorname'] = $r['gegenerVorname1'];
                $gegner['nachname'] = $r['gegenerNachname1'];
                $gegner['team'] = $r['gegnerTeam1'];
                $gegner['mannschaft'] = $r['gegnerMannschaft1'];
                $gegner['home'] = true;
            }
        }

        return $gegner;
    }

    function getSpieldatum($spieltag, $db)
    {
        $query = "SELECT min(a.spieldatum) as sd
                 FROM spiele as a, mannschaften as b
                 WHERE b.liga=1
                 AND a.spieltag= " .$spieltag. "
                 AND a.mannschaft1=b.id
                 ORDER BY a.id";

        $q1 = mysqli_query($db, $query);

        echo mysqli_error($db);

        while($r = mysqli_fetch_assoc($q1)) {
            $result = $r['sd'];
        }

        if($result>0)
        {
            return $result;
        }
    }

    function getPlace($spieler,$spieltag,$db)
    {
        $place = 0;

        if($spieltag > 0)
        {
            $query = "SELECT platz FROM auswertung WHERE spieler='".$spieler."' AND spieltag='".$spieltag."'";
            $q1 = mysqli_query($db, $query);
            while($r = mysqli_fetch_assoc($q1)) {
                $place = $r['platz'];
            }
        }
        return $place;
    }

    function getLastSpieltag($db)
    {
        $spieltag = 0;

        $query = "SELECT max(spieltag) as st FROM auswertung";
        $q1 = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($q1)) {
            $spieltag = $r['st'];
        }

        return $spieltag;
    }

    function getNextSpieltag($db)
    {
        $spieltag = 0;

        $query = "SELECT max(spieltag) as st FROM auswertung";
        $q1 = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($q1)) {
            $spieltag = $r['st'] + 1;
        }

        return $spieltag;
    }

    function getSpielDetail($spiel, $db)
    {
        $query = "SELECT A.*,
                        M1.name AS mannschaftA,
                        M2.name AS mannschaftB,
                        A.tore1,
                        A.tore2
                    FROM spiele as A

                    INNER JOIN mannschaften AS M1
                       ON A.mannschaft1 = M1.id
                    INNER JOIN mannschaften AS M2
                       ON A.mannschaft2 = M2.id
                       
                    WHERE A.id=$spiel";

        $q1 = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($q1)) {
            $spiel = $r;
        }

        return $spiel;
    }

    function calPoints($tore1,$tore2,$tipp1,$tipp2)
    {
        $div1 = $tore1 - $tore2;
        $div2 = $tipp1 - $tipp2;

        if( ($tore1 != "") && ($tore2 != "") &&
            ($tore1 != "-1") && ($tore2 != "-1") &&
            ($tipp1 !== "-1") && ($tipp2 !== "-1") &&
            ($tipp1 != "-") && ($tipp2 != "-") &&
            ($tipp1 != "")&& ($tipp2 != "") &&
            ($tipp1 != null)&& ($tipp2 != null)
        )
        {
            if( ($tore1 == $tipp1) && ($tore2 == $tipp2) )
            {
                $punkte = 5;
            }
            elseif( ($div1 == $div2) && ($div1 != 0) )
            {
                $punkte = 4;
            }
            elseif( ($div1 == $div2) && ($div1 == 0) )
            {
                $punkte = 4;
            }
            elseif( ($div1 > 0) && ($div2 > 0) )
            {
                $punkte = 3;
            }
            elseif( ($div1 < 0) && ($div2 < 0) )
            {
                $punkte = 3;
            }
            else
            {
                $punkte = 0;
            }
        }
        else
        {
            $punkte = 0;
        }

        return $punkte;
    }

    function getRealTable($db)
    {
        $query = "SELECT * FROM mannschaften WHERE liga=1 order by name";
        $qTabelle = mysqli_query($db, $query);

        while($r = mysqli_fetch_assoc($qTabelle)) {

            $ges_tor_div = 0;
            $ges_punkte = 0;
            $ges_tore = 0;
            $ges_tore_get = 0;

            $id = $r["id"];
            $name = $r["name"];

            $query2 = "SELECT * FROM spiele WHERE (mannschaft1=$id OR mannschaft2=$id)";
            $qTabelleTeam = mysqli_query($db, $query2);
            while($rt = mysqli_fetch_assoc($qTabelleTeam)) {

                $s_id = $rt['id'];
                $tore1 = $rt['tore1'];
                $tore2 = $rt['tore2'];
                $mannschaft1 = $rt['mannschaft1'];
                $mannschaft2 = $rt['mannschaft2'];

                if($tore1 == -1)
                {
                    $tore1 = "-";
                }
                if($tore2 == -1)
                {
                    $tore2 = "-";
                }
                $tor_div = $tore1 - $tore2;

                if( ($tore1 != "-") && ($tore2 != "-") )
                {
                    if( ($mannschaft1 == $id) && ($tor_div > 0) )
                    {
                        $punkte = 3;
                        $tore = $tore1;
                        $tore_get = $tore2;
                    }
                    elseif( ($mannschaft2 == $id) && ($tor_div < 0) )
                    {
                        $punkte = 3;
                        $tor_div = $tor_div * -1;
                        $tore = $tore2;
                        $tore_get = $tore1;
                    }
                    elseif($tor_div == 0)
                    {
                        $punkte = 1;
                        $tore = $tore1;
                        $tore_get = $tore2;
                    }
                    else
                    {
                        $punkte = 0;
                        $tore = $tore1;
                        $tore_get = $tore2;
                        if($tor_div > 0)
                        {
                            $tor_div = $tor_div * -1;
                            $tore = $tore2;
                            $tore_get = $tore1;
                        }
                    }
                }
                else
                {
                    $punkte = 0;
                    $tor_div = 0;
                    $tore = 0;
                    $tore_get = 0;
                }

                $ges_punkte = $ges_punkte + $punkte;
                $ges_tor_div = $ges_tor_div + $tor_div;
                $ges_tore = $ges_tore + $tore;
                $ges_tore_get = $ges_tore_get + $tore_get;
            }

            $tab_name[$id] = $name;
            $tab_punkte[$id] = $ges_punkte;
            $tab_div[$id] = $ges_tor_div;
            $tab_tore[$id] = $ges_tore;
            $tab_tore_get[$id] = $ges_tore_get;

            $sorts=($ges_punkte*10000)+($ges_tor_div*100)+$ges_tore;
            $tab_sort[$id] = $sorts;

        }

        //Tabelle sortieren...
        arsort($tab_sort,SORT_NUMERIC);

        $c = 0;
        foreach($tab_sort as $key => $value)
        {
            $platz = $c+1;
            $platz = $platz.".";

            if( ($c == 0) || ($c == 1) )
            {
                $bgcolor="#afccff";
            }
            elseif($c == 2)
            {
                $bgcolor="#bfd6ff";
            }
            elseif( ($c == 3) || ($c == 4) )
            {
                $bgcolor="#fff4b4";
            }
            elseif($c == 15)
            {
                $bgcolor="#ffd9d9";
            }
            elseif( ($c == 16) || ($c == 17) )
            {
                $bgcolor="#ffafb2";
            }
            elseif($c%2 == 0)
            {
                $bgcolor="#EEEEEE";
            }
            else
            {
                $bgcolor="#FFFFFF";
            }

            $array = array(
                "id" => $key,
                "platz" => $platz,
                "name" => $tab_name[$key],
                "diff" => $tab_div[$key],
                "toreplus" => $tab_tore[$key],
                "toreminus" => $tab_tore_get[$key],
                "punkte" => $tab_punkte[$key],
                "bgcolor" => $bgcolor
            );

            $return[] = $array;

            $c++;
        }

        return $return;
    }


    function getResult($spieltag, $liga, $db) {

        if($spieltag <= getLastSpieltag($db)) {
            $query2 = "SELECT A.*,
                        M1.name AS mannschaftA,
                        M2.name AS mannschaftB,
                        S1.id AS spielerA, S1.vorname AS spielerVornameA, S1.nachname AS spielerNachnameA,
                        S2.id AS spielerB, S2.vorname AS spielerVornameB, S2.nachname AS spielerNachnameB,
                        A1.tore_geschossen AS toreA,
                        A2.tore_geschossen AS toreB
                    FROM spiele as A

                    INNER JOIN mannschaften AS M1
                       ON A.mannschaft1 = M1.id
                    INNER JOIN mannschaften AS M2
                       ON A.mannschaft2 = M2.id

                    INNER JOIN spieler AS S1
                       ON A.mannschaft1 = S1.mannschaft
                    INNER JOIN spieler AS S2
                       ON A.mannschaft2 = S2.mannschaft

                    INNER JOIN auswertung AS A1
                       ON S1.id = A1.spieler AND A1.spieltag=$spieltag
                    INNER JOIN auswertung AS A2
                       ON S2.id = A2.spieler AND A2.spieltag=$spieltag

                    WHERE M1.liga=$liga AND A.spieltag=$spieltag";
        } else {
            $query2 = "SELECT A.*,
                        M1.name AS mannschaftA,
                        M2.name AS mannschaftB,
                        S1.id AS spielerA, S1.vorname AS spielerVornameA, S1.nachname AS spielerNachnameA,
                        S2.id AS spielerB, S2.vorname AS spielerVornameB, S2.nachname AS spielerNachnameB
                    FROM spiele as A

                    INNER JOIN mannschaften AS M1
                       ON A.mannschaft1 = M1.id
                    INNER JOIN mannschaften AS M2
                       ON A.mannschaft2 = M2.id

                    INNER JOIN spieler AS S1
                       ON A.mannschaft1 = S1.mannschaft
                    INNER JOIN spieler AS S2
                       ON A.mannschaft2 = S2.mannschaft

                    WHERE M1.liga=$liga AND A.spieltag=$spieltag";
        }

        $i = 0;
        $ergebnisse = array();

        $qr2 = mysqli_query($db, $query2);
        while($r = mysqli_fetch_assoc($qr2)) {
            $data = $r;

            $data['logo1'] = "images/vlogos/".$r['mannschaft1'].".gif";
            $data['logo2'] = "images/vlogos/".$r['mannschaft2'].".gif";

            $ergebnisse[] = $data;

            $i++;
        }

        return $ergebnisse;
    }

    function getUserTable($spieltag, $liga, $db) {
        $q_check = "SELECT * FROM auswertung";
        $q1 = mysqli_query($db, $q_check );

        if(mysqli_num_rows($q1) === 0)
        {
            $query = "SELECT
                    c.name,
                    b.vorname,
                    b.nachname,
                    b.id as spielerid,
                    c.id as mannid
                  FROM
                    spieler AS b,
                    mannschaften AS c
                  WHERE
                    c.id=b.mannschaft
                  AND
                    c.liga=$liga
                  ORDER BY
                    c.name";
        } else {
            $query = "SELECT
                    c.name,
                    b.vorname,
                    b.nachname,
                    b.id as spielerid,
                    c.id as mannid,
                    sum(a.punkte) AS punkte,
                    sum(a.tore_geschossen)-sum(a.tore_bekommen) AS diff,
                    sum(a.tore_geschossen) as tore_g,
                    sum(a.tore_bekommen) as tore_b
                  FROM
                    auswertung AS a,
                    spieler AS b,
                    mannschaften AS c
                  WHERE
                    a.spieler=b.id
                  AND
                    a.spieltag <= $spieltag
                  AND
                    c.id=b.mannschaft
                  AND
                    c.liga=$liga
                  GROUP BY
                    b.id
                  ORDER BY
                    punkte DESC,
                    diff DESC,
                    tore_g DESC";
        }

        $p = 1;
        $result = array();
        $qr = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($qr)) {

            $data = $r;
            $data['platz'] = $p;

            if(mysqli_num_rows($q1) === 0)
            {
                $the_div = 0;
                $the_tore = 0;
                $the_tore_get = 0;
                $the_punkte = 0;
            }
            else
            {
                $the_div = $r['diff'];
                $the_tore = $r['tore_g'];
                $the_tore_get = $r['tore_b'];
                $the_punkte = $r['punkte'];
            }

            if($r["spielerid"] == $_SESSION["user"]["id"]) {
                $data['me'] = true;
            } else {
                $data['me'] = false;
            }

            $data['diff'] = $the_div ;
            $data['toreplus'] = $the_tore;
            $data['toreminus'] = $the_tore_get;
            $data['punkte'] = $the_punkte;

            //letzte Platzierung...
            $last_spieltag = $spieltag-1;
            $aus_platz = getPlace($data['spielerid'],$last_spieltag,$db);
            $data['platzzuletzt'] = $aus_platz;

            $result[] = $data;
            $p++;
        };

        return $result;
    }

    function getUserTableSmall($spieltag, $liga, $myplace, $db) {

        $tabledata = getUserTable($spieltag, $liga, $db);

        //plaetze ausrechnen...
        if($myplace <=4)
        {
            $start = 0;
        }
        elseif($myplace >=15)
        {
            $start = 8;
        }
        else
        {
            $start = $myplace -5;
        }

        //echo $start;

        $data = array_slice($tabledata, $start, 10);

        return $data;

    }

    function getUserSummary($user, $db) {
        $query = "SELECT * FROM auswertung WHERE spieler=".$user." ORDER BY spieltag";

        $qr = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($qr)) {
            $data[] = $r;
        }
        return $data;
    }

    function getSummary($spieler, $db) {

        $query = "SELECT
						c.name,
						b.vorname,
						b.nachname,
						b.username,
						b.id,
						sum(a.dreier)*5 + sum(a.zweier)*4  + sum(a.einer)*3 AS punkte,
						sum(a.dreier) as drei,
						sum(a.zweier) as zwei,
						sum(a.einer) as ein,
						sum(a.nuller) as nul
					FROM
						auswertung AS a,
						spieler AS b,
						mannschaften AS c
					WHERE
						a.spieler=b.id
					AND
						c.id=b.mannschaft
					GROUP BY
						b.id
					ORDER BY
						punkte DESC";

        $qr = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($qr)) {

            $dta = $r;

            if($r['id'] == $spieler) {
                $dta['me'] = true;
            } else {
                $dta['me'] = false;
            }

            $data[] = $dta;
        }
        return $data;

    }

    function getBestSpieler($spieltag, $liga, $db)
    {
        if($spieltag > 0)
        {
            $query = "SELECT max(a.tore_geschossen) as punkte, b.vorname, b.nachname, c.name
                            FROM auswertung as a, spieler as b, mannschaften as c
                            WHERE c.liga=".$liga."
                            AND a.spieltag=".$spieltag."
                            AND a.spieler=b.id
                            AND b.mannschaft=c.id
                            GROUP BY a.id
                            ORDER BY punkte DESC";

            //print_r($query);
            $qr = mysqli_query($db, $query);

            $i = 0;
            $max = 0;
            while($r = mysqli_fetch_assoc($qr)) {
                if($i === 0) {
                    $max = $r['punkte'];
                }

                if($max === $r['punkte']) {
                    $data[] = $r;
                }

                $i++;
            }

            return $data;
        } else {
            return false;
        }

    }

    function getAllTippsFromGame($spiel, $db) {
        $query = "SELECT a.tipp1, a.tipp2, b.vorname, b.nachname
                    FROM tipps as a, spieler as b 
                    WHERE a.spieler=b.id 
                    AND spiel=".$spiel."
                    ORDER BY b.vorname";

        $qr = mysqli_query($db, $query);
        while($r = mysqli_fetch_assoc($qr)) {
            $data[] = $r;
        }
        return $data;
    }