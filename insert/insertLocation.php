<?php

 include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 $key = $_POST['key'];
 $lat = $_POST['latitude'];
 $lon = $_POST['longitude'];

 if ($stmt = $con->prepare('INSERT INTO location (idUser,lat,lon) VALUES (?,?,?)')){
 	$stmt->bind_param('sss',$key,$lat,$lon);
 	$stmt->execute();
  $stmt->close();
 }

?>
