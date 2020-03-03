<?php
// Change this to your connection info.
include 'DatabaseConfig.php' ;

$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	http_response_code(500);
	die ();
}

if ( !isset($_GET['key'])) {
	// Could not get the data that should have been sent
	http_response_code(402);
	die ();
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id,password FROM accounts WHERE idUser= ?')){
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s',$_GET['key']);
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
		http_response_code(200);
	}
	else {
		http_response_code(404);
	}
}
$con->close();

?>
