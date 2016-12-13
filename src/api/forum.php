<?php
    require('session.inc.php');
    require('api.lib.php');

    $id = $_GET['id'];

    //all parents
    $query = "SELECT *
              FROM beitraege
              WHERE id=".$id." ORDER BY datum DESC";

    $forum = array();
    $qf = mysqli_query($db, $query);
    while($r = mysqli_fetch_assoc($qf)) {
        $parent = $r;

        $datum = strftime('%d.%m.%Y - %H:%M',$r['datum']);
        $parent['datum'] = $datum;

        $beitrag = $r['beitrag'];
        //$beitrag = str_replace("\r\n",'', trim($r['beitrag']));
        //$beitrag = str_replace("<",'&lt;', $beitrag);
        //$beitrag = str_replace(">",'&gt;', $beitrag);
        $parent['beitrag'] = $beitrag;

        //get children
        $children = array();
        $query2 = "SELECT * FROM beitraege WHERE child=". $parent['id'];
        $qf2 = mysqli_query($db, $query2);
        while($r2 = mysqli_fetch_assoc($qf2)) {
            $child = $r2;

            $datum2 = strftime('%d.%m.%Y - %H:%M',$r2['datum']);
            $child['datum'] = $datum2;

            $beitrag2 = $r2['beitrag'];
            //$beitrag2 = str_replace("\r\n",'', trim($r2['beitrag']));
            //$beitrag2 = str_replace("<",'&lt;', $beitrag2);
            //$beitrag2 = str_replace(">",'&gt;', $beitrag2);
            $child['beitrag'] = $beitrag2;

            $children[] = $child;
        }

        $parent['children'] = $children;
        $issues[] = $parent;
    }

    $data['issues'] = $issues;

    $json = json_encode($data);
    print_r($json);