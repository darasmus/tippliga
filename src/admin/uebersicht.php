<html>
<head>
  <title>Administration Euro2004 Tippspiel</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<tr>
  <td class="txt_fzwoelf_schwarz">Tipp-&Uuml;bersicht</td>
  <td><a href="add_spiel.php" class="txt_felf_schwarz">[neues Spiel anlegen]</a></td>
</tr>

</table>

<table cellpadding="2" border=1 bordercolor="#000000">
<tr>
  <td class="txt_felf_schwarz">Spiel</td>
  <td class="txt_felf_schwarz">Ergebnnis</td>

<?
	include("../lib/dblib.phps");
	db_connect();
	
	$query_spieler = "SELECT * FROM spieler";
        $result_spieler = mysql_query($query_spieler);
        $all_spieler = mysql_num_rows($result_spieler);

	$i=0;
        while($i<$all_spieler)
        {
		$s_id = mysql_result($result_spieler,$i,'id');
		$s_vorname = mysql_result($result_spieler,$i,'vorname');	
		$s_nachname = mysql_result($result_spieler,$i,'nachname');
	
		$spielerids[$i] = $s_id;

		echo "<td class=\"txt_felf_schwarz\">$s_vorname $s_nachname</td>";
		echo "<td class=\"txt_felf_rot\">Pts.</td>";		

		$i++;
	}
?>

</tr>


<?
        $query = "SELECT * FROM spiele ORDER BY spieltag,spieldatum";
        $result = mysql_query($query);
        $all = mysql_num_rows($result);

	$i=0;
        while($i<$all)
        {
                $id = mysql_result($result,$i,'id');
                $mann1 = mysql_result($result,$i,'mannschaft1');
                $mann2 = mysql_result($result,$i,'mannschaft2');
                $tore1 = mysql_result($result,$i,'tore1');
                $tore2 = mysql_result($result,$i,'tore2');

		$query_m1 = "SELECT name as mannschaft1 FROM mannschaften WHERE id=$mann1";
                $result_m1 = mysql_query($query_m1);
                $mannschaft1 = mysql_result($result_m1,0,'mannschaft1');

                $query_m2 = "SELECT name as mannschaft2 FROM mannschaften WHERE id=$mann2";
                $result_m2 = mysql_query($query_m2);
                $mannschaft2 = mysql_result($result_m2,0,'mannschaft2');

		if($tore1 == -1)
                {
                        $tore1 = "-";
                }
                if($tore2 == -1)
                {
                        $tore2 = "-";
                }

		$ergebnis = "$tore1:$tore2";		

		if($i%2 == 0)
                {
                        $bgcolor="#FFFFFF";
                }
                else
                {
                        $bgcolor="#EEEEEE";
                }

		$anzahl_s = count($spielerids);

		echo "<tr bgcolor=\"$bgcolor\">\n";
                echo "<td class=\"txt_elf_schwarz\">$mannschaft1 - $mannschaft2</td>";
                echo "<td class=\"txt_felf_rot\" align=\"center\">$ergebnis</td>";

		for($s=0;$s<$anzahl_s;$s++)
		{
			$the_id = $spielerids[$s];
			
			$query_tipp = "SELECT tipp1,tipp2 FROM tipps WHERE spieler=$the_id AND spiel=$id";
        		$result_tipp = mysql_query($query_tipp);
			
			$tipp1 = "";
			$tipp2 = "";

			if(mysql_num_rows($result_tipp)>0)
			{
				$tipp1 = mysql_result($result_tipp,0,'tipp1');
                		$tipp2 = mysql_result($result_tipp,0,'tipp2');
			}

			if( ($tipp1 == -1) || ($tipp1 == "") )
                	{
                        	$tipp1 = "-";
                	}
                	if( ($tipp2 == -1) || ($tipp2 == "") )
                	{
                        	$tipp2 = "-";
                	}


			    // Punkteberechnung..
                            $div1 = $tore1 - $tore2;
                            $div2 = $tipp1 - $tipp2;

                            if( ($tore1 != "-") && ($tore2 != "-") && ($tipp1 != "-") && ($tipp2 != "-") )
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
                                      $punkte = 3;
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
                             //ENDE Punktberechnung
			
									
			echo "<td class=\"txt_elf_schwarz\" align=\"center\">$tipp1:$tipp2</td>";
			echo "<td class=\"txt_felf_rot\" align=\"center\">$punkte</td>";
		}

		echo "</tr>";
		$i++;
	}
?>	

</table>



</body>
</html>