<?php

include '../DatabaseConfig.php' ;
include 'insertNotifications.php';

$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

$spam_words = file('../ressource/bad-words.txt', FILE_IGNORE_NEW_LINES);
$warning="0";

function contains($str, array $arr) {
  foreach($arr as $a) {
      if (stripos($str,$a) !== false) {

        $index=stripos($str,$a)+strlen($a);
        if (stripos($str,$a)!=0) {
          if (ctype_alpha($str[stripos($str,$a)-1]))  return false;
        }

        if (isset($str[$index])) {
          if (ctype_alpha($str[$index])){
            if (isset($str[$index+2])) {
              return substr($str,$index,3)=="ing" OR substr($str,$index,2)=="ed" OR substr($str,$index,2)=="s " OR substr($str,$index,2)=="es";
            }
            elseif (isset($str[$index+1])) {
              return substr($str,$index,2)=="ed" OR substr($str,$index,2)=="s " OR substr($str,$index,2)=="es";
            }
            else return substr($str,$index,1)=="s";
          }
          else return true;
        }
        else return true;
      }
  }
  return false;
}

$key = $_POST['key'];
$address = $_POST['address'];
$message = $_POST['message'];
$date = $_POST['date'];
$action= $_POST['action'];

if (contains($message,$spam_words)) {
  $warning="1";
  insertSmsNotification($key,$address,$message,$action);
}

if ($stmt = $con->prepare('INSERT INTO sms (idUser,date,action,address,message,warning) values (?,?,?,?,?,?)')){
	$stmt->bind_param('ssssss',$key,$date,$action,$address,$message,$warning);
	$stmt->execute();
  $stmt->close();
}
?>
