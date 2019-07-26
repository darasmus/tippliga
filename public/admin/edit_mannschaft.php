<?
	include("../lib/dblib.phps");
        db_connect();

		header("Content-Type: text/html; charset=utf-8");

	$error = array();
	$gruppen = array('A','B','C','D');

	if($action == "edit")
	{
		if($name == "")
		{
			$error[0] = "Bitte einen Namen eingeben!!!";
		}

		if(count($error)==0)
		{
			$query_update = "UPDATE mannschaften SET name='$name',
								 meister='$meister',
								 herbstmeister='$herbstmeister',
								 liga='$liga'
						WHERE ID=$id";
			$result_update = mysql_query($query_update);
			header("Location: mannschaften.php");
		}
		
	}
	else
	{
		//Daten auslesen
                $query_select = "SELECT * FROM mannschaften WHERE ID=$id";
                $result_select = mysql_query($query_select);
                $name = mysql_result($result_select,0,'name');
		$meister = mysql_result($result_select,0,'meister');
		$herbstmeister = mysql_result($result_select,0,'herbstmeister');
		$liga = mysql_result($result_select,0,'liga');
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

<form action="edit_mannschaft.php" method="post">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?=$id?>">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Name]:</td>
<td class="txt_felf_schwarz"><input type="text" name="name" value="<?=$name?>" size="20"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Liga]:</td>
<td>
        <select name="liga" size="1">
		<? if($liga == 1): ?>
		   <option value="1" selected>1. Liga</option>
		   <option value="2">2. Liga</option>
           <option value="3">3. Liga</option>
		<? elseif($liga == 2): ?>
		   <option value="1">1. Liga</option>
		   <option value="2" selected>2. Liga</option>
           <option value="3">3. Liga</option>
        <? else: ?>
		   <option value="1">1. Liga</option>
           <option value="2">2. Liga</option>
		   <option value="3" selected>3. Liga</option>
		<? endif; ?>
        </select>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Meister]:</td>
<td class="txt_felf_schwarz">
	<select name="meister" size="1">
	<?
		if($meister == 1)
		{
			echo "<option value=\"0\">Nein</option>";
			echo "<option value=\"1\" selected>Ja</option>";
		}
		else
		{
			echo "<option value=\"0\" selected>Nein</option>";
			echo "<option value=\"1\">Ja</option>";
			
		}	
	?>
	</select>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Herbsteister]:</td>
<td class="txt_felf_schwarz">
	<select name="herbstmeister" size="1">
	<?
		if($herbstmeister == 1)
		{
			echo "<option value=\"0\">Nein</option>";
			echo "<option value=\"1\" selected>Ja</option>";
		}
		else
		{
			echo "<option value=\"0\" selected>Nein</option>";
			echo "<option value=\"1\">Ja</option>";
			
		}	
	?>
	</select>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Speichern]:</td>
<td class="txt_felf_schwarz">
	<input type="submit" value="Speichern">
</td>
</tr>

</table>
</form>

</body>
</html>

<? db_close(); ?>




