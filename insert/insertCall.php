<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $phone = $_POST['phone'];
 $date = $_POST['date'];
 $duration = $_POST['duration'];
 $type = $_POST['type'];

 if ($stmt = $con->prepare('INSERT INTO callLogs (idUser,address,date,duration,type) VALUES (?,?,?,?,?)')){
  $stmt->bind_param('sssss',$key,$phone,$date,$duration,$type);
  $stmt->execute();
  $stmt->close();
 }
?>
