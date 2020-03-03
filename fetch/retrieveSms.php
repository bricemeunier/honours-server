<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 $sql = "select date,address,message from sms order by date desc";
 $result = mysqli_query($con,$sql);
 $data=[];
for ($i=0; $i<mysqli_num_rows($result);$i++){
	$temp=mysqli_fetch_row($result);
	$temp[0]=date("d/m/Y H:i:s",$temp[0]/1000);
	array_push($data,$temp);
}

echo json_encode($data);
?>
