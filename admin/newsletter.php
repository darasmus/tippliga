<?
	include("../lib/dblib.phps");
	db_connect();

	if($action == "send")
	{
		$query = "SELECT * FROM spieler ORDER BY nachname";
        $result = mysql_query($query);
        $all = mysql_num_rows($result);

		$i=0;
        
        while($i<$all)
        {
			$sendtext = "";
            $id = mysql_result($result,$i,'id');
            $nachname = mysql_result($result,$i,'nachname');
            $vorname = mysql_result($result,$i,'vorname');
			$email = mysql_result($result,$i,'email');

			//send mail
			$sendtext = "Hallo $vorname,\n\n".$text;
			$from = "jens.fot@nord-com.de";
			$to = $email;

			echo "TO: '$email'<br>";

            $mail_header = "From: ".$from." <".$from.">\r\n";
            $mail_header .= "Reply-To: ".$from."\r\n";
            $mail_header .= "MIME-Version: 1.0\r\n";
            $mail_header .= "Content-Type: text/plain;\r\n\r\n";

            //mail verschicken
            mail($to,$subject,$sendtext,$mail_header);

			$i++;
		}
	}

	db_close();
?>

<html>
<head>
  <title>Newsletter</title>
  <link rel="STYLESHEET" type="text/css" href="../formate.css">
</head>

<body bgcolor="#eeeeee">

<table cellpadding="2">

<form action="newsletter.php" name="nl" method="post">
<input type="hidden" name="action" value="send">

</table>

<table cellpadding="2" border=1 bordercolor="#000000">

	<tr>
	<td class="txt_felf_schwarz">[FROM]</td>
	<td class="txt_felf_schwarz">jens.fot@nord-com.de</td>
	</tr>

<tr>
<td class="txt_felf_schwarz">[Subject]</td>
<td class="txt_felf_schwarz"><input type="text" name="subject" value="<?=$subject?>" style="width: 500px;"></td>
</tr>

<tr>
<td class="txt_felf_schwarz">[Text]</td>
<td class="txt_felf_schwarz"><textarea name="text" cols="40" rows="20" style="width: 500px;"></textarea></td>
</tr>

<tr>
<td colspan="2" class="txt_felf_schwarz"><input type="submit" value="Abschicken"></td>
</tr>

</form>

</table>

</body>
</html>
