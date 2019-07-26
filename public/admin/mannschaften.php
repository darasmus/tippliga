<? header("Content-Type: text/html; charset=utf-8"); ?>

<html>
<head>
  <title>Administration Euro2004 Tippspiel</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<tr>
  <td class="txt_fzwoelf_schwarz">Spiele</td>
  <td><a href="add_mannschaft.php" class="txt_felf_schwarz">[neue Mannschaft anlegen]</a></td>
</tr>

</table>

<table cellpadding="2" border=1 bordercolor="#000000">

<tr>
<td class="txt_felf_schwarz">[Name (Liga)]</td>
<td class="txt_felf_schwarz">[EDIT/DELETE]</td>
</tr>

<?
	include("../lib/dblib.phps");
	db_connect();
	$query = "SELECT * FROM mannschaften ORDER BY liga,name";
        $result = mysql_query($query);
        $all = mysql_num_rows($result); 

        $i=0;
        while($i<$all)
        {
                $id = mysql_result($result,$i,'id');
                $name = mysql_result($result,$i,'name');
		$liga = mysql_result($result,$i,'liga');


		if($i%2 == 0)
                {
                        $bgcolor="#FFFFFF";
                }
                else
                {
                        $bgcolor="#EEEEEE";
                }

		echo "<tr bgcolor=\"$bgcolor\">\n";
		echo "<td class=\"txt_elf_schwarz\">$name ($liga)</td>";
		echo "<td>";
		echo "<a href=\"edit_mannschaft.php?id=$id\" class=\"txt_felf_schwarz\">[edit]</a>&nbsp;&nbsp;";
		echo "<a href=\"del_mannschaft.php?id=$id\" class=\"txt_felf_rot\">[del]</a>";
		echo "</td>";
		echo "</tr>\n";

		$i++;
	}
	
	db_close();
?>

</table>

</body>
</html>