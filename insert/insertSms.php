<?php

include '../DatabaseConfig.php' ;

$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

$spam_words = file('../ressource/bad-words.txt', FILE_IGNORE_NEW_LINES);
$warnig="0";

function contains($str, array $arr) {
    foreach($arr as $a) {
        if (stripos($str,$a) !== false) {
          return true;
        }
    }
    return false;
}

$key = $_POST['key'];
$address = $_POST['address'];
$message = $_POST['message'];
$date = $_POST['date'];
$action=$_POST['action'];

if (contains($message,$spam_words)) $warning="1";

if ($stmt = $con->prepare('INSERT INTO sms (idUser,date,action,address,message,warning) values (?,?,?,?,?,?)')){
	$stmt->bind_param('ssssss',$key,$date,$action,$address,$message,$warning);
	$stmt->execute();
  $stmt->close();
}
?>
