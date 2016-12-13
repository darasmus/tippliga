<?
	include("../lib/dblib.phps");
   db_connect();
        
	$cur_spieltag = getLastSpieltag();  	
?>

<html>
<head>
  <title>Administration Euro2004 Tippspiel</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<form action="spieltag_auswerten.php" method="post" target="bottom" >

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

</body>
</html>

<? db_close(); ?>
