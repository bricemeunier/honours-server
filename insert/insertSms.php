<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $address = $_POST['address'];
 $message = $_POST['message'];
 $date = $_POST['date']; 
 $Sql_Query = "insert into sms (idUser,date,address,message) values ('$key','$date','$address','$message')";

 if(mysqli_query($con,$Sql_Query)){

 echo 'Data Submit Successfully';

 }
 else{

 echo 'Try Again';

}
?>
