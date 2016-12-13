<?
	include("../lib/dblib.phps");
        db_connect();

	$error = array();
	$gruppen = array('A','B','C','D');

	if($action == "delete")
	{
		$query_delete = "DELETE FROM mannschaften WHERE ID=$id";
		$result_delete = mysql_query($query_delete);
		header("Location: mannschaften.php");
	}
	else
	{
		//Daten auslesen
                $query_select = "SELECT * FROM mannschaften WHERE ID=$id";
                $result_select = mysql_query($query_select);
                $name = mysql_result($result_select,0,'name');
                $gruppe = mysql_result($result_select,0,'gruppe');
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
  <td><a href="add_mannschaft.php" class="txt_felf_schwarz">[neue Mannschaft anlegen]</a></td>
</tr>

<? 
	if(count($error)>0)
	{
		foreach ($error as $key => $value)
		{
			echo "<tr><td colspan=\"2\" class=\"txt_felf_rot\">$value</td></tr>";
		}
	}
?>

</table>

<form action="del_mannschaft.php" method="post">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?=$id?>">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Name]:</td>
<td class="txt_felf_schwarz"><?=$name?></tr>

<tr>
<td class="txt_felf_schwarz">[Gruppe]:</td>
<td class="txt_felf_schwarz"><?=$gruppe?></td>
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




