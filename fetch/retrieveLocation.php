<?php

include '../DatabaseConfig.php' ;

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: index.html');
        exit();
}
 $key=$_SESSION['key'];
 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 $sql = "select date,lat,lon from location where idUser='$key' order by date desc";
 $result = mysqli_query($con,$sql);
 $data=[];
for ($i=0; $i<mysqli_num_rows($result);$i++){
	array_push($data,mysqli_fetch_row($result));
}

echo json_encode($data);
?>
