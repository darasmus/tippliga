<?
	include("../lib/dblib.phps");
        db_connect();

	$error = array();
	$gruppen = array('A','B','C','D');

	if($action == "add")
	{
		if($nachname == "")
		{
			$error[0] = "Bitte einen Nachnamen eingeben!!!";
		}
		if($vorname == "")
		{
			$error[1] = "Bitte einen Vornamen eingeben!!!";
		}
		if($username == "")
		{
			$error[2] = "Bitte einen Usernamen eingeben!!!";
		}
		else
		{
			$query_user = "SELECT * FROM spieler WHERE username='$username'";
			$result_user = mysql_query($query_user);
			$exist_user = mysql_num_rows($result_user);
			if($exist_user > 0)
			{
				$error[2] = "Der Username existiert schon. Bitte einen anderen eingeben!!!";
			}
		}
		
		if($email == "")
		{
			$error[3] = "Bitte eine E-Mail-Adresse eingeben!!!";
		}
		elseif((!eregi("^[_\.0-9a-z-]+@([0-9a-z<F6><E4><FC>][0-9a-z<F6><E4><FC>-]+\.)+[a-z]{2,3}$",$email)))
		{
			$error[3] = "Bitte eine g&uuml;ltige E-Mail-Adresse eingeben!!!";
		}
		if($passwort == "")
		{
			$error[4] = "Bitte ein Passwort eingeben!!!";
		}

		if(count($error)==0)
		{
			$query_insert = "INSERT INTO spieler(nachname,vorname,username,passwort,email,startpunkte,mannschaft)
						VALUES('$nachname','$vorname','$username','$passwort','$email','$startpunkte','$mannschaft')";
			$result_insert = mysql_query($query_insert);
			header("Location: spieler.php");
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

<form action="add_spieler.php" method="post">
<input type="hidden" name="action" value="add">

<table cellpadding="2">

<tr>
<td class="txt_felf_schwarz">[Nachname]:</td>
<td class="txt_felf_schwarz"><input type="text" name="nachname" value="<?=$nachname?>" size="20"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Vorname]:</td>
<td class="txt_felf_schwarz"><input type="text" name="vorname" value="<?=$vorname?>" size="20"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[StartPunkte]:</td>
<td class="txt_felf_schwarz"><input type="text" name="startpunkte" value="<?=$startpunkte?>" size="4"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Mannschaft]:</td>
<td class="txt_felf_schwarz">
        <select name="mannschaft" size="1">
        <?
                $query = "SELECT * FROM mannschaften ORDER BY liga,name";
                $result = mysql_query($query);
                $all = mysql_num_rows($result); 

                $i=0;
                while($i<$all)
                {
                        $id = mysql_result($result,$i,'id');
                        $name = mysql_result($result,$i,'name');
						$liga = mysql_result($result,$i,'liga');
        
                        if($mannschaft == $id)
                        {       
                                echo "<option value=\"$id\" selected>$name ($liga. Liga)</option>";
                        }
                        else
                        {
                                echo "<option value=\"$id\">$name ($liga. Liga)</option>";
                        }

                        $i++;
                }
        ?>
</td>
</tr>


<tr>
<td class="txt_felf_schwarz">[Username]:</td>
<td class="txt_felf_schwarz"><input type="text" name="username" value="<?=$username?>" size="20"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[E-Mail-Adresse]:</td>
<td class="txt_felf_schwarz"><input type="text" name="email" value="<?=$email?>" size="20"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Passwort]:</td>
<td class="txt_felf_schwarz"><input type="password" name="passwort" value="<?=$passwort?>" size="20"></td>
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




