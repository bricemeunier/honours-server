<!DOCTYPE html>
<html>

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Login</title>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
		<link rel="stylesheet" href="style/styleLogin.css">
		<link rel="shortcut icon" href="img/fav.png">
	</head>

	<body>

		<div id="container">
			<div id="nav-container">
				<ul id="tabs-swipe-demo" class="tabs">
					<li class="tab col s6"><a class="active" href="#login">Login</a></li>
					<li class="tab col s6"><a href="#register">Register</a></li>
				</ul>
			</div>

			<div id="login" class="col s12">
				<h1>Login</h1>
				<p id="error"></p>
				<p id="success"></p>
				<form action="authenticate.php" method="post">
					<label for="username">
						<i class="fas fa-user"></i>
					</label>
					<input type="text" name="username" placeholder="Username" id="username" required>
					<label for="password">
						<i class="fas fa-lock"></i>
					</label>
					<input type="password" name="password" placeholder="Password" id="password" required>
					<button class="btn waves-effect waves-light" type="submit" name="login">Login
						<i class="material-icons right">send</i>
					</button>
				</form>
			</div>

			<div id="register" class="col s12">
	      <h1>Register</h1>
				<p id="errorRegistration"></p>
				<form action="register.php" method="post" autocomplete="off">
					<label for="username">
						<i class="fas fa-user"></i>
					</label>
					<input type="text" name="username" placeholder="Username" id="usernameRegistration" required>
					<label for="password">
						<i class="fas fa-lock"></i>
					</label>
					<input type="password" name="password" placeholder="Password" id="passwordRegistration" required>
					<label for="email">
						<i class="fas fa-envelope"></i>
					</label>
					<input type="email" name="email" placeholder="Email" id="email" required>

					<button class="btn waves-effect waves-light" type="submit" name="register">Register
						<i class="material-icons right">send</i>
					</button>
				</form>
	    </div>

		</div>
	</body>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){

			$('ul.tabs').tabs({
			  swipeable : true,
			  responsiveThreshold : 1920
			});

			$(".tabs-content").css('height','420px');

			var queryString=window.location.search;
			var urlParams=new URLSearchParams(queryString);
			if (urlParams.get("error")=="1"){
				var doc=document.getElementById("error");
				var errMessage="<p>Username or password incorrect</p>";
				doc.innerHTML=errMessage;
			}
			if (urlParams.get("error")=="0"){
	                        var doc=document.getElementById("error");
	                        var errMessage="<p>Error, please try again</p>";
	                        doc.innerHTML=errMessage;
	                }
			if (urlParams.get("error")=="00"){
	                        var doc=document.getElementById("errorRegistration");
	                        var errMessage="<p>Error, please try again</p>";
	                        doc.innerHTML=errMessage;
	                }
			if (urlParams.get("error")=="2"){
	                        var doc=document.getElementById("errorRegistration");
	                        var errMessage="<p>Username or password incorrect</p>";
	                        doc.innerHTML=errMessage;
	                }
			if (urlParams.get("error")=="3"){
	                        var doc=document.getElementById("errorRegistration");
	                        var errMessage="<p>Username already exists</p>";
	                        doc.innerHTML=errMessage;
	                }
			if (user=urlParams.get("registration")){
	                        var doc=document.getElementById("success");
	                        var errMessage="<p>Registration completed, please login</p>";
	                        doc.innerHTML=errMessage;
	                }

		});
	</script>
</html>
