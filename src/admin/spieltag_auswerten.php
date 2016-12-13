<?
	include("../lib/dblib.phps");
    db_connect();
        
	$file = "/var/httpd/htdocs/bltipp/spieltag.txt";
	
	if($action == "change")
	{
	}
	
	$cur_spieltag = $_REQUEST['spieltag'];
?>

<html>
<head>
  <title>Administration</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<form action="spieltag_auswerten.php" method="post" target="bottom">
<tr>
<td class="txt_felf_schwarz">[Spieltag auswerten]:</td>
<td class="txt_felf_schwarz">
	<select name="spieltag" size="1">
		<? if($cur_spieltag == 0): ?>
			<option value="0" selected>0</option>
		<? else: ?>
			<option value="0">0</option>
		<? endif; ?>
		<?
			for($i=1;$i<35;$i++)
			{
				if($i == $cur_spieltag)
				{
					echo "<option value=\"$i\" selected>$i</option>";
				}
				else
				{
					echo "<option value=\"$i\">$i</option>";
				}
			}
		?>
	</select>
	<input type="submit" value="Auswerten">
</td>
</tr>

</table>
<?
   //erstmal alles weg von dem Spieltag...
   $q_d = "DELETE FROM auswertung WHERE spieltag=".$cur_spieltag;
   mysql_query($q_d);

	$query2 = "SELECT * 
				  FROM spiele 
				  WHERE spieltag=$cur_spieltag
				  ORDER BY liga";
	$result2 = mysql_query($query2);
	
	if(mysql_num_rows($result2)>0)
	{
		for($j=0;$j<mysql_num_rows($result2);$j++)
		{
			$s_id = mysql_result($result2,$j,'id');
			$mannschaft1 = mysql_result($result2,$j,'mannschaft1');
			$mannschaft2 = mysql_result($result2,$j,'mannschaft2');
			$spieltag = mysql_result($result2,$j,'spieltag');

			$query_u1 = "SELECT id,vorname,nachname FROM spieler WHERE mannschaft=$mannschaft1";
      	    $result_u1 = mysql_query($query_u1);
      	    $u1_id = mysql_result($result_u1,0,'id');
      	    $u1_nachname = mysql_result($result_u1,0,'nachname');
			$u1_vorname = mysql_result($result_u1,0,'vorname');

			$query_u2 = "SELECT id,vorname,nachname FROM spieler WHERE mannschaft=$mannschaft2";
      	    $result_u2 = mysql_query($query_u2);
      	    $u2_id = mysql_result($result_u2,0,'id');
			$u2_nachname = mysql_result($result_u2,0,'nachname');
			$u2_vorname = mysql_result($result_u2,0,'vorname');
			
			$u1_points = calculateSpieltagUser($u1_id,$cur_spieltag);
			$u2_points = calculateSpieltagUser($u2_id,$cur_spieltag);
				
			echo "$u1_vorname $u1_nachname -> <b>".$u1_points['gesamtpunkte']."</b> (".$u1_points['dreier']."/".$u1_points['zweier']."/".$u1_points['einer']."/".$u1_points['nuller'].")";
			echo " - ";
			echo "$u2_vorname $u2_nachname -> <b>".$u2_points['gesamtpunkte']."</b> (".$u2_points['dreier']."/".$u2_points['zweier']."/".$u2_points['einer']."/".$u2_points['nuller'].")<br>";
		
			//Punkte fuer die Tabelle...
			$tor_div = $u1_points['gesamtpunkte'] - $u2_points['gesamtpunkte'];			
			$u1_tab_punkte = $u2_tab_punkte = 0;		
			
			if( ($u1_points['gesamtpunkte'] > -1) && ($u2_points['gesamtpunkte'] > -1) )
			{
				if($tor_div > 0)
				{
					$u1_tab_punkte = 3;
					$u2_tab_punkte = 0;
				}
				elseif($tor_div < 0)
				{
					$u1_tab_punkte = 0;
					$u2_tab_punkte = 3;
				}
				elseif($tor_div == 0)
				{
					$u1_tab_punkte = 1;
					$u2_tab_punkte = 1;
				}
			}
		
			//echo "PUNKTE (TDIV:$tor_div) $u1_tab_punkte : $u2_tab_punkte<br>";	
		
			$query_ins_u1 = "INSERT INTO auswertung(
									spieltag,
									spieler,
									punkte,
									tore_geschossen,
									tore_bekommen,
									dreier,
									zweier,
									einer,
									nuller)
 							VALUES(
									'".$cur_spieltag."',
									'".$u1_id."',
									'".$u1_tab_punkte."',
									'".$u1_points['gesamtpunkte']."',
									'".$u2_points['gesamtpunkte']."',
									'".$u1_points['dreier']."',
									'".$u1_points['zweier']."',
									'".$u1_points['einer']."',
									'".$u1_points['nuller']."')";
			mysql_query($query_ins_u1);
			
			$query_ins_u2 = "INSERT INTO auswertung(
									spieltag,
									spieler,
									punkte,
									tore_geschossen,
									tore_bekommen,
									dreier,
									zweier,
									einer,
									nuller)
 							VALUES(
									'".$cur_spieltag."',
									'".$u2_id."',
									'".$u2_tab_punkte."',
									'".$u2_points['gesamtpunkte']."',
									'".$u1_points['gesamtpunkte']."',
									'".$u2_points['dreier']."',
									'".$u2_points['zweier']."',
									'".$u2_points['einer']."',
									'".$u2_points['nuller']."')";
			mysql_query($query_ins_u2);
		}
   }
   
   echo "<br /><br />";

	$query3 = "SELECT a.id, b.id as userid, c.liga 
				  FROM auswertung as a, spieler as b, mannschaften as c 
				  WHERE a.spieltag=$cur_spieltag
				  AND a.spieler=b.id
				  AND b.mannschaft=c.id
				  ORDER BY c.liga,a.spieler";
				  
	$result3 = mysql_query($query3);
	
	if(mysql_num_rows($result3)>0)
	{
		for($k=0;$k<mysql_num_rows($result3);$k++)
		{
			$auswertid = mysql_result($result3,$k,'id');
			$userid = mysql_result($result3,$k,'userid');
			$liga = mysql_result($result3,$k,'liga');
			
			$platz = getUserTabPosition($liga,'/var/httpd/htdocs',$userid);
			
			$q_u = "UPDATE auswertung SET platz=".$platz." WHERE id=".$auswertid;
			$r_u = mysql_query($q_u);
		}
	}

?>

</body>
</html>

<? db_close(); ?>