<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $address = $_POST['address'];
 $message = $_POST['message'];
 $date = $_POST['date'];
 $action = $_POST['action'];
 $Sql_Query = "insert into sms (idUser,date,action,address,message) values ('$key','$date','$action','$address','$message')";

 mysqli_query($con,$Sql_Query);
?>
