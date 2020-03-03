<?php

include 'DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $lat = $_POST['latitude'];
 $lon = $_POST['longitude'];

 $Sql_Query = "insert into location (lat,lon) values ('$lat','$lon')";

 if($con->query($Sql_Query)){

 echo 'Data Submit Successfully';

 }
 else{

 echo 'Try Again';

}
?>
