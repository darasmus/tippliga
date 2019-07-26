<?php
require('session.inc.php');
require('api.lib.php');

$data = json_decode($_POST['dta']);

$datum = time();
$benutzer = $_SESSION["user"]["vorname"]." ".$_SESSION["user"]["nachname"];

$title = htmlspecialchars($data->title, ENT_QUOTES);

$beitrag = htmlspecialchars($data->message, ENT_QUOTES);
$beitrag = nl2br($beitrag);

$link = htmlspecialchars($data->link, ENT_QUOTES);
$linktext = htmlspecialchars($data->linktext, ENT_QUOTES);

if (strpos($link, 'http') !== 0) {
    $link = 'http://'.$link;
}

if($data->child == "") {
    $child = 0;
} else {
    $child = $data->child;
}

$query = 'INSERT INTO beitraege(titel,beitrag,benutzer,datum,child,link,linktext)
          VALUES("'.$title.'","'.$beitrag.'","'.$benutzer.'","'.$datum.'","'.$child.'","'.$link.'","'.$linktext.'")';

$insert = mysqli_query($db, $query);

$return = array("msg" => "done");
$json = json_encode($return);
print_r($json);

//close the db connection
mysqli_close($db);
