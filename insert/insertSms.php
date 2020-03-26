<?php

include '../DatabaseConfig.php' ;
 
 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $address = $_POST['address'];
 $message = $_POST['message'];
 $date = $_POST['date'];
 $action=$_POST['action'];
 if ($stmt = $con->prepare('INSERT INTO sms (idUser,date,action,address,message) values (?,?,?,?,?)')){
 	$stmt->bind_param('sssss',$key,$date,$action,$address,$message);
 	$stmt->execute();
	$stmt->close();
 }
?>
