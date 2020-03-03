<?php

include 'DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 $timePeriod = $_POST['timePeriod'];
 $app = $_POST['app'];
 $timeUsed=$_POST['timeUsed'];
 $firstPeriod=$timePeriod-3601000;
 $secondPeriod=$timePeriod-3599000;
$sql = "select timeUsed from usageStat where timePeriod > '$firstPeriod' AND timePeriod < '$secondPeriod' AND app='$app'";

// $sql = "select timeUsed from usageStat";
 $result = mysqli_query($con,$sql);

if (mysqli_num_rows($result)>0) {
	$row = mysqli_fetch_row($result);
	$lastTimeUsed=$row[0];
	$timeUsed=$timeUsed-$lastTimeUsed;
}

$Sql_Query = "insert into usageStat (timePeriod,app,timeUsed) values ('$timePeriod','$app','$timeUsed')";
mysqli_query($con,$Sql_Query);
?>

