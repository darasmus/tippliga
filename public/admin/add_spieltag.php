<?
	include("../lib/dblib.phps");
   	db_connect();

	header("Content-Type: text/html; charset=utf-8");

	if($action == "add")
	{
		$error = array();
		
		$spieldatum = mktime ($stunde,$minute,0,$monat,$tag,$jahr);
        	$spieldatum2 = mktime ($stunde2,$minute2,0,$monat2,$tag2,$jahr2);		

		$spieltag2 = $spieltag + 17;

		//erstmal aufraeumen...
		$del_query = "DELETE FROM spiele WHERE (spieltag=$spieltag OR spieltag=$spieltag2)";
		$del_result = mysql_query($del_query);	
	
		foreach($mannschaft1 as $key => $value)
		{		
			$m1 = $mannschaft1[$key];
			$m2 = $mannschaft2[$key];
			$t1 = $tore1[$key];
			$t2 = $tore2[$key];
						
			if($m1 == $m2)
			{
				$error[$key] = "Eine Mannschaft kann nicht gegen sich selbst spielen!!!";
			}		
			
			if( ($t1 != "") && !(ereg("^[0-9]{1,2}$",$t1))  )
			{
				$error[$key] = "Tore bitte als Zahl angeben!!!";
			}
			if( ($t2 != "") && !(ereg("^[0-9]{1,2}$",$t2))  )
			{
				$error[$key] = "Tore bitte als Zahl angeben!!!";
			}				
		
			if($t1 == "")
			{
				$t1 = -1;
			}
			if($t2 == "")
			{
				$t2 = -1;
			}
			
			if(count($error)==0)
			{
				$query_insert1 = $query_insert2 = "";			
			
				//echo $mannschaft1[$key]." - ".$mannschaft2[$key]." -> ".$tore1[$key].":".$tore2[$key]."<br>";
				$query_insert1 = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag)
										VALUES('$m1','$m2','$t1','$t2','$spieldatum','$spieltag')";
				$result_insert = mysql_query($query_insert1);
				//echo "Q_: ".$query_insert1."<br>";				
				
				//echo $mannschaft1[$key]." - ".$mannschaft2[$key]." -> ".$tore1[$key].":".$tore2[$key]."<br>";
				$query_insert2 = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag)
									   VALUES('$m2','$m1','-1','-1','$spieldatum2','$spieltag2')";
				$result_insert = mysql_query($query_insert2);
				//echo "Q_: ".$query_insert2."<br>";			
			}
		}	
	}
	else
	{
		$stunde = $stunde2 = 15;
		$minute = $minute2 = 30;
		$jahr = $jahr2 = 2010;
	}

?>

<html>
<head>
  <title>Administration TippLIGA</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<? if(count($error)>0): ?>
<table cellpadding="2">
<tr>
<td><b>[Fehler]</b></td>
</tr>
<tr>
<td style="color:#df0000;font-weight:bold;font-size:11px;">
<? 
	foreach ($error as $key => $value)
	{
		echo "Paarung $key: $value<br>";
	}
?>
</td>
</tr>
</table>
<? endif; ?>

<form action="add_spieltag.php" method="post">
<input type="hidden" name="action" value="add">

<table cellpadding="2">

<tr>
	<td colspan="3"><b>[Spieltagdaten]</b></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Spieltag]:</td>
<td class="txt_felf_schwarz">
	<select name="spieltag" size="1">
		<?
			for($i=1;$i<18;$i++)
			{
				$j = 17 + $i;
				if($i == $spieltag)
				{
					echo "<option value=\"$i\" selected>$i / $j</option>";
				}
				else
				{
					echo "<option value=\"$i\">$i / $j</option>";
				}
			}
		?>
	</select>
</td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Datum Hinspiel]:</td>
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
			for($i=2015;$i<2018;$i++)
			{
				if($i == $jahr)
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
                                if($i == $minute)
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
<td class="txt_felf_schwarz">[Datum R&uuml;ckspiel]:</td>
<td class="txt_felf_schwarz">
	<select name="tag2" size="1">
		<?
			for($i=1;$i<32;$i++)
			{
				if($i == $tag2)
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
	<select name="monat2" size="1">
		<?
			for($i=1;$i<13;$i++)
			{
				if($i == $monat2)
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
	<select name="jahr2" size="1">
		<?
			for($i=2015;$i<2018;$i++)
			{
				if($i == $jahr2)
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
	<select name="stunde2" size="1">
		<?
			for($i=12;$i<21;$i++)
			{
				if($i == $stunde2)
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
	<select name="minute2" size="1">
		<?
                  	for($i=0;$i<46;$i=$i+15)
			{
                                if($i == $minute2)
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
	<td colspan="3"><b>[Spielpaarungen 1.Liga]</b></td>
</tr>

<? 
   //1.Liga
	for($p=1;$p<10;$p++)
	{
?>

<tr>
<td class="txt_felf_schwarz">[Spielpaarung <?=$p?>]:</td>
<td class="txt_felf_schwarz">
	<select name="mannschaft1[<?=$p?>]" size="1">
	<?
        	$query = "SELECT * FROM mannschaften WHERE liga=1 ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft1[$p] == $id)
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
	<select name="mannschaft2[<?=$p?>]" size="1">
	<?
        	$query = "SELECT * FROM mannschaften WHERE liga=1 ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft2[$p] == $id)
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
<td class="txt_felf_schwarz">
	<input type="text" name="tore1[<?=$p?>]" size="2" maxlength="2" value="<?=$tore1[$p]?>">&nbsp;:&nbsp;
	<input type="text" name="tore2[<?=$p?>]" size="2" maxlength="2" value="<?=$tore2[$p]?>">
</td>
</tr>

<?
	}
?>

<tr>
	<td colspan="3"><b>[Spielpaarungen 2.Liga]</b></td>
</tr>

<? 
   //2.Liga
	for($p=10;$p<19;$p++)
	{
?>

<tr>
<td class="txt_felf_schwarz">[Spielpaarung <?=$p?>]:</td>
<td class="txt_felf_schwarz">
	<select name="mannschaft1[<?=$p?>]" size="1">
	<?
        	$query = "SELECT * FROM mannschaften WHERE liga=2 ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft1[$p] == $id)
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
	<select name="mannschaft2[<?=$p?>]" size="1">
	<?
        	$query = "SELECT * FROM mannschaften WHERE liga=2 ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft2[$p] == $id)
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
<td class="txt_felf_schwarz">
	<input type="text" name="tore1[<?=$p?>]" size="2" maxlength="2" value="<?=$tore1[$p]?>">&nbsp;:&nbsp;
	<input type="text" name="tore2[<?=$p?>]" size="2" maxlength="2" value="<?=$tore2[$p]?>">
</td>
</tr>

<?
	}
?>

<tr>
	<td colspan="3"><b>[Spielpaarungen 3.Liga]</b></td>
</tr>

<? 
   //3.Liga
	for($p=19;$p<28;$p++)
	{
?>

<tr>
<td class="txt_felf_schwarz">[Spielpaarung <?=$p?>]:</td>
<td class="txt_felf_schwarz">
	<select name="mannschaft1[<?=$p?>]" size="1">
	<?
        	$query = "SELECT * FROM mannschaften WHERE liga=3 ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft1[$p] == $id)
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
	<select name="mannschaft2[<?=$p?>]" size="1">
	<?
        	$query = "SELECT * FROM mannschaften WHERE liga=3 ORDER BY name";
        	$result = mysql_query($query);
        	$all = mysql_num_rows($result); 

       	 	$i=0;
        	while($i<$all)
        	{
                	$id = mysql_result($result,$i,'id');
                	$name = mysql_result($result,$i,'name');
	
			if($mannschaft2[$p] == $id)
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
<td class="txt_felf_schwarz">
	<input type="text" name="tore1[<?=$p?>]" size="2" maxlength="2" value="<?=$tore1[$p]?>">&nbsp;:&nbsp;
	<input type="text" name="tore2[<?=$p?>]" size="2" maxlength="2" value="<?=$tore2[$p]?>">
</td>
</tr>

<?
	}
?>

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




