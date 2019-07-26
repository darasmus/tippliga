<?
	include("header.php");

	if($_REQUEST['liga'] > 0)
		$req_liga = $_REQUEST['liga'];
	else
		$req_liga = $_SESSION['usrliga'];

?>

<html>
<head>
  	<title>TIPPLIGA</title>
   	<style type="text/css" media="screen">@import url("style/screen.css");</style>
	<!--[if IE 5]><style type="text/css">@import "/style/ie5.css";</style><![endif]-->
	<!--[if IE 6]><style type="text/css">@import "/style/ie6.css";</style><![endif]-->
	<!--[if IE 7]><style type="text/css">@import "/style/ie7.css";</style><![endif]-->
</head>

<body bgcolor="#FFFFFF">

<table cellpadding="2" border="1">
<tr>
  <th>&nbsp;&nbsp;&nbsp;Spiel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
  <th>&nbsp;Erg.&nbsp;</th>

<?

	$query_spieler = "SELECT a.*,b.name as mannschaftsname FROM spieler as a, mannschaften as b WHERE a.mannschaft=b.id AND b.liga=".$req_liga;
    $result_spieler = mysql_query($query_spieler);
    $all_spieler = mysql_num_rows($result_spieler);

	$i=0;
   while($i<$all_spieler)
   {
		$s_id = mysql_result($result_spieler,$i,'id');
		$s_vorname = mysql_result($result_spieler,$i,'vorname');
		$s_nachname = mysql_result($result_spieler,$i,'nachname');
        $s_username = mysql_result($result_spieler,$i,'username');
		$s_mannschaft = mysql_result($result_spieler,$i,'mannschaft');
		$m_name = mysql_result($result_spieler,$i,'mannschaftsname');

		$logo = "images/vlogos/".$s_mannschaft.".gif";
		$spielerids[$i] = $s_id;

        echo '<th><a class="tooltip" href="#"><img src="'.$logo.'" border="0" /><span>'.$s_vorname.' '.$s_nachname.'<br />('.$m_name.')</span></a></th>';
		echo "<th class=\"rot\">P.</th>";
		$i++;
	}
?>
</tr>

<?
	$tippdatum = getSpieldatum($_REQUEST['cur_st']);
	$query = "SELECT a.* FROM spiele as a,mannschaften as b WHERE a.spieltag=$cur_st AND b.liga=1 AND a.mannschaft1=b.id ORDER BY spieltag,spieldatum";
   	$result = mysql_query($query);
   	$all = mysql_num_rows($result);
?>

<? if(time() > $tippdatum): ?>

<?
    $ges_all = array();

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
      		
	  $logo_m1 = "images/vlogos/".$mann1.".gif";
	  $logo_m2 = "images/vlogos/".$mann2.".gif";
      
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
      echo "<td><img src=\"$logo_m1\"> : <img src=\"$logo_m2\"></td>";
      echo "<td class=\"rot\" style=\"text-align:center;\">$ergebnis</td>";


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
			
			$ges_all[$the_id] = $ges_all[$the_id] + $punkte;
			
          	//ENDE Punktberechnung

			echo "<td class=\"kleinunddick\" style=\"text-align:center;\">$tipp1:$tipp2</td>";
			echo "<td class=\"rot\" style=\"text-align:center;\">$punkte</td>";
		}
		echo "</tr>";
		$i++;
	}
?>

<tr style="height:22px;">
<td colspan="2" class="kleinunddick" style="background-color:#CCCCCC;">Gesamtpunkte:</td>
<?
	for($s=0;$s<$anzahl_s;$s++)
	{
			$theid = $spielerids[$s];
			echo '<td colspan="2" class="rot" style="text-align:center;background-color:#CCCCCC;">'.$ges_all[$theid].'</td>';
	}
?>

</tr>
</table>

<? else: ?>
<table cellpadding="2" cellspacing="0" border="0">
<tr>
  <td class="rot">Dieser Spieltag darf noch nicht eingesehen werden!</td>
</tr>
</table>
<? endif; ?>

</body>
</html>
