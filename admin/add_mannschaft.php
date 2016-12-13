<?
	include("../lib/dblib.phps");
        db_connect();

	$error = array();

	if($action == "add")
	{
		if($name == "")
		{
			$error[0] = "Bitte einen Namen eingeben!!!";
		}

		if(count($error)==0)
		{
			$query_insert = "INSERT INTO mannschaften(name,meister,herbstmeister,liga)
						VALUES('$name','$meister','$herbstmeister','$liga')";
			$result_insert = mysql_query($query_insert);
			header("Location: mannschaften.php");
		}
		
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

<form action="add_mannschaft.php" method="post">
<input type="hidden" name="action" value="add">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Name]:</td>
<td class="txt_felf_schwarz"><input type="text" name="name" value="<?=$name?>" size="20"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Liga]:</td>
<td>
	<select name="liga" size="1">
		<option value="1">1. Liga</option>
		<option value="2">2. Liga</option>	
        <option value="3">3. Liga</option>
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
<td class="txt_felf_schwarz">[Herbstmeister]:</td>
<td class="txt_felf_schwarz">
        <select name="herbstmeister" size="1">
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




