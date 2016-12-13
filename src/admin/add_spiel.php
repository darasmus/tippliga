<?
	include("../lib/dblib.phps");
        db_connect();

	$error = array();

	if($action == "add")
	{
		
		$spieldatum = mktime ($stunde,$minute,0,$monat,$tag,$jahr);
		//$tippdatum = mktime ($tippstunde,$tippminute,0,$tippmonat,$tipptag,$tippjahr);

		//if($tippdatum > $spieldatum)
		//{
		//	$error[0] = "Das Tippdatum kann icht vor dem Spieldatum liegen!!!";
		//}

		if($mannschaft1 == $mannschaft2)
		{
			$error[1] = "Eine Mannschaft kann nicht gegen sich selbst spielen!!!";
		}

		if( ($tore1 != "") && !(ereg("^[0-9]{1,2}$",$tore1))  )
		{
			$error[2] = "Tore bitte als Zahl angeben!!!";
		}
		if( ($tore2 != "") && !(ereg("^[0-9]{1,2}$",$tore2))  )
		{
			$error[2] = "Tore bitte als Zahl angeben!!!";
		}

		if(count($error)==0)
		{
			if($tore1 == "")
			{
				$tore1 = -1;
			}
			if($tore2 == "")
			{
				$tore2 = -1;
			}	

			$query_insert = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag)
						VALUES('$mannschaft1','$mannschaft2','$tore1','$tore2','$spieldatum','$spieltag')";
			$result_insert = mysql_query($query_insert);
			header("Location: spiele.php?spieltag=$st");
		}
		
	}

	$stunde = 15;
	$minute = 30;
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

<form action="add_spiel.php" method="post">
<input type="hidden" name="action" value="add">
<input type="hidden" name="st" value="<?=$spieltag?>">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Spieltag]:</td>
<td class="txt_felf_schwarz">
	<select name="spieltag" size="1">
		<?
			for($i=1;$i<35;$i++)
			{
				if($i == $spieltag)
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
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Datum]:</td>
<td class="txt_felf_schwarz">
	<select name="tag" size="1">
		<?
			for($i=1;$i<32;$i++)
			{
				if($i == $tag)
				{
					echo "<option value=\"$i\" selected>$i</option>";
				}
				else
				{
					echo "<option value=\"$i\">$i</option>";
				}
			}
		?>
	</select>.
	<select name="monat" size="1">
		<?
			for($i=1;$i<13;$i++)
			{
				if($i == $monat)
				{
					echo "<option value=\"$i\" selected>$i</option>";
				}
				else
				{
					echo "<option value=\"$i\">$i</option>";
				}
			}
		?>
	</select>.
	<select name="jahr" size="1">
		<?
			for($i=2010;$i<2012;$i++)
			{
				if($i == 2006)
				{
					echo "<option value=\"$i\" selected>$i</option>";
				}
				else
				{
					echo "<option value=\"$i\">$i</option>";
				}
			}
		?>
	</select>&nbsp;
	<select name="stunde" size="1">
		<?
			for($i=12;$i<21;$i++)
			{
				if($i == $stunde)
				{
					echo "<option value=\"$i\" selected>$i</option>";
				}
				else
				{
					echo "<option value=\"$i\">$i</option>";
				}
			}
		?>
	</select>:
	<select name="minute" size="1">
		<?
                        for($i=0;$i<46;$i=$i+15)
                        {
                                if($i == $stunde)
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
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Spielpaarung]:</td>
<td class="txt_felf_schwarz">
	<select name="mannschaft1" size="1">
	<?
        	$query = "SELECT * FROM mannschaften ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft1 == $id)
			{	
				echo "<option value=\"$id\" selected>$name</option>";
			}
			else
			{
				echo "<option value=\"$id\">$name</option>";
			}

			$i++;
		}
	?>
	</select>&nbsp;-&nbsp; 
	<select name="mannschaft2" size="1">
	<?
        	$query = "SELECT * FROM mannschaften ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft2 == $id)
			{	
				echo "<option value=\"$id\" selected>$name</option>";
			}
			else
			{
				echo "<option value=\"$id\">$name</option>";
			}
			$i++;
		}
	?>
	</select>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Ergebnis]:</td>
<td class="txt_felf_schwarz">
	<input type="text" name="tore1" size="2" maxlength="2" value="<?=$tore1?>">&nbsp;:&nbsp;
	<input type="text" name="tore2" size="2" maxlength="2" value="<?=$tore2?>">
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




