<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit();
}

include 'DatabaseConfig.php' ;

$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

if (mysqli_connect_errno()) {
	die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
if ($stmt = $con->prepare('SELECT email FROM accounts WHERE id = ?')){
	// In this case we can use the account ID to get the account info.
	$stmt->bind_param('i', $_SESSION['id']);
	$stmt->execute();
	$stmt->bind_result($email);
	$stmt->fetch();
	$stmt->close();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Profile Page</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
		<link href="style/style.css" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="img/fav.png">
	</head>
	<body>
		<div id="profile-page">
			<nav>
				<div class="nav-wrapper">
					<a href="home.php" class="brand-logo">Monitoring Dashboard</a>
					<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
					<ul id="nav-mobile" class="right hide-on-med-and-down">
						<li><a href="#"><b><?php echo $_SESSION['name']; ?></b></a></li>
						<li><a href="logout.php"><b>Logout</b></a></li>
					</ul>
				</div>
			</nav>
			<ul class="sidenav" id="mobile-demo">
				<li><a href="home.php" class="brand-logo">Monitoring Dashboard</a></li>
				<li><a href="#"><b><?php echo $_SESSION['name']; ?></b></a></li>
				<li><a href="logout.php"><b>Logout</b></a></li>
			</ul>
			<div id="showInfo">
				<h5>Account details</h5>
				<br>
				<table id="accountDetails">
					<tr>
						<td>Username</td>
						<td id="longString"><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Private key</td>
						<td id="longString"><?=$_SESSION['key']?></td>
					</tr>
					<tr>
						<td>Email</td>
						<td id="longString"><?=$email?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
	<script type="text/javascript">

	// jquery loading when page starts
	$(document).ready(function() {

	  //responsive navbar
	  $('.sidenav')
			.sidenav()
			.on('click tap', 'li a', () => {
	      $('.sidenav').sidenav('close')
			})
	});
	</script>
</html>
