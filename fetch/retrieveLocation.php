<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 $sql = "select date,lat,lon from location order by date desc";
 $result = mysqli_query($con,$sql);
 $data=[];
for ($i=0; $i<mysqli_num_rows($result);$i++){
	array_push($data,mysqli_fetch_row($result));
}

echo json_encode($data);
?>
