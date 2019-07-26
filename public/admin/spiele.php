<?
	header("Content-Type: text/html; charset=utf-8");

	if($spieltag == "")
	{
		$spieltag = 1;
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
  <td><a href="add_spiel.php?spieltag=<?=$spieltag?>" class="txt_felf_schwarz">[neues Spiel anlegen]</a></td>
</tr>

</table>

<table cellpadding="2" border=1 bordercolor="#000000">

<tr>
<td class="txt_felf_schwarz">[ST - Datum]</td>
<td class="txt_felf_schwarz">[Mannschaft 1]</td>
<td class="txt_felf_schwarz">&nbsp;</td>
<td class="txt_felf_schwarz">[Mannschaft 2]</td>
<td class="txt_felf_schwarz">[Ergebnis]</td>
<td class="txt_felf_schwarz">[EDIT/DELETE]</td>
</tr>

<form action="spiele.php" method="post">
<tr>
<td class="txt_felf_schwarz" colspan="6">
Spieltag:
<select name="spieltag" size="1">
<?
	for($i=1;$i<35;$i++)
	{
		if($i==$spieltag)
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
<input type="submit" value=">>">
</td>
</tr>
</form>

<?
	include("../lib/dblib.phps");
	db_connect();
	$query = "SELECT * FROM spiele WHERE spieltag=$spieltag ORDER BY spieltag,spieldatum";
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
		$spieldatum = mysql_result($result,$i,'spieldatum');
		$spieltag = mysql_result($result,$i,'spieltag');

		$query_m1 = "SELECT name as mannschaft1 FROM mannschaften WHERE id=$mann1";
		$result_m1 = mysql_query($query_m1);
		$mannschaft1 = mysql_result($result_m1,0,'mannschaft1');

		$query_m2 = "SELECT name as mannschaft2 FROM mannschaften WHERE id=$mann2";
		$result_m2 = mysql_query($query_m2);
		$mannschaft2 = mysql_result($result_m2,0,'mannschaft2');

		$the_date = getdate($spieldatum);
                $the_monat = $the_date['mon'];
                $the_tag = $the_date['mday'];
                $the_jahr = $the_date['year'];

		if(strlen($the_monat) == 1)
		{
			$the_monat = "0".$the_monat;
		}
		if(strlen($the_tag) == 1)
		{
			$the_tag = "0".$the_tag;
		}

		$datum = "$the_tag.$the_monat.$the_jahr";

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

		echo "<tr bgcolor=\"$bgcolor\">\n";
		echo "<td class=\"txt_elf_schwarz\">$spieltag - $datum</td>";
		echo "<td class=\"txt_elf_schwarz\">$mannschaft1</td>";
		echo "<td class=\"txt_elf_schwarz\">-</td>";
		echo "<td class=\"txt_elf_schwarz\">$mannschaft2</td>\n";
		echo "<td class=\"txt_elf_schwarz\">$ergebnis</td>";
		echo "<td>";
		echo "<a href=\"edit_spiel.php?id=$id\" class=\"txt_felf_schwarz\">[edit]</a>&nbsp;&nbsp;";
		echo "<a href=\"del_spiel.php?id=$id\" class=\"txt_felf_rot\">[del]</a>";
		echo "</td>";
		echo "</tr>\n";

		$i++;
	}
	
	db_close();
?>

</table>

</body>
</html>