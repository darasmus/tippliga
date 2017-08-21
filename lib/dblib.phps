<?   
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

  	// 13.12.2008 register_globals Fix
  	extract($_GET, EXTR_SKIP);
  	extract($_POST, EXTR_SKIP);
  	extract($_SERVER, EXTR_SKIP);
  	//

  	function dprint_r($var) 
	{
      echo "<pre>";
      print_r($var);
      echo "</pre>";
  	} 

	function db_connect()
        {
	        //DB-connect
    	    //include("/kunden/kult-ur-camp.de/foetchen/dbconf/dbconf.php");
								
		define ("_DEV", ($_SERVER['REMOTE_ADDR']=='127.0.0.1'));
		define ("_GASTUSER",14);

        	//Datenbank-Stuff
            if(_DEV)
        	{
           		$db_host = "localhost";
				$db_user = "root";
				$db_passwd = ""; 
				$db_name = "tippliga";           		
       		}
        	else
        	{
				$db_host = "sql81.your-server.de";
				$db_user = "dadeva_1";
				$db_passwd = "SE80zNk8c56ymEA0";
				$db_name = "dadeva_db1";         		
        	}
		                
			global $db_pro;
 	        $db_pro = mysql_connect($db_host,$db_user,$db_passwd) or die ("Verbindung zur Datenbankfehlgeschlagen");
            mysql_select_db($db_name,$db_pro);
        }

        function db_close()
        {
                global $db_pro;
                mysql_close($db_pro);
        }

		function getUserName($user)
        {
        		global $conn;

				$query = "SELECT vorname,nachname FROM spieler WHERE id=$user";
                $res = mysql_query($query);
                $all = mysql_num_rows($res);

               	if($all > 0)
               	{
               		return mysql_result($res,0,'vorname')." ".mysql_result($res,0,'nachname');
               	}
               	else
  				{
  					return false;
  				}
        }

	function calculate_all($the_user)
	{
		$back[gesamtpunkte] = "0";
		$back[dreier] = "0";
		$back[zweier] = "0";
		$back[einer] = "0";
		$back[nuller] = "0";

		$query = "SELECT * FROM spiele";
       	$result = mysql_query($query);
       	$all = mysql_num_rows($result);

       	$i=0;
       	while($i<$all)
       	{
	                  $id = mysql_result($result,$i,'id');
                      $tore1 = mysql_result($result,$i,'tore1');
                      $tore2 = mysql_result($result,$i,'tore2');

                      $query_tipp = "SELECT tipp1, tipp2 FROM tipps WHERE spieler=$the_user AND spiel=$id";
                      $result_tipp = mysql_query($query_tipp);
                      if(mysql_num_rows($result_tipp)>0)
                      {
                            $tipp1 = mysql_result($result_tipp,0,'tipp1');
                            $tipp2 = mysql_result($result_tipp,0,'tipp2');

                            if( ($tipp1 == -1) || ($tipp1 == "") )
                            {
                                 $tipp1 = "-";
                            }
                            if( ($tipp2 == -1) || ($tipp2 == "") )
                            {
                                 $tipp2 = "-";
                            }
                            $tipp = "$tipp1 : $tipp2";

                            if($tore1 == -1)
                            {
                                 $tore1 = "-";
                            }
                            if($tore2 == -1)
                            {
                                 $tore2 = "-";
                            }

                            // Punkteberechnung..
			    			$div1 = $tore1 - $tore2;
                            $div2 = $tipp1 - $tipp2;

                            if( ($tore1 != "-") && ($tore2 != "-") && ($tipp1 != "-") && ($tipp2 != "-") )
                            {
                                 if( ($tore1 == $tipp1) && ($tore2 == $tipp2) )
                                 {
                                      	$punkte = 5;
				      					$back[dreier] = $back[dreier] + 1;
                                 }
                                 elseif( ($div1 == $div2) && ($div1 != 0) )
                                 {
                                      	$punkte = 4;
				      					$back[zweier] = $back[zweier] + 1;
                                 }
                                 elseif( ($div1 == $div2) && ($div1 == 0) )
                                 {
                                      	$punkte = 4;
				      					$back[zweier] = $back[zweier] + 1;
                                 }
                                 elseif( ($div1 > 0) && ($div2 > 0) )
                                 {
                                      	$punkte = 3;
				      					$back[einer] = $back[einer] + 1;
                                 }
                                 elseif( ($div1 < 0) && ($div2 < 0) )
                                 {
                                      	$punkte = 3;
				      					$back[einer] = $back[einer] + 1;
                                 }
                                 else
                                 {
                                      	$punkte = 0;
				      					$back[nuller] = $back[nuller] + 1;
                                 }
                             }
                             else
                             {
                                 $punkte = 0;
                                 //$back[nuller] = $back[nuller] + 1;
                             }

                             $back[gesamtpunkte] = $back[gesamtpunkte] + $punkte;
                             //ENDE Punktberechnung
                      }
		      $i++;
		}
		return $back;
	}

	function generate_tabelle()
	{
		$query = "SELECT * FROM mannschaften WHERE liga=1 order by name";
		$result = mysql_query($query);
		$i = 0;
     			
		while($i<mysql_num_rows($result))
     	{
				$ges_tor_div = 0;
				$ges_punkte = 0;
				$ges_tore = 0;
		    	$ges_tore_get = 0;

				$id = mysql_result($result,$i,'id');
                $name = mysql_result($result,$i,'name');

				$query2 = "SELECT * FROM spiele WHERE (mannschaft1=$id OR mannschaft2=$id)";
				$result2 = mysql_query($query2);
				$j=0;
				while($j<mysql_num_rows($result2))
				{
					$s_id = mysql_result($result2,$j,'id');
                	$tore1 = mysql_result($result2,$j,'tore1');
					$tore2 = mysql_result($result2,$j,'tore2');
					$mannschaft1 = mysql_result($result2,$j,'mannschaft1');
					$mannschaft2 = mysql_result($result2,$j,'mannschaft2');

					if($tore1 == -1)
                	{
                   		$tore1 = "-";
                	}
                	if($tore2 == -1)
                	{
                   		$tore2 = "-";
                	}

					$tor_div = $tore1 - $tore2;

					if( ($tore1 != "-") && ($tore2 != "-") )
					{
						if( ($mannschaft1 == $id) && ($tor_div > 0) )
						{
							$punkte = 3;
							$tore = $tore1;
							$tore_get = $tore2;
						}
						elseif( ($mannschaft2 == $id) && ($tor_div < 0) )
						{
							$punkte = 3;
							$tor_div = $tor_div * -1;
							$tore = $tore2;
							$tore_get = $tore1;
						}
						elseif($tor_div == 0)
						{
							$punkte = 1;
							$tore = $tore1;
							$tore_get = $tore2;
						}
						else
						{
							$punkte = 0;
							$tore = $tore1;
							$tore_get = $tore2;
							if($tor_div > 0)
							{
								$tor_div = $tor_div * -1;
								$tore = $tore2;
								$tore_get = $tore1;
							}
						}
					}
					else
					{
						$punkte = 0;
						$tor_div = 0;
						$tore = 0;
						$tore_get = 0;
					}

					$ges_punkte = $ges_punkte + $punkte;
					$ges_tor_div = $ges_tor_div + $tor_div;
					$ges_tore = $ges_tore + $tore;
					$ges_tore_get = $ges_tore_get + $tore_get;

				    $j++;
				}
				$tab_name[$id] = $name;
				$tab_punkte[$id] = $ges_punkte;
				$tab_div[$id] = $ges_tor_div;
				$tab_tore[$id] = $ges_tore;
				$tab_tore_get[$id] = $ges_tore_get;

				$sorts=($ges_punkte*10000)+($ges_tor_div*100)+$ges_tore;
				$tab_sort[$id] = $sorts;

				$i++;
		}
		//Tabelle sortieren...
		arsort($tab_sort,SORT_NUMERIC);

		echo '<table>';
		echo '<tr>';
		echo '<th width="35">Platz</th>';
		echo '<th width="35"></th>';
		echo '<th width="330">Mannschaft</th>';
		echo '<th width="140" style="text-align:center;">Punkte</th>';
		echo '<th width="120" style="text-align:center;">Tore</th>';
		echo '</tr>';

		$c = 0;
		foreach($tab_sort as $key => $value)
		{
			$platz = $c+1;
			$platz = $platz.".";

			$the_name = $tab_name[$key];
			$the_div = $tab_div[$key];
			$the_tore = $tab_tore[$key];
			$the_tore_get = $tab_tore_get[$key];
			$the_punkte = $tab_punkte[$key];

			if( ($c == 0) || ($c == 1) )
			{
				$bgcolor="#afccff";
			}
			elseif($c == 2)
			{
				$bgcolor="#bfd6ff";
			}
			elseif( ($c == 3) || ($c == 4) )
			{
				$bgcolor="#fff4b4";
			}
			elseif($c == 15)
			{
				$bgcolor="#ffd9d9";
			}
			elseif( ($c == 16) || ($c == 17) )
			{
				$bgcolor="#ffafb2";
			}
			elseif($c%2 == 0)
			{
				$bgcolor="#EEEEEE";
			}
			else
			{
				$bgcolor="#FFFFFF";
			}
			
			echo '<tr bgcolor="'.$bgcolor.'">';
			echo '<td>'.$platz.'</td>';
			echo '<td class="center"><img src="images/vlogos/'.$key.'.gif"></td>';
			echo '<td>'.$the_name.'</td>';
			echo '<td style="text-align:center;">'.$the_punkte.'</td>';
			echo '<td style="text-align:center;">'.$the_tore.':'.$the_tore_get.' ('.$the_div.')</td>';
			echo '</tr>';

			$c++;
		}
		
		echo '</table>';
	}

	function calculateUserSpieltag($the_user,$spieltag)
	{
		$query = "SELECT * FROM spiele WHERE spieltag=$spieltag";
            	$result = mysql_query($query);
            	$all = mysql_num_rows($result);

		$back = 0;
            	$i=0;
            	while($i<$all)
            	{
                      $id = mysql_result($result,$i,'id');
                      $tore1 = mysql_result($result,$i,'tore1');
                      $tore2 = mysql_result($result,$i,'tore2');

                      $query_tipp = "SELECT tipp1, tipp2 FROM tipps WHERE spieler=$the_user AND spiel=$id";
                      $result_tipp = mysql_query($query_tipp);
                      if(mysql_num_rows($result_tipp)>0)
                      {
                            $tipp1 = mysql_result($result_tipp,0,'tipp1');
                            $tipp2 = mysql_result($result_tipp,0,'tipp2');

                            if( ($tipp1 == -1) || ($tipp1 == "") )
                            {
                                 $tipp1 = "-";
                            }
                            if( ($tipp2 == -1) || ($tipp2 == "") )
                            {
                                 $tipp2 = "-";
                            }
                            $tipp = "$tipp1 : $tipp2";

                            if($tore1 == -1)
                            {
                                 $tore1 = "-";
                            }
                            if($tore2 == -1)
                            {
                                 $tore2 = "-";
                            }

                            // Punkteberechnung..
			    $div1 = $tore1 - $tore2;
                            $div2 = $tipp1 - $tipp2;

                            if( ($tore1 != "-") && ($tore2 != "-") && ($tipp1 != "-") && ($tipp2 != "-") )
                            {
                                 if( ($tore1 == $tipp1) && ($tore2 == $tipp2) )
                                 {
                                      $punkte = 5;
                                 }
                                 elseif( ($div1 == $div2) && ($div1 != 0) )
                                 {
                                      $punkte = 4;
                                 }
                                 elseif( ($div1 == $div2) && ($div1 == 0) )
                                 {
                                      $punkte = 4;
                                 }
                                 elseif( ($div1 > 0) && ($div2 > 0) )
                                 {
                                      $punkte = 3;
                                 }
                                 elseif( ($div1 < 0) && ($div2 < 0) )
                                 {
                                      $punkte = 3;
                                 }
                                 else
                                 {
                                      $punkte = 0;
                                 }
                             }
                             else
                             {
                                 $punkte = 0;
                             }

                             $back = $back + $punkte;

                             //ENDE Punktberechnung
                      }
		      $i++;
		}
		return $back;
	}

         function getSpielerSpiel($spieler,$spiel)
         {
               $query = "SELECT * FROM spiele WHERE id=$spiel ORDER BY spieldatum";
        			$result = mysql_query($query);
                $tore1 = mysql_result($result,0,'tore1');
               $tore2 = mysql_result($result,0,'tore2');

					if( ($tore1 == "") || ($tore1 < 0) )
					{
						$tore1 = "-";
					}
					if( ($tore2 == "") || ($tore2 < 0) )
					{
						$tore2 = "-";
					}

               $query_tipp = "SELECT tipp1, tipp2 FROM tipps WHERE spieler=$spieler AND spiel=$spiel";
					$result_tipp = mysql_query($query_tipp);
					if(mysql_num_rows($result_tipp)>0)
					{
						$tipp1 = mysql_result($result_tipp,0,'tipp1');
						$tipp2 = mysql_result($result_tipp,0,'tipp2');
						$tipp = "$tipp1 : $tipp2";
					}
					elseif(time() > $tippdatum)
					{
						$tipp = "zu sp&auml;t";
						$tipp1 = "-";
						$tipp2 = "-";
					}
					else
					{
						$tipp1 = "-";
						$tipp2 = "-";
					}

                 // Punkteberechnung...
                	$div1 = $tore1 - $tore2;
                	$div2 = $tipp1 - $tipp2;

                	if( ($tore1 != "-") && ($tore2 != "-") && ($tipp1 != "-") && ($tipp2 != "-") )
                	{
                 				if( ($tore1 == $tipp1) && ($tore2 == $tipp2) )
                        	{
                         	$punkte = 5;
                        	}
                        	elseif( ($div1 == $div2) && ($div1 != 0) )
                        	{
                         	$punkte = 4;
                        	}
                        	elseif( ($div1 == $div2) && ($div1 == 0) )
                        	{
                         	$punkte = 4;
                        	}
                        	elseif( ($div1 > 0) && ($div2 > 0) )
                        	{
                         	$punkte = 3;
                        	}
                        	elseif( ($div1 < 0) && ($div2 < 0) )
                        	{
                         	$punkte = 3;
                        	}
                        	else
                        	{
                         	$punkte = 0;
                        	}
                	}
                	else
                	{
                 		$punkte = 0;
                	}

                 //ENDE Punktberechnung

                 $return['tipp1'] = $tipp1;
                 $return['tipp2'] = $tipp2;
                 $return['punkte'] = $punkte;

                 $query_u1 = "SELECT vorname,nachname,id FROM spieler WHERE id=$spieler";
                	$result_u1 = mysql_query($query_u1);
                	$u_vorname = mysql_result($result_u1,0,'vorname');
		$u_nachname = mysql_result($result_u1,0,'nachname');
		$u1_id = mysql_result($result_u1,0,'id');
		$u_1 = $u_vorname." ".$u_nachname;

                 $return['name'] = $u_1;

                 return $return;
         }

         function getSpielerTabelle($spieler,$spieltag,$liga)
         {
	 			if($liga == 2)
				{
					$liga = 2;
				}
	 
         	$query = "SELECT b.name,a.vorname,a.nachname 
         			 	 FROM spieler as a,mannschaften as b 
         			 	 WHERE a.id=$spieler AND a.mannschaft=b.id";
        				
        				$result = mysql_query($query);
         			$mannschaft = mysql_result($result,0,'name');
         		  	$vorname = mysql_result($result,0,'vorname');
                	$nachname = mysql_result($result,0,'nachname');

                	echo '<table width=130><tr style="height:73px;">';
                	echo "<th colspan=\"2\" class=\"dick\" style=\"text-align:center;padding-top:25px;\">";
                	echo "$mannschaft</th>";
                	echo "</tr>";

                 	$query = "SELECT a.* 
                 	          FROM spiele as a, mannschaften as b 
                 	          WHERE a.spieltag=$spieltag 
                 	          AND b.liga=1 
                 	          AND a.mannschaft1=b.id 
                 	          ORDER BY a.id";
						$result = mysql_query($query);
        				$all = mysql_num_rows($result);

                	$i=0;
        				while($i<$all)
        				{
                         	$tippspiele[$i] = mysql_result($result,$i,'id');
                         	$i++;
                 	}

                	$query = " SELECT a.* 
                	           FROM spiele as a, mannschaften as b 
                	           WHERE a.spieltag=$spieltag 
                	           AND b.liga=$liga 
                	           AND a.mannschaft1=b.id 
                	           ORDER BY a.spieldatum";
						$result = mysql_query($query);
        				$all = mysql_num_rows($result);

                	$i=0;
        				while($i<$all)
        				{
							$tipp = "- : -";

                		$id = mysql_result($result,$i,'id');
                		$mann1 = mysql_result($result,$i,'mannschaft1');
                		$mann2 = mysql_result($result,$i,'mannschaft2');
                		$tore1 = mysql_result($result,$i,'tore1');
                		$tore2 = mysql_result($result,$i,'tore2');
                		$spieldatum = mysql_result($result,$i,'spieldatum');
							$spieltag = mysql_result($result,$i,'spieltag');

                 		$data = getSpielerSpiel($spieler,$tippspiele[$i]);

                 		$tipp = $data['tipp1']." : ".$data['tipp2'];
                 		$punkte = $data['punkte'];
                     $mannschaft = $data['mannschaft'];

							if($i%2 == 0)
							{
								$bg = 'bgcolor="#F3F3F3"';   
							}
							else
							{
								$bg = '';
							}

                     echo "<tr ".$bg.">";
                     echo "<td style=\"text-align:center; border-right:1px solid #C2C2C2; height:38px;\">$tipp</td>";
                     echo "<td class=\"rot\" style=\"text-align:center;\">$punkte</td>";
                     echo "</tr>";

				    $ges_punkte = $ges_punkte + $punkte;
                     $i++;
                 	}

                 	echo "<tr><td class=\"dick\" style=\"text-align:center; border-right:1px solid #C2C2C2; height:25px;\">Punkte</td><td class=\"rot\" align=\"center\">$ges_punkte</td></tr></table>";
           }

         function getSpielerTabellePrint($spieler,$spieltag,$liga)
         {
	 			if($liga == 2)
				{
					$liga = 2;
				}
	 
         	$query = "SELECT b.name,a.vorname,a.nachname 
         			 	 FROM spieler as a,mannschaften as b 
         			 	 WHERE a.id=$spieler AND a.mannschaft=b.id";
        				
        				$result = mysql_query($query);
         			$mannschaft = mysql_result($result,0,'name');
         		  	$vorname = mysql_result($result,0,'vorname');
                	$nachname = mysql_result($result,0,'nachname');

                	echo '<table width=130><tr style="height:48px;">';
                	echo "<th colspan=\"2\" class=\"dick\" style=\"font-size:12px;text-align:center\">";
                	echo "$mannschaft<br>($vorname $nachname)</th>";
                	echo "</tr>";

                 	$query = "SELECT a.* 
                 	          FROM spiele as a, mannschaften as b 
                 	          WHERE a.spieltag=$spieltag 
                 	          AND b.liga=1 
                 	          AND a.mannschaft1=b.id 
                 	          ORDER BY a.id";
						$result = mysql_query($query);
        				$all = mysql_num_rows($result);

                	$i=0;
        				while($i<$all)
        				{
                         	$tippspiele[$i] = mysql_result($result,$i,'id');
                         	$i++;
                 	}

                	$query = " SELECT a.* 
                	           FROM spiele as a, mannschaften as b 
                	           WHERE a.spieltag=$spieltag 
                	           AND b.liga=$liga 
                	           AND a.mannschaft1=b.id 
                	           ORDER BY a.spieldatum";
						$result = mysql_query($query);
        				$all = mysql_num_rows($result);

                	$i=0;
        				while($i<$all)
        				{
							$tipp = "- : -";

                		$id = mysql_result($result,$i,'id');
                		$mann1 = mysql_result($result,$i,'mannschaft1');
                		$mann2 = mysql_result($result,$i,'mannschaft2');
                		$tore1 = mysql_result($result,$i,'tore1');
                		$tore2 = mysql_result($result,$i,'tore2');
                		$spieldatum = mysql_result($result,$i,'spieldatum');
							$spieltag = mysql_result($result,$i,'spieltag');

                 		$data = getSpielerSpiel($spieler,$tippspiele[$i]);

                 		$tipp = $data['tipp1']." : ".$data['tipp2'];
                 		$punkte = $data['punkte'];
                     $mannschaft = $data['mannschaft'];

							if($i%2 == 0)
							{
								$bg = 'bgcolor="#F3F3F3"';   
							}
							else
							{
								$bg = '';
							}

                     echo "<tr ".$bg." style=\"border-top:1px solid #C2C2C2;\">";
                     echo "<td style=\"text-align:center; border-right:1px solid #C2C2C2; height:38px;\">$tipp</td>";
                     echo "<td class=\"rot\" style=\"text-align:center;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
                     echo "</tr>";
                     $i++;
                 	}

                 	echo "</table>";
           }

         function getSpielerTabellePrintRaw($spieler,$spieltag,$liga)
         {
	 			if($liga == 2)
				{
					$liga = 2;
				}
	 
         		$query = "SELECT b.name,a.vorname,a.nachname 
         			 	 FROM spieler as a,mannschaften as b 
         			 	 WHERE a.id=$spieler AND a.mannschaft=b.id";
        				
        		$result = mysql_query($query);
         		$mannschaft = mysql_result($result,0,'name');
         		$vorname = mysql_result($result,0,'vorname');
                $nachname = mysql_result($result,0,'nachname');

				$return['vorname'] = $vorname;
				$return['nachname'] = $nachname;
				$return['mannschaft'] = $mannschaft;

               	$query = "SELECT a.* 
               	          FROM spiele as a, mannschaften as b 
               	          WHERE a.spieltag=$spieltag 
               	          AND b.liga=1 
               	          AND a.mannschaft1=b.id 
               	          ORDER BY a.id";
				$result = mysql_query($query);
      			$all = mysql_num_rows($result);

                $i=0;
        		while($i<$all)
        		{
                	$tippspiele[$i] = mysql_result($result,$i,'id');
                    $i++;
                }

                $query = " SELECT a.* 
                	       FROM spiele as a, mannschaften as b 
                	       WHERE a.spieltag=$spieltag 
                	       AND b.liga=$liga 
                	       AND a.mannschaft1=b.id 
                	       ORDER BY a.spieldatum";
				$result = mysql_query($query);
      			$all = mysql_num_rows($result);

                $i=0;
        		while($i<$all)
   				{
					$tipp = "-:-";

           			$id = mysql_result($result,$i,'id');
           			$mann1 = mysql_result($result,$i,'mannschaft1');
           			$mann2 = mysql_result($result,$i,'mannschaft2');
           			$tore1 = mysql_result($result,$i,'tore1');
           			$tore2 = mysql_result($result,$i,'tore2');
           			$spieldatum = mysql_result($result,$i,'spieldatum');
					$spieltag = mysql_result($result,$i,'spieltag');

            		$data = getSpielerSpiel($spieler,$tippspiele[$i]);

            		$tipp = $data['tipp1'].":".$data['tipp2'];
            		$punkte = $data['punkte'];
                	$mannschaft = $data['mannschaft'];

					$return['tipps'][$i] = $tipp;
					$return['tore1'][$i] = $data['tipp1'];
					$return['tore2'][$i] = $data['tipp2'];
                	$i++;
            	}
				return $return;
           }
           
           
           

           function getSpieltagTabelle($spieltag,$liga,$mode)
           {
	   	if($liga == 2)
		{
			$liga = 0;
		}
	   
                 $query = "SELECT * FROM spiele WHERE spieltag=$spieltag AND liga=$liga ORDER BY spieldatum";
        	 $result = mysql_query($query);
        	 $all = mysql_num_rows($result);

                 echo "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
                 echo "<tr bgcolor=\"#e7e7e7\"><td class=\"txt_felf_schwarz\">Heim</td>";
                 echo "<td class=\"txt_felf_schwarz\">Gast</td>";
                 if($mode == 1)
		 {
                 echo "<td class=\"txt_felf_schwarz\">Tipp</td>";
                 }
		 if($liga == 1)
		 {
                	  echo "<td class=\"txt_felf_schwarz\">Spiel</td><tr>";
		 }

		 $imagepfad = "images/vlogos/";

                 $i=0;
        	 while($i<$all)
        	 {
			$tipp = "- : -";

                        $id = mysql_result($result,$i,'id');
                	$mann1 = mysql_result($result,$i,'mannschaft1');
                	$mann2 = mysql_result($result,$i,'mannschaft2');
                	$tore1 = mysql_result($result,$i,'tore1');
                	$tore2 = mysql_result($result,$i,'tore2');
                	$spieldatum = mysql_result($result,$i,'spieldatum');

                        $query_u1 = "SELECT vorname,nachname,id FROM spieler WHERE mannschaft=$mann1";
                	$result_u1 = mysql_query($query_u1);
			$u1_id = mysql_result($result_u1,0,'id');
			$u_1 = $u_vorname." ".$u_nachname;
			$u_p1 = calculateUserSpieltag($u1_id,$spieltag);

                        $query_u2 = "SELECT vorname,nachname,id FROM spieler WHERE mannschaft=$mann2";
                	$result_u2 = mysql_query($query_u2);
                        $u2_id = mysql_result($result_u2,0,'id');
			$u_2 = $u_vorname." ".$u_nachname;
                        $u_p2 = calculateUserSpieltag($u2_id,$spieltag);

                        $g_tipp =  $u_p1.":".$u_p2;

                	$query_m1 = "SELECT name as mannschaft1 FROM mannschaften WHERE id=$mann1";
                	$result_m1 = mysql_query($query_m1);
                	$mannschaft1 = mysql_result($result_m1,0,'mannschaft1');
			$logo1 = $imagepfad.$mann1.".gif";

               		$query_m2 = "SELECT name as mannschaft2 FROM mannschaften WHERE id=$mann2";
               		$result_m2 = mysql_query($query_m2);
               		$mannschaft2 = mysql_result($result_m2,0,'mannschaft2');
			$logo2 = $imagepfad.$mann2.".gif";

               		if($tore1 == -1)
               		{
                       		$tore1 = "-";
               		}
               		if($tore2 == -1)
               		{
                       		$tore2 = "-";
               		}

			$ergebnis = "$tore1 : $tore2";

                         echo "<tr height=\"34\"><td class=\"txt_felf_schwarz\" align=\"center\" valign=\"middle\">";
                         echo "<img src=\"$logo1\"></td>";
                 	echo "<td class=\"txt_felf_schwarz\" align=\"center\"><img src=\"$logo2\"></td>";
                         if($mode == 1)
                         {
                         	echo "<td class=\"txt_felf_schwarz\" align=\"center\">$g_tipp</td>";
                         }
                         if($liga == 1)
			{
				echo "<td class=\"txt_felf_schwarz\" align=\"center\">$ergebnis</td><tr>";
			}
                        $i++;
                 }
                 echo "<tr><td colspan=\"4\" class=\"txt_felf_schwarz\">&nbsp;</td></tr>";
                 echo "</table>";
           }
           
           
   function calculateSpieltagUser($the_user,$spieltag)
	{
		$back[gesamtpunkte] = "0";
		$back[dreier] = "0";
		$back[zweier] = "0";
		$back[einer] = "0";
		$back[nuller] = "0";

		$query = "SELECT * FROM spiele as a, mannschaften as b WHERE a.spieltag=$spieltag AND a.mannschaft1=b.id AND b.liga=1";
  	 	$result = mysql_query($query);
      $all = mysql_num_rows($result);

      $i=0;
      while($i<$all)
      {
      	$id = mysql_result($result,$i,'id');
         $tore1 = mysql_result($result,$i,'tore1');
         $tore2 = mysql_result($result,$i,'tore2');

         $query_tipp = "SELECT tipp1, tipp2 FROM tipps WHERE spieler=$the_user AND spiel=$id";
         $result_tipp = mysql_query($query_tipp);
         
         if(mysql_num_rows($result_tipp)>0)
         {
         	$tipp1 = mysql_result($result_tipp,0,'tipp1');
            $tipp2 = mysql_result($result_tipp,0,'tipp2');

            if( ($tipp1 == -1) || ($tipp1 == "") )
            {
            	$tipp1 = "-";
            }
            if( ($tipp2 == -1) || ($tipp2 == "") )
            {
            	$tipp2 = "-";
            }
            
            $tipp = "$tipp1 : $tipp2";

            if($tore1 == -1)
            {
            	$tore1 = "-";
            }
            if($tore2 == -1)
            {
            	$tore2 = "-";
            }

            // Punkteberechnung..
			   $div1 = $tore1 - $tore2;
            $div2 = $tipp1 - $tipp2;

            if( ($tore1 != "-") && ($tore2 != "-") )
            {
            	if( ($tipp1 != "-") && ($tipp2 != "-") )
            	{
            		if( ($tore1 == $tipp1) && ($tore2 == $tipp2) )
               	{
               		$punkte = 5;
				     	 	$back[dreier] = $back[dreier] + 1;
               	}
               	elseif( ($div1 == $div2) && ($div1 != 0) )
               	{
                  	$punkte = 4;
				     		$back[zweier] = $back[zweier] + 1;
               	}
               	elseif( ($div1 == $div2) && ($div1 == 0) )
               	{
                  	$punkte = 4;
				     		$back[zweier] = $back[zweier] + 1;
               	}
               	elseif( ($div1 > 0) && ($div2 > 0) )
               	{
                  	$punkte = 3;
				      	$back[einer] = $back[einer] + 1;
               	}
               	elseif( ($div1 < 0) && ($div2 < 0) )
               	{
                  	$punkte = 3;
				      	$back[einer] = $back[einer] + 1;
               	}
               	else
               	{
                  	$punkte = 0;
                  	$back[nuller] = $back[nuller] + 1;
               	}
               }
               else
               {
               	$punkte = 0;
				      $back[nuller] = $back[nuller] + 1;
               }
            }
            else
            {
            	$punkte = 0;
            	$back[nuller] = $back[nuller] + 1;
            }

            $back[gesamtpunkte] = $back[gesamtpunkte] + $punkte;
            //ENDE Punktberechnung
          }
          else
          {
          	$back[nuller] = $back[nuller] + 1;
          }
		    $i++;
		}
		return $back;
	}

	function getUserTabPosition($liga,$docroot,$userid)
	{
		$file = $docroot."/tippliga/spieltag.txt";
  		if(file_exists($file))
  		{
  			include($file);
  		}

		$q_check = "SELECT * FROM auswertung";
		$r_check = mysql_query($q_check);		
		
		if(mysql_num_rows($r_check)==0)
		{
			$query = "SELECT 
						c.name,
						b.id,
						b.vorname,
						b.nachname
					FROM  
						spieler AS b, 
						mannschaften AS c  
					WHERE  
						c.id=b.mannschaft  
					AND 
						c.liga=$liga
					ORDER BY
						c.name";  
		}
		else		
		{
			$query = "SELECT 
						c.name,
						b.id,
						b.vorname,
						b.nachname,
						sum(a.punkte) AS punkte,
						sum(a.tore_geschossen)-sum(a.tore_bekommen) AS diff,
						sum(a.tore_geschossen) as tore_g,
						sum(a.tore_bekommen) as tore_b 
					FROM 
						auswertung AS a, 
						spieler AS b, 
						mannschaften AS c  
					WHERE 
						a.spieler=b.id  
					AND 
						c.id=b.mannschaft  
					AND 
						c.liga=$liga 
					GROUP BY 
						b.id  
					ORDER BY 
						punkte DESC,
						diff DESC,
						tore_g DESC";
		}

		$result = mysql_query($query);		
		$all = mysql_num_rows($result);
		
		for($i=0;$i<$all;$i++)
		{
			$platz = $i+1;
			$platz = $platz.".";
			
			$the_name = mysql_result($result,$i,'name');
			$the_spielername = mysql_result($result,$i,'vorname')." ".mysql_result($result,$i,'nachname');
			$the_id = mysql_result($result,$i,'id');
			
			if(mysql_num_rows($r_check)==0)
			{
				$the_div = 0;
				$the_tore = 0;
				$the_tore_get = 0;
				$the_punkte = 0;
			}
			else
			{						
				$the_div = mysql_result($result,$i,'diff');
				$the_tore = mysql_result($result,$i,'tore_g');
				$the_tore_get = mysql_result($result,$i,'tore_b');
				$the_punkte = mysql_result($result,$i,'punkte');
			}
			
			if($the_id == $userid)
			{
				echo "Name: ".$the_spielername."(".$the_name.") -> PLATZ: ".$platz."<br />";
				return $platz;
			}
			
		}
	}
	

	function generateUserTabelle($liga,$docroot)
	{
		$file = $docroot."/tippliga/spieltag.txt";
  		if(file_exists($file))
  		{
  			include($file);
  		}

		$q_check = "SELECT * FROM auswertung";
		$r_check = mysql_query($q_check);		
		
		if(mysql_num_rows($r_check)==0)
		{
			$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.id as spielerid,
						c.id as mannid
					FROM  
						spieler AS b, 
						mannschaften AS c  
					WHERE  
						c.id=b.mannschaft  
					AND 
						c.liga=$liga
					ORDER BY
						c.name";  
		}
		else		
		{
			$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.id as spielerid,
						c.id as mannid,
						sum(a.punkte) AS punkte,
						sum(a.tore_geschossen)-sum(a.tore_bekommen) AS diff,
						sum(a.tore_geschossen) as tore_g,
						sum(a.tore_bekommen) as tore_b
					FROM 
						auswertung AS a, 
						spieler AS b, 
						mannschaften AS c  
					WHERE 
						a.spieler=b.id  
					AND 
						c.id=b.mannschaft  
					AND 
						c.liga=$liga 
					GROUP BY 
						b.id  
					ORDER BY 
						punkte DESC,
						diff DESC,
						tore_g DESC";
		}

		$result = mysql_query($query);		
		$all = mysql_num_rows($result);

		echo '<table>';
		echo '<tr>';
		echo '<th colspan="2" width="50">Platz</th>';
		//echo '<th width="35"></th>';
		//echo '<th width="35"></th>';
		echo '<th colspan="2" width="345">Mannschaft (Vorname Nachname)</th>';
		echo '<th width="80" style="text-align:center;">Punkte</th>';
		echo '<th width="60" style="text-align:center;">Tor-Diff</th>';
		echo '<th width="60" style="text-align:center;">Tore+</th>';
		echo '<th width="60" style="text-align:center;">Tore-</th>';
		echo '</tr>';

		for($i=0;$i<$all;$i++)
		{
			$platz = $i+1;
			//$platz = $platz.".";

			$the_name = mysql_result($result,$i,'name');
			$the_spieler_id = mysql_result($result,$i,'spielerid');
			$the_mann_id = mysql_result($result,$i,'mannid');
			$the_spielername = mysql_result($result,$i,'vorname')." ".mysql_result($result,$i,'nachname');


      //letzte Platzierung...
      $last_spieltag = getLastSpieltag() - 1;
      $aus_platz = getPlace($the_spieler_id,$last_spieltag);
      
      if($platz > $aus_platz)
      {
        $icon = "images/down.gif";
      }
      elseif($platz < $aus_platz)
      {
        $icon = "images/up.gif";
      }
      else
      {
        $icon = "images/equal.gif";
      }

			if(mysql_num_rows($r_check)==0)
			{
				$the_div = 0;
				$the_tore = 0;
				$the_tore_get = 0;
				$the_punkte = 0;
			}
			else
			{						
				$the_div = mysql_result($result,$i,'diff');
				$the_tore = mysql_result($result,$i,'tore_g');
				$the_tore_get = mysql_result($result,$i,'tore_b');
				$the_punkte = mysql_result($result,$i,'punkte');
			}

			if($i == 0)
			{
				$bgcolor="#afccff";
			}
			elseif($i == 1)
			{
				$bgcolor="#bfd6ff";
			}
			elseif($i == 2)
			{
				$bgcolor="#cfe0ff";
			}
			elseif( ($i == 3) || ($i == 4) )
			{
				$bgcolor="#fff4b4";
			}
			//elseif( ($i == 15) && ($liga == 2 || $liga == 3) )
			//{
			//	$bgcolor="#ffafb2";
			//}
			elseif( ($i == 15) || ($i == 16) || ($i == 17) )
			{
				$bgcolor="#ffafb2";
			}
			elseif($i%2 == 0)
			{
				$bgcolor="#EEEEEE";
			}
			else
			{
				$bgcolor="#FFFFFF";
			}

			echo '<tr bgcolor="'.$bgcolor.'">';
			echo '<td><b>'.$platz.'</b></td>';
			echo '<td><a href="javascript:WinOpen(\'history.php?user='.$the_spieler_id.'\',\'tipps\',\'650\',\'420\',\'0\',\'0\')"><img src="'.$icon.'" width="15" border="0"></a></td>';
			echo '<td class="center"><a href="javascript:WinOpen(\'history.php?user='.$the_spieler_id.'\',\'tipps\',\'650\',\'420\',\'0\',\'0\')"><img src="images/vlogos/'.$the_mann_id.'.gif"></a></td>';
			echo '<td>'.$the_name.' ('.$the_spielername.')</td>';
			echo '<td style="text-align:center;">'.$the_punkte.'</td>';
			echo '<td style="text-align:center;">'.$the_div.'</td>';
			echo '<td style="text-align:center;">'.$the_tore.'</td>';
			echo '<td style="text-align:center;">'.$the_tore_get.'</td>';
			echo '</tr>';
		}
		
		echo "</table>";
	}
	
	//only the data...
	function getUserTabelleData($liga,$docroot)
	{
		$file = $docroot."/tippliga/spieltag.txt";
  		if(file_exists($file))
  		{
  			include($file);
  		}

		$q_check = "SELECT * FROM auswertung";
		$r_check = mysql_query($q_check);		
		
		if(mysql_num_rows($r_check)==0)
		{
			$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.id as spielerid,
						c.id as mannid
					FROM  
						spieler AS b, 
						mannschaften AS c  
					WHERE  
						c.id=b.mannschaft  
					AND 
						c.liga=$liga
					ORDER BY
						c.name";  
		}
		else		
		{
			$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.id as spielerid,
						c.id as mannid,
						sum(a.punkte) AS punkte,
						sum(a.tore_geschossen)-sum(a.tore_bekommen) AS diff,
						sum(a.tore_geschossen) as tore_g,
						sum(a.tore_bekommen) as tore_b
					FROM 
						auswertung AS a, 
						spieler AS b, 
						mannschaften AS c  
					WHERE 
						a.spieler=b.id  
					AND 
						c.id=b.mannschaft  
					AND 
						c.liga=$liga 
					GROUP BY 
						b.id  
					ORDER BY 
						punkte DESC,
						diff DESC,
						tore_g DESC";
		}

		$result = mysql_query($query);		
		$all = mysql_num_rows($result);

		for($i=0;$i<$all;$i++)
		{
			$platz = $i+1;

			$the_name = mysql_result($result,$i,'name');
			$the_spieler_id = mysql_result($result,$i,'spielerid');
			$the_mann_id = mysql_result($result,$i,'mannid');
			$the_spielername = mysql_result($result,$i,'vorname')." ".mysql_result($result,$i,'nachname');

		    //letzte Platzierung...
		    $last_spieltag = getLastSpieltag() - 1;
		    $aus_platz = getPlace($the_spieler_id,$last_spieltag);
      
      		if($platz > $aus_platz)
      		{
        		$icon = "images/down.gif";
      		}
      		elseif($platz < $aus_platz)
      		{
        		$icon = "images/up.gif";
      		}
      		else
      		{
        		$icon = "images/equal.gif";
      		}

			if(mysql_num_rows($r_check)==0)
			{
				$the_div = 0;
				$the_tore = 0;
				$the_tore_get = 0;
				$the_punkte = 0;
			}
			else
			{						
				$the_div = mysql_result($result,$i,'diff');
				$the_tore = mysql_result($result,$i,'tore_g');
				$the_tore_get = mysql_result($result,$i,'tore_b');
				$the_punkte = mysql_result($result,$i,'punkte');
			}

			if($i == 0)
			{
				$bgcolor="#afccff";
			}
			elseif($i == 1)
			{
				$bgcolor="#bfd6ff";
			}
			elseif($i == 2)
			{
				$bgcolor="#cfe0ff";
			}
			elseif( ($i == 3) || ($i == 4) )
			{
				$bgcolor="#fff4b4";
			}
			elseif( ($i == 15) || ($i == 16) || ($i == 17) )
			{
				$bgcolor="#ffafb2";
			}
			elseif($i%2 == 0)
			{
				$bgcolor="#EEEEEE";
			}
			else
			{
				$bgcolor="#FFFFFF";
			}

			$data[$i]['bgcolor'] = $bgcolor;
			$data[$i]['tendenzicon'] = $icon;
			$data[$i]['teamid'] = $the_mann_id;
			$data[$i]['teamname'] = utf8_encode($the_name);
			$data[$i]['playername'] = utf8_encode($the_spielername);
			$data[$i]['points'] = $the_punkte;
			$data[$i]['diff'] = $the_div;
			$data[$i]['goalsPlus'] = $the_tore;
			$data[$i]['goalsMinus'] = $the_tore_get;
		}

		return $data;
	}
	
	
	
	function generateUserTabelleSmall($liga,$docroot,$user)
	{
		$file = $docroot."/tippliga/spieltag.txt";
  		if(file_exists($file))
  		{
  			include($file);
  		}

		$q_check = "SELECT * FROM auswertung";
		$r_check = mysql_query($q_check);		
		
		if(mysql_num_rows($r_check)==0)
		{
			$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.id as myuser
					FROM  
						spieler AS b, 
						mannschaften AS c  
					WHERE  
						c.id=b.mannschaft  
					AND 
						c.liga=$liga
					ORDER BY
						c.name";  
		}
		else		
		{
			$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.id as myuser,
						sum(a.punkte) AS punkte,
						sum(a.tore_geschossen)-sum(a.tore_bekommen) AS diff,
						sum(a.tore_geschossen) as tore_g,
						sum(a.tore_bekommen) as tore_b 
					FROM 
						auswertung AS a, 
						spieler AS b, 
						mannschaften AS c  
					WHERE 
						a.spieler=b.id  
					AND 
						c.id=b.mannschaft  
					AND 
						c.liga=$liga 
					GROUP BY 
						b.id  
					ORDER BY 
						punkte DESC,
						diff DESC,
						tore_g DESC";
		}

		$result = mysql_query($query);		
		$all = mysql_num_rows($result);

		echo '<table cellpadding="0" cellspacing="0" border="0" style="margin-left:0px;">';
		echo '<tr>';
		echo '<th width="40">Platz</th>';
		echo '<th width="212">Mannschaft (Vorname Nachname)</th>';
		echo '<th width="60" style="text-align:center;">Punkte</th>';
		//echo '<th width="60" style="text-align:center;">Tor-Diff</th>';
		//echo '<th width="60" style="text-align:center;">Tore+</th>';
		//echo '<th width="60" style="text-align:center;">Tore-</th>';
		echo '</tr>';

		for($i=0;$i<$all;$i++)
		{
			$platz = $i+1;
			$platz = $platz.".";

			$the_name = mysql_result($result,$i,'name');
			$the_spielername = mysql_result($result,$i,'vorname')." ".mysql_result($result,$i,'nachname');
         $the_userid = mysql_result($result,$i,'myuser');

			if(mysql_num_rows($r_check)==0)
			{
				$the_div = 0;
				$the_tore = 0;
				$the_tore_get = 0;
				$the_punkte = 0;
			}
			else
			{						
				$the_div = mysql_result($result,$i,'diff');
				$the_tore = mysql_result($result,$i,'tore_g');
				$the_tore_get = mysql_result($result,$i,'tore_b');
				$the_punkte = mysql_result($result,$i,'punkte');
			}

			$userids[$i] = $the_userid;
			$userplatz[$i] = $platz;
			$usernames[$i] = $the_name.' ('.$the_spielername.')';
			$userpunkte[$i] = $the_punkte;
			$userdiv[$i] = $the_div;
			$usertore[$i] = $the_tore;
			$usertoreget[$i] = $the_tore_get;
			
			if($user == $the_userid)
			{
				$my_platz = $i+1;
			}			
		}
		//plaetze ausrechnen...
		if($my_platz <=4)
		{
			$start = 0;
			$ende = 7;
		}		
		elseif($my_platz >=15)
		{
			$start = 11;
			$ende = 18;
		}
		else
		{
			$start = $my_platz -4;
			$ende = $my_platz +3;
		}
		
		$cnt = 0;
		for($m=$start;$m<$ende;$m++)
		{
			$id = $userids[$m];
			$platz = $userplatz[$m];
			$name = $usernames[$m];
			$punkte = $userpunkte[$m];
			$div = $userdiv[$m];
			$tore = $usertore[$m];
			$tore_get = $usertoreget[$m];
		
			if($cnt%2 == 0)
			{
				$bgcolor="#EEEEEE";
			}
			else
			{
				$bgcolor="#FFFFFF";
			}
			
			if($id == $user)
			{
				echo '<tr bgcolor="'.$bgcolor.'" style="border: 1px solid red;">';
			}
			else
			{
				echo '<tr bgcolor="'.$bgcolor.'">';
			}
			echo '<td>'.$platz.'</td>';
			echo '<td>'.$name.'</td>';
			echo '<td style="text-align:center;">'.$punkte.' ('.$div.')</td>';
			//echo '<td style="text-align:center;">'.$div.'</td>';
			//echo '<td style="text-align:center;">'.$tore.'</td>';
			//echo '<td style="text-align:center;">'.$tore_get.'</td>';
			echo '</tr>';
			
			$cnt++;
		}

		echo "</table>";
	}

	function showGesamt()
	{
		$back[gesamtpunkte] = "0";
		$back[dreier] = "0";
		$back[zweier] = "0";
		$back[einer] = "0";
		$back[nuller] = "0";

		$query = "SELECT 
						c.name,
						b.vorname,
						b.nachname,
						b.username,
						sum(a.dreier)*5 + sum(a.zweier)*4  + sum(a.einer)*3 AS punkte,
						sum(a.dreier) as drei,
						sum(a.zweier) as zwei,
						sum(a.einer) as ein,
						sum(a.nuller) as nul
					FROM 
						auswertung AS a, 
						spieler AS b, 
						mannschaften AS c  
					WHERE 
						a.spieler=b.id  
					AND 
						c.id=b.mannschaft  
					GROUP BY 
						b.id  
					ORDER BY 
						punkte DESC";
		
       $result = mysql_query($query);
       $all = mysql_num_rows($result);
	
		echo '<table>';
		echo '<tr>';
		echo '<th width="50">Platz</th>';
		echo '<th width="330">Spielername (Vorname Nachname)</th>';
		echo '<th width="80">Gesamt</th>';
		echo '<th width="50">5er</th>';
		echo '<th width="50">4er</th>';
		echo '<th width="50">3er</th>';
		echo '<th width="50">0er</th>';
		echo '</tr>';
	
		$old_gesamtpunkte = 0;
		for($i=0;$i<$all;$i++)
		{
			$sp_vorname = mysql_result($result,$i,'vorname');
			$sp_nachname = mysql_result($result,$i,'nachname');
			$sp_username = mysql_result($result,$i,'name');
			$gesamtpunkte = $startpunkte;

			$gesamtpunkte = $gesamtpunkte + mysql_result($result,$i,'punkte');
			$dreier = mysql_result($result,$i,'drei');
			$zweier = mysql_result($result,$i,'zwei');
			$einer = mysql_result($result,$i,'ein');
			$nuller = mysql_result($result,$i,'nul');
		
			if($gesamtpunkte != $old_gesamtpunkte)
			{
				$platz = $i + 1;
				$old_gesamtpunkte = $gesamtpunkte;
			}
		
			if(strlen($platz)<2)
			{
				$platz = "0".$platz;	
			}
		
		   if($i%2 == 0)
		   {
				$bg = 'bgcolor="#F3F3F3"';   
		   }
		   else
			{
				$bg = '';
			}   
		
			echo '<tr '.$bg.'>';
			echo '<td align="center">'.$platz.'.</td>';
			echo '<td>'.$sp_username.' ('.$sp_vorname.' '.$sp_nachname.')</td>';
			echo '<td>'.$gesamtpunkte.'</td>';
			echo '<td>'.$dreier.'</td>';
			echo '<td>'.$zweier.'</td>';
			echo '<td>'.$einer.'</td>';
			echo '<td>'.$nuller.'</td>';
			echo '</tr>';	
		}
		
		echo "</table>";
		
		//Grafische Auswertung...
		
		echo '<div id="linie"></div>';
		echo '<h1>Spielstand</h1>';
		
		
		echo '<table cellpadding="0" cellspacing="2" border="0">';
		
		$old_gesamtpunkte = 0;
		for($i=0;$i<$all;$i++)
		{
			$sp_vorname = mysql_result($result,$i,'vorname');
			$sp_nachname = mysql_result($result,$i,'nachname');
			$sp_username = mysql_result($result,$i,'name');
			$gesamtpunkte = $startpunkte;

			$gesamtpunkte = $gesamtpunkte + mysql_result($result,$i,'punkte');
		
			if($i == 0)
			{
				$max = $gesamtpunkte;
			}

			if($max == 0)
			{
				$max = 1;
			}
			
			$per_value = ($gesamtpunkte/$max) * 100;
			$width = $per_value * 4.5;
			$width = $width."pt";

			if($gesamtpunkte != $old_gesamtpunkte)
			{
				$platz = $i + 1;
				$old_gesamtpunkte = $gesamtpunkte;
			}

			if(strlen($platz)<2)
			{
				$platz = "0".$platz;	
			}
			
			if($i%2 == 0)
		   {
				$bg = 'bgcolor="#F3F3F3"';   
		   }
		   else
			{
				$bg = '';
			}

			echo "<tr ".$bg."><td class=\"txt_felf_schwarz\" valign=\"middle\" width=\"10\">$platz.</td>";
			echo "<td class=\"txt_felf_schwarz\" valign=\"middle\" width=\"160\">$sp_vorname $sp_nachname</td>";
			echo "<td class=\"txt_felf_schwarz\" valign=\"middle\" align=\"left\">";
			if($gesamtpunkte > 0)
			{
				echo "<img src=\"media/pixel_graph_ende.jpg\" width=\"1\" height=\"12\" align=\"absmiddle\">";
				echo "<img src=\"media/pixel_graph.jpg\" width=\"$width\" height=\"12\" align=\"absmiddle\">";
				echo "<img src=\"media/pixel_graph_ende.jpg\" width=\"1\" height=\"12\" align=\"absmiddle\">";
				echo "&nbsp;".$gesamtpunkte;
			}
			else
			{
				echo "&nbsp;".$gesamtpunkte;
			}
			echo "</td></tr>";
		}
		echo '</table>';
	}



	     function getBeitraege()
        {
        			global $conn,$wochentage;
					echo '<table>';
					echo '<tr bgcolor="#F3F3F3">';
					echo '<th width="250">Titel</th>';
					echo '<th width="150">Autor</th>';
					echo '<th align="center" width="120" style="text-align:center;">Antworten</th>';
					echo '<th width="140">Letzter Beitrag</th>';
					echo '</tr>';

					$query = "SELECT * FROM beitraege WHERE child=0 ORDER BY datum DESC";
               $res = mysql_query($query);
               $all = mysql_num_rows($res);

  					if($all > 0)
  					{
  						for($i=0;$i<$all;$i++)
                	{
                        $id = mysql_result($res,$i,'id');
                        $titel = mysql_result($res,$i,'titel');
                        $benutzer = mysql_result($res,$i,'benutzer');
                        $datum = strftime('%d.%m.%Y - %H:%M',mysql_result($res,$i,'datum'));

                        $q_2 = "SELECT id FROM beitraege WHERE child=$id";
                        $r_2 = mysql_query($q_2);
                        $antworten = mysql_num_rows($r_2);

                        if($antworten > 0)
                        {
                        	$q_3 = "SELECT max(datum) as neues_datum FROM beitraege WHERE child=$id";
                        	$r_3 = mysql_query($q_3);
                        	$datum = strftime('%d.%m.%Y - %H:%M',mysql_result($r_3,0,'neues_datum'));
                        }

                        if($i%2 != 0)
                     	{
                     		$bgcolor = 'bgcolor="#F3F3F3"';
                     	}
                     	else
                     	{
                     		$bgcolor = '';
                     	}

                     	$link = "beitraege.php?child=$id";

                        echo '<tr '.$bgcolor.'>';
								echo '<td class="klein"><a class="kleinunddick"href="'.$link.'">'.$titel.'</a></td>';
								echo '<td class="klein">'.$benutzer.'</td>';
								echo '<td class="klein" align="center" style="text-align:center;">'.$antworten.'</td>';
								echo '<td class="klein">'.$datum.'</td>';
								echo '</tr>';
						}
					}

					echo '</table>';
			}

		  function getChildBeitraege($child)
        {
        			global $conn,$wochentage;

  					$m_query = "SELECT * FROM beitraege WHERE id=$child";
  					$m_res = mysql_query($m_query);

  					if(mysql_num_rows($m_res) > 0)
  					{
  						$m_titel = mysql_result($m_res,0,'titel');
  						$m_datum = strftime('%d.%m.%Y - %H:%M',mysql_result($m_res,0,'datum'));
  						$m_benutzer = mysql_result($m_res,0,'benutzer');
  						$m_beitrag = mysql_result($m_res,0,'beitrag');
  						$m_link = mysql_result($m_res,0,'link');
  						$m_linktext = mysql_result($m_res,0,'linktext');

  						if($m_linktext == "")
  						{
  							$m_linktext = "LINK";
  						}
  					}

				   echo '<table border="0" cellspacing="0" style="border: 1px solid #bebebe; width:660px;">';
               echo '<tr bgcolor="#bebebe"><td><b>'.$m_titel.'</b></td>';
               echo '<td style="text-align:right;">'.$m_benutzer.'</td></tr>';
               echo '<tr bgcolor="#bebebe"><td colspan="2">'.$m_datum.'</td></tr>';
               echo '<tr bgcolor="#F3F3F3"><td colspan="2" class="blau"><br>'.$m_beitrag;
               if($m_link != "")
               {
               	echo '<br><br><a href="'.$m_link.'" target="_blank">>>'.$m_linktext.'</a>';
               }
               echo '<br><br></td></tr>';
					echo '</table><br>';

					$query = "SELECT * FROM beitraege WHERE child=$child ORDER BY datum";
               $res = mysql_query($query);
               $all = mysql_num_rows($res);

  					if($all > 0)
  					{
  						for($i=0;$i<$all;$i++)
                	{
                        $id = mysql_result($res,$i,'id');
                        $titel = mysql_result($res,$i,'titel');
                        $benutzer = mysql_result($res,$i,'benutzer');
                        $beitrag = mysql_result($res,$i,'beitrag');
                        $datum = strftime('%d.%m.%Y - %H:%M',mysql_result($res,$i,'datum'));
                        $link = mysql_result($res,$i,'link');
               			$linktext = mysql_result($res,$i,'linktext');

               			if($linktext == "")
  								{
  									$linktext = "LINK";
  								}

               			echo '<table border="0" cellspacing="0" style="border: 1px solid #bebebe; width:660px;">';
                        echo '<tr bgcolor="#F3F3F3"><td><b>'.$titel.'</b></td>';
                        echo '<td style="text-align:right;">'.$benutzer.'</td></tr>';
                        echo '<tr bgcolor="#F3F3F3"><td colspan="2">'.$datum.'</td></tr>';
                        echo '<tr><td colspan="2"><br>'.$beitrag;
                        if($link != "")
               			{
               				echo '<br><br><a href="'.$link.'" target="_blank">>>'.$linktext.'</a>';
               			}
                        echo '<br><br></td></tr>';
								echo '</table><br>';
						}
					}

					echo '</table>';
			}   
			
			
		  function getBeitraegeSmall()
        {
        			global $conn,$wochentage;
					echo '<table cellpadding="0" cellspacing="0" style="margin-left:0px;">';
					echo '<tr bgcolor="#F3F3F3">';
					echo '<th width="200">Titel</th>';
					echo '<th width="120">Autor</th>';
					echo '</tr>';

					$query = "SELECT * FROM beitraege ORDER BY datum DESC LIMIT 7";
               $res = mysql_query($query);
               $all = mysql_num_rows($res);

  					if($all > 0)
  					{
  						for($i=0;$i<$all;$i++)
                	{
                        $id = mysql_result($res,$i,'id');
                        $titel = mysql_result($res,$i,'titel');
                        $benutzer = mysql_result($res,$i,'benutzer');
                        $child = mysql_result($res,$i,'child');
                        $datum = strftime('%d.%m.%Y - %H:%M',mysql_result($res,$i,'datum'));

                        $q_2 = "SELECT id FROM beitraege WHERE child=$id";
                        $r_2 = mysql_query($q_2);
                        $antworten = mysql_num_rows($r_2);

                        if($antworten > 0)
                        {
                        	$q_3 = "SELECT max(datum) as neues_datum FROM beitraege WHERE child=$id";
                        	$r_3 = mysql_query($q_3);
                        	$datum = strftime('%d.%m.%Y - %H:%M',mysql_result($r_3,0,'neues_datum'));
                        }

                        if($i%2 != 0)
                     	{
                     		$bgcolor = 'bgcolor="#F3F3F3"';
                     	}
                     	else
                     	{
                     		$bgcolor = '';
                     	}
                     	
                     	if($child == 0)
                     	{
                     		$child = $id;
                     	}

                     	$link = "beitraege.php?child=$child";

                        echo '<tr '.$bgcolor.'>';
								echo '<td class="klein"><a class="kleinunddick"href="'.$link.'">'.$titel.'</a></td>';
								echo '<td class="klein">'.$benutzer.'</td>';
								echo '</tr>';
						}
					}

					echo '</table>';
			}
			
			function generateTipptabelle()
			{
		    	global $conn;
				ini_set('display_errors', 1);
		
				for($s=1;$s<35;$s++)
				{
					$ges_punkte[$s] = 0;
					$sname = "Spieltag $s";
					$sd = strftime('%d.%m.%Y - %H:%M', getSpieldatum($s));
					
					echo '<a name="s_'.$s.'"></a>';
					
					echo '<table style="border: 1px solid #C2C2C2; width:665px; margin-bottom:0px;">';
					echo '<tr style="background-color:#DF373E;">';
					echo '<th colspan="5" style="padding-left:5px; text-align:left; color:white;">';
					echo $sname." (Tippabgabe bis: ".$sd.")</th>";
					echo '<th colspan="2" style="padding-right:5px; text-align:right; color:white;">';
					echo '<a href="#" id="down_'.$s.'"><img src="media/down.png" border="0" width="8"></a>&nbsp;';
					echo '<a href="#" id="up_'.$s.'"><img src="media/up.png" width="8" border="0"></a>&nbsp;';
					echo '<a href="#" style="color:white;">Seitenanfang</a></th>';
					echo '</tr>';
					echo '</table>';					
					
					echo '<table style="border-left: 1px solid #C2C2C2; border-right: 1px solid #C2C2C2; border-bottom: 1px solid #C2C2C2; display:none; width:665px; margin-top:0px;" id="tab_'.$s.'">';
					echo '<tr>';
					echo '<th width="130" style="border-bottom:1px solid #666666;">Datum</th>';
					echo '<th width="170" style="border-bottom:1px solid #666666;">Mannschaft 1</th>';
					echo '<th width="170" style="border-bottom:1px solid #666666;">Mannschaft 2</th>';
					echo '<th width="60" style="text-align:center;border-bottom:1px solid #666666;">Ergebnis</th>';
					echo '<th width="60" style="text-align:center;border-bottom:1px solid #666666;">Tipp</th>';
					echo '<th width="50" style="text-align:center;border-bottom:1px solid #666666;">Punkte</th>';
					echo '<th width="20" style="text-align:center;border-bottom:1px solid #666666;"></th>';
					echo '</tr>';
					
					
					$m_query = "SELECT a.* 
					            FROM spiele as a, mannschaften as b 
					            WHERE a.spieltag=$s 
					            AND a.mannschaft1=b.id 
					            AND b.liga=1 
					            ORDER BY a.spieldatum";
			  		$m_res = mysql_query($m_query);

			  		if(mysql_num_rows($m_res) > 0)
			  		{
        				for($i=0;$i<mysql_num_rows($m_res);$i++)
        				{
							$tipp = "- : -";
                			$id = mysql_result($m_res,$i,'id');
                			$mann1 = mysql_result($m_res,$i,'mannschaft1');
                			$mann2 = mysql_result($m_res,$i,'mannschaft2');
							$tore1 = mysql_result($m_res,$i,'tore1');
                			$tore2 = mysql_result($m_res,$i,'tore2');
                			$spieldatum = mysql_result($m_res,$i,'spieldatum');
							$spieltag = mysql_result($m_res,$i,'spieltag');
							
							if( ($tore1<0) || ($tore2<0) )
							{
								$tore1 = $tore2 = "-";
							}
							
							
							$query_m1 = "SELECT name as mannschaft1 FROM mannschaften WHERE id=$mann1";
                			$result_m1 = mysql_query($query_m1);
                			$mannschaft1 = mysql_result($result_m1,0,'mannschaft1');

                			$query_m2 = "SELECT name as mannschaft2 FROM mannschaften WHERE id=$mann2";
                			$result_m2 = mysql_query($query_m2);
                			$mannschaft2 = mysql_result($result_m2,0,'mannschaft2');

							$spielertipp = getSpielerSpiel($_SESSION['userid'],$id);

							$tipp1 = $spielertipp['tipp1']; 
							$tipp2 = $spielertipp['tipp2'];
							$punkte = $spielertipp['punkte'];
							$ges_punkte[$s] = $ges_punkte[$s] + $punkte;					
					
							$tippdatum = getSpieldatum($s);
							
							if($i%2 == 0)
							{
								$bg = 'bgcolor="#F3F3F3"';   
							}
							else
							{
								$bg = '';
							}   

							echo '<tr '.$bg.'>';
							echo '<td>'.strftime('%d.%m.%Y - %H:%M',$spieldatum).'</td>';
							echo '<td>'.$mannschaft1.'</td>';
							echo '<td>'.$mannschaft2.'</td>';
							echo '<td style="text-align:center;">'.$tore1.':'.$tore2.'</td>';
							echo '<td style="text-align:center;">'.$tipp1.':'.$tipp2.'</td>';								
							echo '<td class="rot">'.$punkte.'</td>';
							if( (time() > $tippdatum) && ($tippdatum > 0) )
							{
								echo '<td><a href="javascript:WinOpen(\'show_tipps.php?spiel='.$id.'\',\'tipps\',\'400\',\'600\',\'1\',\'1\')"><img src="images/liste.gif" border="0"></a></td>';
							}
							else
							{
								echo "<td>&nbsp;</td>";
							}
							echo '</tr>';
						}
					}
					
					echo '<tr>';
					echo '<td style="text-align:left;border-top:1px solid #666666;border-bottom:1px solid #666666;" class="kleinunddick" colspan="3">';
					echo 'Gesamtpunkte am '.$s.'. Spieltag';
					echo '</td>';
					echo '<td style="text-align:right;border-top:1px solid #666666;border-bottom:1px solid #666666;" class="rot" colspan="4">';
					echo $ges_punkte[$s];
					echo '</td></tr>';
					
					echo '<tr>';
					echo '<td style="text-align:left;" colspan="3" class="rot">';
					if( (time() > $tippdatum) && ($tippdatum > 0) )
					{
						echo 'Tipps einsehen ';
						echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;'.$_SESSION['usrliga'].'.Liga</span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

						if($_SESSION['usrliga'] == 1)
						{
							echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?liga=2&cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;2.Liga</span></a>&nbsp;';
							echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?liga=3&cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;3.Liga</span></a>&nbsp;';						
						}
						elseif($_SESSION['usrliga'] == 2)
						{
							echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?liga=1&cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;1.Liga</span></a>&nbsp;';
							echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?liga=3&cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;3.Liga</span></a>&nbsp;';
						}
						else
						{
							echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?liga=1&cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;1.Liga</span></a>&nbsp;';
							echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag.php?liga=2&cur_st='.$s.'\',\'tipps\',\'860\',\'340\',\'0\',\'0\')"><img src="images/lupe.gif" border="0">&nbsp;2.Liga</span></a>&nbsp;';
						}
					}
					else
					{
						echo '&nbsp;';
					}					
					echo '</td>';
					echo '<td style="text-align:right;" colspan="4">';
					echo '<a style="text-decoration:none;" href="javascript:WinOpen(\'spieltag_print.php?spieltag='.$s.'\',\'tipps\',\'475\',\'500\',\'1\',\'1\')"><img src="images/drucker.gif" border="0">&nbsp;Tippschein</a>&nbsp;&nbsp;';		
					echo '<a style="text-decoration:none;" href="tippen.php?spieltag='.$s.'"><img src="images/stift.gif" border="0">&nbsp;Tippen</a>';
					echo '</td></tr>';																	
					echo "</table>\n";				
				}
			}
			
			function generateWhoIs($liga)
			{
				global $conn;
				
				echo '<table>';
				echo '<tr><td colspan="3" class="dick">';
 				echo 'Who is Who in der '.$liga.'. Liga</td></tr>';
					
				echo '<tr>';
				echo '<th width="120">Logo</th>';
				echo '<th width="180">Mannschaft</th>';
				echo '<th width="180">Name</th>';
				echo '</tr>';
				
				$query = "SELECT a.vorname,a.nachname,a.mannschaft,b.name AS mname
							 FROM spieler as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.mannschaft=b.id
							 ORDER BY b.name";
					$result = mysql_query($query);
					$i = 0;
     				while($i<mysql_num_rows($result))
               {
               	$vorname = mysql_result($result,$i,'vorname');
						$nachname = mysql_result($result,$i,'nachname');
						$mannschaft = mysql_result($result,$i,'mannschaft');
						$mname = mysql_result($result,$i,'mname');
	
						$logo = "images/vlogos/".$mannschaft.".gif";

						if($i%2 == 0)
						{
							$bg = 'bgcolor="#F3F3F3"';   
						}
						else
						{
							$bg = '';
						}

						echo "<tr ".$bg.">";
						echo "<td class='center'><img src=\"$logo\"></td>";
						echo "<td>$mname</td>";
						echo "<td>$vorname $nachname</td>";
						echo "</tr>";

						$i++;
					}
					
					echo '</table>';
			}
			
			function generateWerGegenWen($liga,$spieltag)
			{
				global $conn;				
				
				echo '<table>';		
				echo '<tr><th colspan="6">'.$liga.'. Liga</th></tr>';		
				
				$query = "SELECT a.*
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.spieldatum";
				$result = mysql_query($query);			
				
				$i = 0;
     			while($i<mysql_num_rows($result))
            {
            	$mannschaft1 = mysql_result($result,$i,'mannschaft1');
               $mannschaft2 = mysql_result($result,$i,'mannschaft2');

               $q_m_1 = "SELECT a.vorname,a.nachname,b.name
               			 FROM spieler as a, mannschaften as b
                         WHERE b.id=$mannschaft1
                         AND a.mannschaft=b.id";
               $result_m_1 = mysql_query($q_m_1);
               
               $name1 = mysql_result($result_m_1,0,'name');
               $vorname1 = mysql_result($result_m_1,0,'vorname');
               $nachname1 = mysql_result($result_m_1,0,'nachname');

               $q_m_2 = "SELECT a.vorname,a.nachname,b.name
                  	  	 FROM spieler as a, mannschaften as b
                         WHERE b.id=$mannschaft2
                         AND a.mannschaft=b.id";
               $result_m_2 = mysql_query($q_m_2);
               
               $name2 = mysql_result($result_m_2,0,'name');
               $vorname2 = mysql_result($result_m_2,0,'vorname');
               $nachname2 = mysql_result($result_m_2,0,'nachname');

               $logo1 = "images/vlogos/".$mannschaft1.".gif";
               $logo2 = "images/vlogos/".$mannschaft2.".gif";

					if($i%2 == 0)
					{
						$bg = 'bgcolor="#F3F3F3"';   
					}
					else
					{
						$bg = '';
					}

               echo "<tr ".$bg.">";
               echo "<td width=200><b>$name1</b><br>($vorname1 $nachname1)</td>";
               echo "<td width=50 class='center'><img src=\"$logo1\"></td>";
               echo "<td width=50>&nbsp;:&nbsp;</td>";
               echo "<td width=50 class='center'><img src=\"$logo2\"></td>";
               echo '<td width=200 style="text-align:right;"><b>'.$name2.'</b><br>('.$vorname2.' '.$nachname2.')</td>';
               echo "</tr>";

               $i++;
            }
            echo "</table>";
			}
			
			function getLastSpieltag()
			{
				global $conn;
				$query = "SELECT max(spieltag) as st FROM auswertung";
				$result = mysql_query($query);
				if(mysql_num_rows($result)>0)
				{               
            	$spieltag = mysql_result($result,0,'st');
            }
            else
            {
            	$spieltag = 0;
            }				
            return $spieltag;
			}
			
			function generateSpieltagErgebnis($liga,$spieltag=1,$nodisplay=0)
			{
				global $conn;
				
				echo '<table>';
				echo '<tr style="height:48px;">';
				echo '<th style="text-align:center;">M1</th>';
				echo '<th></th>';
				echo '<th style="text-align:center;">M2</th>';
				if($liga == 1)
				{			
					echo '<th style="text-align:center;">Erg</th>';
				}
				if($nodisplay == 0)
				{
					echo '<th style="text-align:center;">Tipp</th>';
				}
				echo '</tr>';				
				
				$query = "SELECT a.*
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.id";
				$result = mysql_query($query);						
				
				$i = 0;
     			while($i<mysql_num_rows($result))
				{
					$tore1 = mysql_result($result,$i,'tore1');				
					$tore2 = mysql_result($result,$i,'tore2');				
					$mannschaft1 = mysql_result($result,$i,'mannschaft1');
               $mannschaft2 = mysql_result($result,$i,'mannschaft2');				
				
					$logo1 = "images/vlogos/".$mannschaft1.".gif";
               $logo2 = "images/vlogos/".$mannschaft2.".gif";

					$query_u1 = "SELECT vorname,nachname,id FROM spieler WHERE mannschaft=$mannschaft1";
               $result_u1 = mysql_query($query_u1);
					$u1_id = mysql_result($result_u1,0,'id');
					$u_1 = $u_vorname." ".$u_nachname;
					$u_p1 = calculateUserSpieltag($u1_id,$spieltag);

					$query_u2 = "SELECT vorname,nachname,id FROM spieler WHERE mannschaft=$mannschaft2";
               $result_u2 = mysql_query($query_u2);
					$u2_id = mysql_result($result_u2,0,'id');
					$u_2 = $u_vorname." ".$u_nachname;
					$u_p2 = calculateUserSpieltag($u2_id,$spieltag);
					
					if($i%2 == 0)
					{
						$bg = 'bgcolor="#F3F3F3"';   
					}
					else
					{
						$bg = '';
					}

               echo '<tr '.$bg.' style="height:38px;">';
               echo '<td width=30><img src="'.$logo1.'"></td>';
               echo '<td width=10>&nbsp;:&nbsp;</td>';
               echo '<td width=30><img src="'.$logo2.'"></td>';
               if($liga == 1)
               {
						echo '<td width=50 style="text-align:center;" class="dick">'.$tore1.' : '.$tore2.'</td>'; 
					}
					if($nodisplay == 0)
					{   
						echo '<td width=50 style="text-align:center;" class="rot">'.$u_p1.' : '.$u_p2.'</td>';               
					}
               echo '</tr>';

               $i++;
				}
				echo "</table>";
			}

			function generateSpieltagPrint($liga,$spieltag=1,$nodisplay=0)
			{
				global $conn;
				
				echo '<table>';
				echo '<tr style="height:48px;">';
				echo '<th style="text-align:center;">M1</th>';
				echo '<th></th>';
				echo '<th style="text-align:center;">M2</th>';
			   echo '<th style="text-align:center;border-left:1px solid #C2C2C2;">Erg</th>';
				echo '</tr>';				
				
				$query = "SELECT a.*
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.id";
				$result = mysql_query($query);						
				
				$i = 0;
     			while($i<mysql_num_rows($result))
				{
					$tore1 = mysql_result($result,$i,'tore1');				
					$tore2 = mysql_result($result,$i,'tore2');				
					$mannschaft1 = mysql_result($result,$i,'mannschaft1');
               $mannschaft2 = mysql_result($result,$i,'mannschaft2');				
				
					$logo1 = "images/vlogos/".$mannschaft1.".gif";
               $logo2 = "images/vlogos/".$mannschaft2.".gif";
					
					if($i%2 == 0)
					{
						$bg = 'bgcolor="#F3F3F3"';   
					}
					else
					{
						$bg = '';
					}

               echo '<tr '.$bg.' style="height:38px;border-top:1px solid #C2C2C2;">';
               echo '<td width=30><img src="'.$logo1.'"></td>';
               echo '<td width=10>&nbsp;:&nbsp;</td>';
               echo '<td width=30><img src="'.$logo2.'"></td>';
					echo '<td width=50 style="text-align:center;border-left:1px solid #C2C2C2;" class="dick">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>'; 
               echo '</tr>';

               $i++;
				}
				echo "</table>";
			}
			
			
			function generateSpieltagPrintRaw($liga,$spieltag=1,$nodisplay=0)
			{
				global $conn;				
				
				$query = "SELECT a.*
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.id";
				$result = mysql_query($query);						
				
				$i = 0;
     			while($i<mysql_num_rows($result))
				{
					$return[$i]['tore1'] = mysql_result($result,$i,'tore1');				
					$return[$i]['tore2'] = mysql_result($result,$i,'tore2');				
					$return[$i]['mannschaft1'] = mysql_result($result,$i,'mannschaft1');
               		$return[$i]['mannschaft2'] = mysql_result($result,$i,'mannschaft2');				
				
					$return[$i]['logo1'] = "/images/vlogos/".$return[$i]['mannschaft1'].".gif";
               		$return[$i]['logo2'] = "/images/vlogos/".$return[$i]['mannschaft2'].".gif";

               		$i++;
				}
				return $return;
			}
			
			
			function generateSpieltagErgebnisSmall($liga,$spieltag=1,$nodisplay=0)
			{
				global $conn;
				
				echo '<table cellpadding="0" cellspacing="0" style="margin-left:40px;">';
				echo '<tr>';
				echo '<th style="text-align:center;" colspan="2">Mannschaft 1 (Vorname Nachname)</th>';
				echo '<th></th>';
				echo '<th style="text-align:center;" colspan="2">Mannschaft 2 (Vorname Nachname)</th>';
				if($nodisplay == 0)
				{
					echo '<th style="text-align:center;">Tipp</th>';
				}
				echo '</tr>';				
				
				$query = "SELECT a.*
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.id";
				$result = mysql_query($query);						
				
				$i = 0;
     			while($i<mysql_num_rows($result))
				{
					$tore1 = mysql_result($result,$i,'tore1');				
					$tore2 = mysql_result($result,$i,'tore2');				
					$mannschaft1 = mysql_result($result,$i,'mannschaft1');
               $mannschaft2 = mysql_result($result,$i,'mannschaft2');				
				
					$logo1 = "images/vlogos/".$mannschaft1.".gif";
               $logo2 = "images/vlogos/".$mannschaft2.".gif";

					$query_u1 = "SELECT a.vorname,a.nachname,a.id,b.name AS mname1 
					             FROM spieler AS a,mannschaften AS b 
					             WHERE a.mannschaft=$mannschaft1
					             AND a.mannschaft=b.id";
               $result_u1 = mysql_query($query_u1);              
               
					$u1_id = mysql_result($result_u1,0,'id');
					$u_vorname = mysql_result($result_u1,0,'vorname');				
					$u_nachname = mysql_result($result_u1,0,'nachname');					
					
					$mname1 = mysql_result($result_u1,0,'mname1');
					$u_1 = $mname1." (".$u_vorname." ".$u_nachname.")";
					$u_p1 = calculateUserSpieltag($u1_id,$spieltag);

					$query_u2 = "SELECT a.vorname,a.nachname,a.id,b.name AS mname2 
					             FROM spieler AS a,mannschaften AS b 
					             WHERE a.mannschaft=$mannschaft2
					             AND a.mannschaft=b.id";
               $result_u2 = mysql_query($query_u2);
					$u2_id = mysql_result($result_u2,0,'id');
					$u_vorname = mysql_result($result_u2,0,'vorname');				
					$u_nachname = mysql_result($result_u2,0,'nachname');					
					
					$mname2 = mysql_result($result_u2,0,'mname2');
					$u_2 = $mname2." (".$u_vorname." ".$u_nachname.")";
					$u_p2 = calculateUserSpieltag($u2_id,$spieltag);
					
					if($i%2 == 0)
					{
						$bg = 'bgcolor="#F3F3F3"';   
					}
					else
					{
						$bg = '';
					}

               echo '<tr '.$bg.'>';
               echo '<td width=35><img src="'.$logo1.'"></td>';
               echo '<td width=220 style="vertical-align:middle;">'.$u_1.'</td>';
               echo '<td width=10>&nbsp;:&nbsp;</td>';
               echo '<td width=220 style="text-align:right;vertical-align:middle;">'.$u_2.'</td>';
               echo '<td width=35><img src="'.$logo2.'"></td>';

					if($nodisplay == 0)
					{   
						echo '<td width=100 style="text-align:center;" class="rot">'.$u_p1.' : '.$u_p2.'</td>';               
					}
               echo '</tr>';

               $i++;
				}
				echo "</table>";
			}
	
			function generateSpieltagErgebnisSmallSmall($liga,$spieltag=1,$nodisplay=0)
			{
				global $conn;
				
				echo '<table cellpadding="0" cellspacing="0" style="margin-left:0px;">';				
				
				if($spieltag > 0)
				{
					$query = "SELECT a.*
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=$liga
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.id";
					$result = mysql_query($query);						
				}								
				
				if($spieltag > 0)
				{
					$i = 0;
     				while($i<mysql_num_rows($result))
					{
						$tore1 = mysql_result($result,$i,'tore1');				
						$tore2 = mysql_result($result,$i,'tore2');				
						$mannschaft1 = mysql_result($result,$i,'mannschaft1');
               	$mannschaft2 = mysql_result($result,$i,'mannschaft2');				
				
						$logo1 = "images/vlogos/".$mannschaft1.".gif";
               	$logo2 = "images/vlogos/".$mannschaft2.".gif";

						$query_u1 = "SELECT a.vorname,a.nachname,a.id,b.name AS mname1 
					             FROM spieler AS a,mannschaften AS b 
					             WHERE a.mannschaft=$mannschaft1
					             AND a.mannschaft=b.id";
               	$result_u1 = mysql_query($query_u1);              
               
						$u1_id = mysql_result($result_u1,0,'id');
						$u_vorname = mysql_result($result_u1,0,'vorname');				
						$u_nachname = mysql_result($result_u1,0,'nachname');					
					
						$mname1 = mysql_result($result_u1,0,'mname1');
						$u_1 = $mname1." (".$u_vorname." ".$u_nachname.")";
						$u_p1 = calculateUserSpieltag($u1_id,$spieltag);

						$query_u2 = "SELECT a.vorname,a.nachname,a.id,b.name AS mname2 
					             FROM spieler AS a,mannschaften AS b 
					             WHERE a.mannschaft=$mannschaft2
					             AND a.mannschaft=b.id";
               	$result_u2 = mysql_query($query_u2);
						$u2_id = mysql_result($result_u2,0,'id');
						$u_vorname = mysql_result($result_u2,0,'vorname');				
						$u_nachname = mysql_result($result_u2,0,'nachname');					
					
						$mname2 = mysql_result($result_u2,0,'mname2');
						$u_2 = $mname2." (".$u_vorname." ".$u_nachname.")";
						$u_p2 = calculateUserSpieltag($u2_id,$spieltag);
					
						if($i%2 == 0)
						{
							$bg = 'bgcolor="#F3F3F3"';   
						}
						else
						{
							$bg = '';
						}

               	echo '<tr '.$bg.'>';
               	echo '<td width=125>'.$mname1.'</td>';
               	echo '<td width=10>&nbsp;:&nbsp;</td>';
               	echo '<td width=125>'.$mname2.'</td>';

						if($nodisplay == 0)
						{   
							echo '<td width=60 style="text-align:center;" class="rot">'.$u_p1.' : '.$u_p2.'</td>';               
						}
               	echo '</tr>';

               	$i++;
					}
				}
				else
				{
					echo '<tr><td class="rot">Es gibt noch keine Ergebnisse ;o)</td></tr>';
				}
				echo "</table>";
			}
			
			function getUserMannschaftOption($selected)
			{
				global $conn;				
				
				$query = "SELECT a.id,a.vorname,a.nachname,b.name
							 FROM spieler as a, mannschaften as b
							 WHERE a.mannschaft=b.id
							 ORDER BY b.liga,b.name";
				$result = mysql_query($query);						
				
				$i = 0;
     			while($i<mysql_num_rows($result))
				{
					$id = mysql_result($result,$i,'id');
					$vorname = mysql_result($result,$i,'vorname');
					$nachname = mysql_result($result,$i,'nachname');
					$name = mysql_result($result,$i,'name');					
					
					$sel = "";
					if($id == $selected)
					{
						$sel = 'selected="selected"';
					}					
					
					echo '<option value="'.$id.'" '.$sel.'>'.$name.'('.$vorname.' '.$nachname.')</option>';
					$i++;
				}
			}
			
			function getBestSpieler($spieltag,$liga)
			{
			
			    if($spieltag > 0)
				{
					global $conn;
					$query = "SELECT max(a.tore_geschossen) as punkte, b.vorname, b.nachname, c.name
							 	FROM auswertung as a, spieler as b, mannschaften as c
							 	WHERE c.liga=".$liga."
							 	AND a.spieltag=".$spieltag."
							 	AND a.spieler=b.id
							 	AND b.mannschaft=c.id
							 	GROUP BY a.id
							 	ORDER BY punkte DESC";
							 
					$result = mysql_query($query);
					$last = 0;
					$max_punkte = mysql_result($result,0,'punkte');
				
					//Punktewolke...
					echo '<div class="sdt_punkte">'.$max_punkte.'</div>';
					echo '<div class="sdt_liga_'.$liga.'">';	
					for($i=0;$i<mysql_num_rows($result);$i++)
					{
						$punkte = mysql_result($result,$i,'punkte');
						if($punkte >= $last)
						{
							echo mysql_result($result,$i,'vorname')." ".mysql_result($result,$i,'nachname')."<br />";
							$last = $punkte;
						}
					}
					echo '</div>';
				}
				else
				{
					echo '<div class="sdt_punkte">?</div>';
					echo '<div class="sdt_liga_'.$liga.'">Gibt noch keinen...</div>';
				}
			}
			
			function getSpieldatum($spieltag)
			{
				global $conn;				
				
				$query = "SELECT min(a.spieldatum) as sd
							 FROM spiele as a, mannschaften as b
							 WHERE b.liga=1
							 AND a.spieltag=$spieltag
							 AND a.mannschaft1=b.id
							 ORDER BY a.id";
				$result = mysql_query($query);
				
				if(mysql_num_rows($result)>0)
				{
					return mysql_result($result,0,'sd');
				}
			}
			
			function getPlace($spieler,$spieltag)
			{
         global $conn;
         
         if($spieltag > 0)
         {
          $query = "SELECT platz FROM auswertung WHERE spieler='".$spieler."' AND spieltag='".$spieltag."'";
          $result = mysql_query($query);
          if(mysql_num_rows($result)>0)
				  {
					 $place = mysql_result($result,0,'platz');
				  }
				 }
				 else
				 {
          $place = 0;
         }
         return $place;
      }
      
      function getPlaceHistory($spieler)
			{
         global $conn;

         $query = "SELECT platz FROM auswertung WHERE spieler='".$spieler."' ORDER BY spieltag";
         $result = mysql_query($query);
         if(mysql_num_rows($result)>0)
				 {
				    for($i=0;$i<mysql_num_rows($result);$i++)
					  {
						  $p[$i] = mysql_result($result,$i,'platz');
				    }
				 }
         return $p;
      }  
      
      function generateFanApp()
			{
		    	global $conn;
				ini_set('display_errors', 1);
		
				for($s=1;$s<35;$s++)
				{
					$ges_punkte[$s] = 0;
					$sname = "Spieltag $s";
					$sd = strftime('%d.%m.%Y - %H:%M', getSpieldatum($s));
	
					echo '<div id="content" style="width:296px;margin:0px;padding:0px;padding-bottom:10px;">';				
					echo '<a name="s_'.$s.'"></a>';
	
					echo '<table cellpadding="0" cellspacing="0" style="border: 1px solid #C2C2C2; width:296px; margin-bottom:0px;">';
					echo '<tr style="background-color:black;">';
					echo '<th style="padding-left:5px; text-align:left; color:white;">';
					echo $sname."</th>";
					echo '<th style="padding-right:5px; text-align:right; color:white;">';
					echo '<span style="width:80px;text-align:center;color:white;border:1px solid white;text-decoration:none;padding:2px;" id="up_'.$s.'">ANZEIGEN</span>&nbsp;&nbsp;';
					echo '<span style="width:80px;text-align:center;color:white;border:1px solid white;text-decoration:none;padding:2px;">Seitenanfang</span></th>';
					echo '</tr>';
					echo '</table>';					
					
					echo '<table cellpadding="0" cellspacing="0" style="width:296px; border-left: 1px solid #C2C2C2; border-right: 1px solid #C2C2C2; border-bottom: 1px solid #C2C2C2; display:none; margin-top:0px;" id="tab_'.$s.'">';			
					
					$m_query = "SELECT a.* 
					            FROM spiele as a, mannschaften as b 
					            WHERE a.spieltag=$s 
					            AND a.mannschaft1=b.id 
					            AND b.liga=1 
					            ORDER BY a.spieldatum";
			  		$m_res = mysql_query($m_query);

			  		if(mysql_num_rows($m_res) > 0)
			  		{
        				for($i=0;$i<mysql_num_rows($m_res);$i++)
        				{
							$tipp = "- : -";
                			$id = mysql_result($m_res,$i,'id');
                			$mann1 = mysql_result($m_res,$i,'mannschaft1');
                			$mann2 = mysql_result($m_res,$i,'mannschaft2');
							$tore1 = mysql_result($m_res,$i,'tore1');
                			$tore2 = mysql_result($m_res,$i,'tore2');
                			$spieldatum = mysql_result($m_res,$i,'spieldatum');
							$spieltag = mysql_result($m_res,$i,'spieltag');
							
							if( ($tore1<0) || ($tore2<0) )
							{
								$tore1 = $tore2 = "-";
							}
							
							
							$query_m1 = "SELECT name as mannschaft1 FROM mannschaften WHERE id=$mann1";
                			$result_m1 = mysql_query($query_m1);
                			$mannschaft1 = mysql_result($result_m1,0,'mannschaft1');

                			$query_m2 = "SELECT name as mannschaft2 FROM mannschaften WHERE id=$mann2";
                			$result_m2 = mysql_query($query_m2);
                			$mannschaft2 = mysql_result($result_m2,0,'mannschaft2');				
					
							$faehnchen1 = "images/vlogos/".$mann1.".gif";
                     		$faehnchen2 = "images/vlogos/".$mann2.".gif";
					
							$tippdatum = getSpieldatum($s);
							
							if($i%2 != 0)
                     		{
                     			$bgcolor = 'style="background:#ddd;"';
                     		}
                     		else
                     		{
                     			$bgcolor = 'style="background:#ccc;"';
                     		}

							//echo '<tr '.$bgcolor.'>';
							//echo '<td class="datum" colspan="4" style="text-align:center;">'.strftime('%d.%m.%Y - %H:%M',$spieldatum).'</td>';
							//echo '</tr>';
							echo '<tr '.$bgcolor.'>';
							echo '<td class="klein" style="vertical-align:middle;width:128px" align="right">'.substr($mannschaft1,0,15).'&nbsp;<img src="'.$faehnchen1.'" width="19"></td>';
							echo '<td class="klein" style="vertical-align:middle;width:10px"align="center">-</td>';
							echo '<td class="klein" style="vertical-align:middle;width:128px"><img src="'.$faehnchen2.'" width="19">&nbsp;'.substr($mannschaft2,0,15).'</td>';
							echo '<td class="klein" style="vertical-align:middle;width:30px" align="center">'.$tore1.':'.$tore2.'</td>';
							echo '</tr>';
						}
					}
																						
					echo "</table>\n";	
					echo "</div>";			
				}
				
	 function generate_fanapp_tabelle()
	 {
		$query = "SELECT * FROM mannschaften WHERE liga=1 order by name";
		$result = mysql_query($query);
		$i = 0;
     			
		while($i<mysql_num_rows($result))
     	{
				$ges_tor_div = 0;
				$ges_punkte = 0;
				$ges_tore = 0;
		    	$ges_tore_get = 0;

				$id = mysql_result($result,$i,'id');
                $name = mysql_result($result,$i,'name');

				$query2 = "SELECT * FROM spiele WHERE (mannschaft1=$id OR mannschaft2=$id)";
				$result2 = mysql_query($query2);
				$j=0;
				while($j<mysql_num_rows($result2))
				{
					$s_id = mysql_result($result2,$j,'id');
                	$tore1 = mysql_result($result2,$j,'tore1');
					$tore2 = mysql_result($result2,$j,'tore2');
					$mannschaft1 = mysql_result($result2,$j,'mannschaft1');
					$mannschaft2 = mysql_result($result2,$j,'mannschaft2');

					if($tore1 == -1)
                	{
                   		$tore1 = "-";
                	}
                	if($tore2 == -1)
                	{
                   		$tore2 = "-";
                	}

					$tor_div = $tore1 - $tore2;

					if( ($tore1 != "-") && ($tore2 != "-") )
					{
						if( ($mannschaft1 == $id) && ($tor_div > 0) )
						{
							$punkte = 3;
							$tore = $tore1;
							$tore_get = $tore2;
						}
						elseif( ($mannschaft2 == $id) && ($tor_div < 0) )
						{
							$punkte = 3;
							$tor_div = $tor_div * -1;
							$tore = $tore2;
							$tore_get = $tore1;
						}
						elseif($tor_div == 0)
						{
							$punkte = 1;
							$tore = $tore1;
							$tore_get = $tore2;
						}
						else
						{
							$punkte = 0;
							$tore = $tore1;
							$tore_get = $tore2;
							if($tor_div > 0)
							{
								$tor_div = $tor_div * -1;
								$tore = $tore2;
								$tore_get = $tore1;
							}
						}
					}
					else
					{
						$punkte = 0;
						$tor_div = 0;
						$tore = 0;
						$tore_get = 0;
					}

					$ges_punkte = $ges_punkte + $punkte;
					$ges_tor_div = $ges_tor_div + $tor_div;
					$ges_tore = $ges_tore + $tore;
					$ges_tore_get = $ges_tore_get + $tore_get;

				    $j++;
				}
				$tab_name[$id] = $name;
				$tab_punkte[$id] = $ges_punkte;
				$tab_div[$id] = $ges_tor_div;
				$tab_tore[$id] = $ges_tore;
				$tab_tore_get[$id] = $ges_tore_get;

				$sorts=($ges_punkte*10000)+($ges_tor_div*100)+$ges_tore;
				$tab_sort[$id] = $sorts;

				$i++;
		}
		//Tabelle sortieren...
		arsort($tab_sort,SORT_NUMERIC);

		echo '<table cellpadding="0" cellspacing="0" style="border:1px solid white;">';
		echo '<tr style="background:black;">';
		echo '<th width="30" style="text-align:center;">Plz.</th>';
		echo '<th width="40"></th>';
		echo '<th width="134">Mannschaft</th>';
		echo '<th width="10" style="text-align:center;">Punkte</th>';
		echo '<th width="50" style="text-align:center;">Tore</th>';
		echo '</tr>';

		$c = 0;
		foreach($tab_sort as $key => $value)
		{
			$platz = $c+1;
			$platz = $platz.".";

			$the_name = $tab_name[$key];
			$the_div = $tab_div[$key];
			$the_tore = $tab_tore[$key];
			$the_tore_get = $tab_tore_get[$key];
			$the_punkte = $tab_punkte[$key];

			if( ($c == 0) || ($c == 1) )
			{
				$bgcolor="#afccff";
			}
			elseif($c == 2)
			{
				$bgcolor="#bfd6ff";
			}
			elseif( ($c == 3) || ($c == 4) )
			{
				$bgcolor="#fff4b4";
			}
			elseif($c == 15)
			{
				$bgcolor="#ffd9d9";
			}
			elseif( ($c == 16) || ($c == 17) )
			{
				$bgcolor="#ffafb2";
			}
			elseif($c%2 == 0)
			{
				$bgcolor="#EEEEEE";
			}
			else
			{
				$bgcolor="#FFFFFF";
			}
			
			echo '<tr bgcolor="'.$bgcolor.'">';
			echo '<td style="text-align:center;"><b>'.$platz.'</b></td>';
			echo '<td style="text-align:center;"><img src="images/vlogos/'.$key.'.gif"></td>';
			echo '<td>'.$the_name.'</td>';
			echo '<td style="text-align:center;"><b style="color:red;">'.$the_punkte.'</b></td>';
			echo '<td style="text-align:center;">'.$the_tore.':'.$the_tore_get.' ('.$the_div.')</td>';
			echo '</tr>';

			$c++;
		}
		
		echo '</table>';
	}
				
			}     
			
			
			function calPoints($tore1,$tore2,$tipp1,$tipp2)
			{
				$div1 = $tore1 - $tore2;
                $div2 = $tipp1 - $tipp2;
                
                //echo $tore1." - ".$tore2." - ".$tipp1." - ".$tipp2;

                 if( ($tore1 != "") && ($tore2 != "") && ($tore1 != "-1") && ($tore2 != "-1") && ($tipp1 !== "-1") && ($tipp2 !== "-1") && ($tipp1 != "-") && ($tipp2 != "-") )
                 {
                      if( ($tore1 == $tipp1) && ($tore2 == $tipp2) )
                      {
                           	$punkte = 5;
                      }
                      elseif( ($div1 == $div2) && ($div1 != 0) )
                      {
                           	$punkte = 4;
                      }
                      elseif( ($div1 == $div2) && ($div1 == 0) )
                      {
                           	$punkte = 4;
                      }
                      elseif( ($div1 > 0) && ($div2 > 0) )
                      {
                           	$punkte = 3;
                      }
                      elseif( ($div1 < 0) && ($div2 < 0) )
                      {
                           	$punkte = 3;
                      }
                      else
                      {
                           	$punkte = 0;
                      }
                  }
                  else
                  {
                      $punkte = 0;
                  }	
                  
                  return $punkte;
			}     
?>