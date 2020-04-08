<?php

include '../DatabaseConfig.php' ;
include 'insertNotifications.php';

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $timePeriod = $_POST['timePeriod'];
 $app = $_POST['app'];
 $timeUsed=$_POST['timeUsed'];

 $dailyTime=$timeUsed;
 $firstPeriod=$timePeriod-3601000;
 $secondPeriod=$timePeriod-3599000;

 $sql = "select timeUsedDaily from usageStat where idUser='$key' AND timePeriod > '$firstPeriod' AND timePeriod < '$secondPeriod' AND app='$app'";
 $result = mysqli_query($con,$sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_row($result);
    $lastTimeUsed=$row[0];
    $timeUsed=$timeUsed-$lastTimeUsed;
}

if ($timeUsed<0){
	$timeUsed=0;
}

if ($timeUsed>0) {
  checkAppNotification($key,$timePeriod,$app,$timeUsed);
}

$Sql_Query = "insert into usageStat (idUser,timeUsedDaily,timePeriod,app,timeUsed) values ('$key','$dailyTime','$timePeriod','$app','$timeUsed')";
mysqli_query($con,$Sql_Query);

?>
