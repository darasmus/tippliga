<?
	include("../lib/dblib.phps");
   	db_connect();

	if($action == "edit")
	{
		$error = array();
		
		$spieldatum = mktime ($stunde,$minute,0,$monat,$tag,$jahr);	

		foreach($mannschaft1 as $key => $value)
		{		
			$m1 = $mannschaft1[$key];
			$m2 = $mannschaft2[$key];
			$t1 = $tore1[$key];
			$t2 = $tore2[$key];			
			$id = $spiel[$key];			
			
			if($key < 10)
			{
				$liga1_spiele[$key][mannschaft1] = $m1;
				$liga1_spiele[$key][mannschaft2] = $m2;
				$liga1_spiele[$key][tore1] = $t1;
				$liga1_spiele[$key][tore2] = $t2;
				$liga1_spiele[$key][id] = $id;
			}
			elseif($key < 19)
			{
				$liga2_spiele[$key][mannschaft1] = $m1;
				$liga2_spiele[$key][mannschaft2] = $m2;
				$liga2_spiele[$key][tore1] = $t1;
				$liga2_spiele[$key][tore2] = $t2;
				$liga2_spiele[$key][id] = $id;
			}	
			else
			{
				$liga3_spiele[$key][mannschaft1] = $m1;
				$liga3_spiele[$key][mannschaft2] = $m2;
				$liga3_spiele[$key][tore1] = $t1;
				$liga3_spiele[$key][tore2] = $t2;
				$liga3_spiele[$key][id] = $id;
			}			
			
			/*
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
			*/		
		
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
				$query = "";			
			
				//echo $mannschaft1[$key]." - ".$mannschaft2[$key]." -> ".$tore1[$key].":".$tore2[$key]."<br>";

				if($id > 0)
				{
					$query = "UPDATE spiele SET 	mannschaft1='$m1',
									mannschaft2='$m2',
									tore1='$t1',
									tore2='$t2',
									spieldatum='$spieldatum',
									spieltag='$spieltag'
								WHERE id='$id'";
				}
				else
				{
					$query = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag)
						  VALUES('$m1','$m2','$t1','$t2','$spieldatum','$spieltag')";
				}								
								
				$result_insert = mysql_query($query);
				echo "Q_: ".$query."<br>";				
			}
		}	
	}



	//1.liga - Spiele suchen...
	$query_a = "	SELECT a.*,b.liga 
			FROM spiele as a, mannschaften as b 
			WHERE a.spieltag=$spieltag 
			AND a.mannschaft1=b.id 
			AND b.liga=1
			ORDER BY a.id";
		
	$result = mysql_query($query_a);
   	$all = mysql_num_rows($result);
		
	$s = 1;		
	while ($row = mysql_fetch_assoc($result)) 
	{
		$liga1_spiele[$s] = $row;
		$s++;
	} 	
				 
				 //echo "C1:  ".count($liga1_spiele)."<br />";
				 
	//2.liga - Spiele suchen...
	$query_b = "	SELECT a.*,b.liga 
			FROM spiele as a, mannschaften as b 
			WHERE a.spieltag=$spieltag 
			AND a.mannschaft1=b.id 
			AND b.liga=2
			ORDER BY a.id";
		
	$result = mysql_query($query_b);
      	$all = mysql_num_rows($result);
				
	while ($row = mysql_fetch_assoc($result)) 
	{
		$liga2_spiele[$s] = $row;
		$s++;
	}
	    //echo "C1:  ".count($liga2_spiele)."<br />";
	//3.liga - Spiele suchen...
	$query_c = "	SELECT a.*,b.liga 
			FROM spiele as a, mannschaften as b 
			WHERE a.spieltag=$spieltag 
			AND a.mannschaft1=b.id 
			AND b.liga=3
			ORDER BY a.id";
		
	$result = mysql_query($query_c);
      	$all = mysql_num_rows($result);
				
	while ($row = mysql_fetch_assoc($result)) 
	{
		$liga3_spiele[$s] = $row;
		$s++;
	}
	
	//echo "C1:  ".count($liga3_spiele)."<br />";


	//spieldatum...
	$spieltermin = getdate($liga1_spiele[1][spieldatum]);
	$tag = $spieltermin[mday];
	$monat = $spieltermin[mon];
	$jahr = $spieltermin[year];
	$stunde = $spieltermin[hours];
	$minute = $spieltermin[minutes];						 

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

<form action="edit_spieltag.php" method="post">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="spieltag" value="<?=$spieltag?>">

<table cellpadding="2">

<tr>
	<td colspan="3"><b>[Spieltagdaten]</b></td>
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
	<td colspan="3"><b>[Spielpaarungen 1.Liga]</b></td>
</tr>

<? 
   //1.Liga
	for($p=1;$p<10;$p++)
	{
?>

<input type="hidden" name="spiel[<?=$p?>]" value="<?=$liga1_spiele[$p][id]?>">

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
	
			if($liga1_spiele[$p][mannschaft1] == $id)
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
	
				if($liga1_spiele[$p][mannschaft2] == $id)
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
	<input type="text" name="tore1[<?=$p?>]" size="2" maxlength="2" value="<?=$liga1_spiele[$p][tore1]?>">&nbsp;:&nbsp;
	<input type="text" name="tore2[<?=$p?>]" size="2" maxlength="2" value="<?=$liga1_spiele[$p][tore2]?>">
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

<input type="hidden" name="spiel[<?=$p?>]" value="<?=$liga2_spiele[$p][id]?>">

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
	
			if($liga2_spiele[$p][mannschaft1] == $id)
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
	
			if($liga2_spiele[$p][mannschaft2] == $id)
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
	<input type="text" name="tore1[<?=$p?>]" size="2" maxlength="2" value="<?=$liga2_spiele[$p][tore1]?>">&nbsp;:&nbsp;
	<input type="text" name="tore2[<?=$p?>]" size="2" maxlength="2" value="<?=$liga2_spiele[$p][tore1]?>">
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

<input type="hidden" name="spiel[<?=$p?>]" value="<?=$liga3_spiele[$p][id]?>">

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
	
			if($liga3_spiele[$p][mannschaft1] == $id)
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
	
			if($liga3_spiele[$p][mannschaft2] == $id)
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
	<input type="text" name="tore1[<?=$p?>]" size="2" maxlength="2" value="<?=$liga3_spiele[$p][tore1]?>">&nbsp;:&nbsp;
	<input type="text" name="tore2[<?=$p?>]" size="2" maxlength="2" value="<?=$liga3_spiele[$p][tore1]?>">
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




