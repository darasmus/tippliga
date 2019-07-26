
<?php

    require('../api/api.lib.php');

    $matchCount = 35;
    $league = 'bl3';
    $year = 2019;

    if($league === 'bl3') {
        $queryleague = 'bl1';
    } else {
        $queryleague = $league;
    }

    for($i=1;$i<$matchCount;$i++) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.openligadb.de/api/getmatchdata/".$queryleague ."/".$year."/".$i); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        curl_close($ch);
        $matches = json_decode($output, true);

        foreach($matches as $match) {
            $selectTeam1 = "SELECT id from mannschaften WHERE api_id=".$match[Team1][TeamId];
            $qSelectTeam1 = mysqli_query($db, $selectTeam1);
            while($r1 = mysqli_fetch_assoc($qSelectTeam1)) {
                $rTeam1 = $r1;
            }

            $selectTeam2 = "SELECT id from mannschaften WHERE api_id=".$match[Team2][TeamId];
            $qSelectTeam2 = mysqli_query($db, $selectTeam2);
            while($r2 = mysqli_fetch_assoc($qSelectTeam2)) {
                $rTeam2 = $r2;
            }

            $spieldatum = strtotime($match[MatchDateTime]);

            if($league === 'bl3') {

                $selectPendantTeam1 = "SELECT id,name from mannschaften WHERE pendant_id=".$rTeam1[id];
                $qSelectPendantTeam1 = mysqli_query($db, $selectPendantTeam1);
                while($rp1 = mysqli_fetch_assoc($qSelectPendantTeam1)) {
                    $rpTeam1 = $rp1;
                }

                $selectPendantTeam2 = "SELECT id,name from mannschaften WHERE pendant_id=".$rTeam2[id];
                $qSelectPendantTeam2 = mysqli_query($db, $selectPendantTeam2);
                while($rp2 = mysqli_fetch_assoc($qSelectPendantTeam2)) {
                    $rpTeam2 = $rp2;
                }

                echo $rpTeam1[name]."(".$rpTeam1[id].") : ".$rpTeam2[name]."(".$rpTeam2[id].")\n";
                //insert
                $query_insert = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag) VALUES('$rpTeam1[id]','$rpTeam2[id]','-1','-1','$spieldatum','$i')";

            } else {
                echo $match[Team1][TeamName]."(".$rTeam1[id].") : ".$match[Team2][TeamName]."(".$rTeam2[id].")\n";
                //insert
                $query_insert = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag) VALUES('$rTeam1[id]','$rTeam2[id]','-1','-1','$spieldatum','$i')";
            }
        
            $insert = mysqli_query($db, $query_insert);
            echo $query_insert."\n";
        }
    }

?>