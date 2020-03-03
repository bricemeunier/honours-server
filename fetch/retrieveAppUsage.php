<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

 $sql = "select timePeriod,app,timeUsed from usageStat order by date desc";
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
