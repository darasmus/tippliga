<?
	include("../lib/dblib.phps");
   db_connect();
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Administration TippLIGA</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">
<form action="edit_spieltag.php" method="post">
<table>

<tr>
	<td colspan="2"><a href="add_spieltag.php" style="color:black;font-weight:bold;">[Neuen Spieltag anlegen]</a></td>
</tr>
<tr>
    <td colspan="2">Nicht angelegte Spieltage m&uuml;ssen erst angelegt werden (die R&uuml;ckrunde wird dann automatisch mit angelegt).<br /><br /></td>
</tr>
    
<tr>
	<td>[Spieltag w&auml;hlen]</td>
	<td>
	<select name="spieltag" size="1">
		<?
			for($i=1;$i<35;$i++)
			{
				$q = "SELECT id FROM spiele WHERE spieltag=$i";
				$result = mysql_query($q);
        		$all = mysql_num_rows($result);				
				
				if($all > 0)
				{
					echo '<option value="'.$i.'">Spieltag '.$i.' (angelegt)</option>';
				}
				else
				{
					echo '<option value="'.$i.'">Spieltag '.$i.' (NICHT ANGELEGT)</option>';
				}
			}
		?>
	</select>
	</td>
</tr>

<tr>
	<td colspan="2"><input type="submit" value="Bearbeiten"></td>
</tr>
</table>
</form>

</body>
</html>

<? db_close(); ?>