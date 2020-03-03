<?php

include '../DatabaseConfig.php' ;
 echo $HostName;
 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $lat = $_POST['latitude'];
 $lon = $_POST['longitude'];

 $Sql_Query = "insert into location (idUser,lat,lon) values ('$key','$lat','$lon')";

 if($con->query($Sql_Query)){

 echo 'Data Submit Successfully';

 }
 else{

 echo 'Try Again';

}
?>
