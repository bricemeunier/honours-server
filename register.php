<?php

session_start();
// Change this to your connection info.

include 'DatabaseConfig.php' ;

// Try and connect using the info above.
$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	die ();
	header('Location: login.php?error=00');
}

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
	// Could not get the data that should have been sent.
	die ();
	header('Location: login.php?error=2');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
	// One or more values are empty.
	die ();
	header('Location: login.php?error=2');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		header('Location: login.php?error=3');
	}
	else {
		// Insert new account
		// Username doesnt exists, insert new account
		if ($stmt = $con->prepare('INSERT INTO accounts (idUser,username, password, email) VALUES (?, ?, ?, ?)')) {
			// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
			$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$id=sha1($_POST['username']);
			$stmt->bind_param('ssss',$id, $_POST['username'], $password, $_POST['email']);
			$stmt->execute();
			header('Location: login.php?registration=1');
		}
		else {
			// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
			header('Location: login.php?error=0');
		}
	}
	$stmt->close();
}
else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	header('Location: login.php?error=0');
}

$con->close();
?>
