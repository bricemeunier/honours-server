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
 $id=$_POST['id'];

 if ($stmt = $con->prepare('UPDATE notification SET seen="1" WHERE id=? AND idUser=?')){
   $stmt->bind_param('ss',$id,$key);
   $stmt->execute();
   $stmt->close();
 }
?>
