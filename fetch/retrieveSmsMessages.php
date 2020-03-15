<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
  header('Location: index.html');
  exit();
}


include '../DatabaseConfig.php' ;

$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

// If there is an error with the connection, stop the script and display the error.
 if ( mysqli_connect_errno() ) {
   http_response_code(500);
 }

$key=$_SESSION['key'];
$address=$_GET['address'];
$address=str_replace(' ', '', $address);
$address="%".$address;


if ($stmt = $con->prepare('select action,message,date from sms where idUser=? AND address like ? order by date desc')){
  // Bind parameters to avoid sql injection
  $stmt->bind_param('ss',$key,$address);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();
	$data=[];
	$stmt->bind_result($action,$message,$date);
	while ($temp = $stmt->fetch()){
    $date=date("d/m/Y H:i:s",$date/1000);
    array_push($data,array($action,$message,$date));
	}
	echo json_encode($data);
}
else {
  echo "Nice try";
}
?>
