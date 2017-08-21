<html>
<head>
  <title>Administration Euro2004 Tippspiel</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<tr>
  <td class="txt_fzwoelf_schwarz">Spiele</td>
  <td><a href="add_spieler.php" class="txt_felf_schwarz">[neuen Spieler anlegen]</a></td>
</tr>

</table>

<table cellpadding="2" border=1 bordercolor="#000000">

<tr>
<td class="txt_felf_schwarz">[Team]</td>
<td class="txt_felf_schwarz">[Nachname]</td>
<td class="txt_felf_schwarz">[Vorname]</td>
<td class="txt_felf_schwarz">[EDIT/DELETE]</td>
</tr>

<tr><td colspan="4" class="txt_felf_schwarz">1. Liga</td></tr>

<?
		include("../lib/dblib.phps");
		db_connect();
		$query = "SELECT a.id,a.vorname,a.nachname,a.mannschaft,b.liga 
	          	  FROM spieler as a, mannschaften as b 
		          WHERE a.mannschaft=b.id 
		          ORDER BY b.liga,a.nachname";
        $result = mysql_query($query);
        $all = mysql_num_rows($result); 
	    $done = 0;

        $i=0;
        while($i<$all)
        {
                $id = mysql_result($result,$i,'id');
                $nachname = utf8_encode(mysql_result($result,$i,'nachname'));
                $vorname = utf8_encode(mysql_result($result,$i,'vorname'));
				$mannschaft = mysql_result($result,$i,'mannschaft');
				$liga = mysql_result($result,$i,'liga');

		$logo = "/img/wappen/".$mannschaft.".png";

		if($i%2 == 0)
		{
			$bgcolor="#FFFFFF";
		}
		else
		{
			$bgcolor="#EEEEEE";
		}

		if( ($liga == 2) && ($done == 0) )
		{
			echo "<tr><td colspan=\"4\" class=\"txt_felf_schwarz\">2. Liga</td></tr>";
			$done = 1;
		}
		elseif( ($liga == 3) && ($done == 1) )
		{
			echo "<tr><td colspan=\"4\" class=\"txt_felf_schwarz\">3. Liga</td></tr>";
			$done = 2;
		}

		echo "<tr bgcolor=\"$bgcolor\">\n";
		echo "<td class=\"txt_elf_schwarz\" align=\"center\"><img width=\"32\" src=\"$logo\"></td>";
		echo "<td class=\"txt_elf_schwarz\">$nachname</td>";
		echo "<td class=\"txt_elf_schwarz\">$vorname</td>";
		echo "<td>";
		echo "<a href=\"edit_spieler.php?id=$id\" class=\"txt_felf_schwarz\">[edit]</a>&nbsp;&nbsp;";
		echo "<a href=\"del_spieler.php?id=$id\" class=\"txt_felf_rot\">[del]</a>";
		echo "</td>";
		echo "</tr>\n";

		$i++;
	}
	
	db_close();
?>

</table>

</body>
</html>
