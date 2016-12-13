<?
	include("../lib/dblib.phps");
        db_connect();

	$error = array();

	if($action == "delete")
	{
		$query_delete = "DELETE FROM spiele WHERE ID=$id";
		$result_delete = mysql_query($query_delete);
		header("Location: spiele.php");
	}
	else
	{
		//Daten auslesen
		$query_select = "SELECT * FROM spiele WHERE ID=$id";
		$result_select = mysql_query($query_select);
                $mannschaft1 = mysql_result($result_select,0,'mannschaft1');
                $mannschaft2 = mysql_result($result_select,0,'mannschaft2');
                $tore1 = mysql_result($result_select,0,'tore1');
                $tore2 = mysql_result($result_select,0,'tore2');
                $spieldatum = mysql_result($result_select,0,'spieldatum');
		$tippdatum = mysql_result($result_select,0,'tippdatum');
		$spieltag = mysql_result($result_select,0,'spieltag');
		
		if($tore1==-1)
		{
			$tore1="-";
		}
		if($tore2==-1)
		{
			$tore2="-";
		}

		$sd_a = getdate($spieldatum);
		$tag = $sd_a['mday'];
		$monat = $sd_a['mon'];
		$jahr = $sd_a['year'];
		$stunde = $sd_a['hours'];
		$minute = $sd_a['minutes'];
		if($minute == 0)
		{
			$minute = "0".$minute;
		}

		$spieldatum = "$tag.$monat.$jahr - $stunde:$minute";

		$td_a = getdate($tippdatum);
		$tipptag = $td_a['mday'];
		$tippmonat = $td_a['mon'];
		$tippjahr = $td_a['year'];
		$tippstunde = $td_a['hours'];
		$tippminute = $td_a['minutes'];
		if($tippminute == 0)
		{
			$tippminute = "0".$tippminute;
		}

		$tippdatum = "$tipptag.$tippmonat.$tippjahr - $tippstunde:$tippminute";
	}

?>

<html>
<head>
  <title>Administration Euro2004 Tippspiel</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<tr>
  <td class="txt_fzwoelf_schwarz">Spiele</td>
  <td><a href="add_spiel.php" class="txt_felf_schwarz">[neues Spiel anlegen]</a></td>
</tr>

</table>

<form action="del_spiel.php" method="post">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?=$id?>">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Spieltag]:</td>
<td class="txt_felf_schwarz"><?=$spieltag?></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Datum]:</td>
<td class="txt_felf_schwarz"><?=$spieldatum?></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Tippdatum]:</td>
<td class="txt_felf_schwarz"><?=$tippdatum?></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Spielpaarung]:</td>
<td class="txt_felf_schwarz">
	<?
        	$query = "SELECT * FROM mannschaften WHERE id=$mannschaft1";
        	$result = mysql_query($query);
		$name1 = mysql_result($result,0,'name');
		$gruppe1 = mysql_result($result,0,'gruppe');

        	$query = "SELECT * FROM mannschaften WHERE id=$mannschaft2";
        	$result = mysql_query($query);
		$name2 = mysql_result($result,0,'name');
		$gruppe2 = mysql_result($result,0,'gruppe');

		echo "$name1($gruppe1) - $name2($gruppe2)";
	?>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Ergebnis]:</td>
<td class="txt_felf_schwarz"><?=$tore1?>&nbsp;:&nbsp;<?=$tore2?>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Speichern]:</td>
<td class="txt_felf_schwarz">
	<input type="submit" value="L&ouml;schen">
</td>
</tr>

</table>
</form>

</body>
</html>

<? db_close(); ?>
