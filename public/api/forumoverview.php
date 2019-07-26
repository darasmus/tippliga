<?php
    require('session.inc.php');
    require('api.lib.php');

    //all parents
    $query = "SELECT *
              FROM beitraege
              WHERE child=0 ORDER BY datum DESC";

    $forum = array();
    $qf = mysqli_query($db, $query);
    while($r = mysqli_fetch_assoc($qf)) {
        $parent = $r;

        $datum = strftime('%d.%m.%Y - %H:%M',$r['datum']);
        $parent['datum'] = $datum;

        $beitrag = str_replace("\r\n",'', trim($r['beitrag']));
        $beitrag = str_replace("<",'&lt;', $beitrag);
        $beitrag = str_replace(">",'&gt;', $beitrag);
        $parent['beitrag'] = $beitrag;

        $query2 = "SELECT count(*) AS count FROM beitraege WHERE child=". $parent['id'];
        $qf2 = mysqli_query($db, $query2);
        while($r2 = mysqli_fetch_assoc($qf2)) {

            $parent['count'] = $r2['count'];

            if($r2['count'] > 0) {
                $query3 = "SELECT max(datum) as neues_datum FROM beitraege WHERE child=". $parent['id'];
                $qf3 = mysqli_query($db, $query3);
                while($r3 = mysqli_fetch_assoc($qf3)) {
                    $parent['latest'] = strftime('%d.%m.%Y - %H:%M',$r3['neues_datum']);
                }
            } else {
                $parent['latest'] = $parent['datum'];
            }

        }

        $issues[] = $parent;
    }

    $data['issues'] = $issues;

    $json = json_encode($data);
    print_r($json);