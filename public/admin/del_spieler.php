<?
	include("../lib/dblib.phps");
        db_connect();

	$error = array();
	$gruppen = array('A','B','C','D');

	if($action == "delete")
	{
		$query_update = "DELETE FROM spieler WHERE ID=$id";
		$result_update = mysql_query($query_update);
		header("Location: spieler.php");
	}
	else
	{
		//Daten auslesen
                $query_select = "SELECT * FROM spieler WHERE ID=$id";
                $result_select = mysql_query($query_select);
                $nachname = mysql_result($result_select,0,'nachname');
                $vorname = mysql_result($result_select,0,'vorname');
		$username = mysql_result($result_select,0,'username');
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
  <td><a href="add_spieler.php" class="txt_felf_schwarz">[neuen Spieler anlegen]</a></td>
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

<form action="del_spieler.php" method="post">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?=$id?>">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Nachname]:</td>
<td class="txt_felf_schwarz"><?=$nachname?></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Vorname]:</td>
<td class="txt_felf_schwarz"><?=$vorname?></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Username]:</td>
<td class="txt_felf_schwarz"><?=$username?></td>
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




