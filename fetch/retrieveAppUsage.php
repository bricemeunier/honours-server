<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: index.html');
        exit();
}

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key=$_SESSION['key'];
 $sql = "select timePeriod,app,timeUsed from usageStat where idUser='$key' order by date desc";
 $result = mysqli_query($con,$sql);
 $data=[];
for ($i=0; $i<mysqli_num_rows($result);$i++){
	$temp=mysqli_fetch_row($result);
	if ($temp[2]==0){
		continue;
	}
	$temp[0]=date("d/m/y H:00",$temp[0]/1000);
	array_push($data,$temp);
}

echo json_encode($data);
?>
