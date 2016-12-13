<?
	include("../lib/dblib.phps");
   db_connect();

	for($i=1;$i<18;$i++)
	{
				$q = "SELECT * FROM spiele WHERE spieltag=$i ORDER BY mannschaft1";
				$result = mysql_query($q);
        		$all = mysql_num_rows($result);				
				
				for($j=0;$j<mysql_num_rows($result);$j++)
				{
					$m1 = mysql_result($result,$j,'mannschaft1');
					$m2 = mysql_result($result,$j,'mannschaft2');
					$spieltag = $i+17;
					$tore1 = -1;
					$tore2 = -1;
					$spieldatum = 1199212200;			
				
					//echo $mannschaft1[$key]." - ".$mannschaft2[$key]." -> ".$tore1[$key].":".$tore2[$key]."<br>";
					$query_insert2 = "INSERT INTO spiele(mannschaft1,mannschaft2,tore1,tore2,spieldatum,spieltag)
									   VALUES('$m2','$m1','-1','-1','$spieldatum','$spieltag')";
					//$result_insert = mysql_query($query_insert2);
					echo "Q_: ".$query_insert2."<br>";
				}
				echo "<br>";
	}
?>