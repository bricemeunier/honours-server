<?php

include '../DatabaseConfig.php' ;

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
        exit();
}

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

// If there is an error with the connection, stop the script and display the error.
 if ( mysqli_connect_errno() ) {
   http_response_code(500);
 }


 $key=$_SESSION['key'];

 $sql = "select name,phone from contact where idUser='$key' ORDER BY name";
 $result = mysqli_query($con,$sql);
 $data=[];
 if (mysqli_num_rows($result)<1){
   http_response_code(204);
 }
 else {
   for ($i=0; $i<mysqli_num_rows($result);$i++){
     array_push($data,mysqli_fetch_row($result));
   }

   echo json_encode($data);
 }
?>
