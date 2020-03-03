<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: index.html');
        exit();
}

?>
<!DOCTYPE html>
<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="/style/style.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
 
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<a href="home.php"><h1>Monitoring Dashboard</h1></a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back <?php echo $_SESSION['name']; ?>!</p>
		</div>
		<div class="tab">
			<button id="locationButton" class="tablinks" onclick="openTabs(event, 'locationContainer')">Location</button>
  			<button id="smsButton" class="tablinks" onclick="openTabs(event, 'smsContainer')">SMS</button>
  			<button id="appButton" class="tablinks" onclick="openTabs(event, 'appUsageContainer')">App Usage</button>
		</div>
                <div id="locationContainer" class="tabcontent">
			<ul></ul>
                </div>
		<div id="smsContainer" class="tabcontent">
			<ul></ul>
                </div>
		<div id="appUsageContainer" class="tabcontent">
			<ul></ul>
                </div>
        </body>
		<script type="text/javascript">

 $(document).ready(function() {
	/*
    $("#locationButton").click(function(){
	fetchData("/fetch/retrieveLocation.php","locationContainer");
    });
    $("#smsButton").click(function(){
	fetchData("/fetch/retrieveSms.php","smsContainer");
    });
    $("#appButton").click(function(){
	fetchData("/fetch/retrieveAppUsage.php","appUsageContainer");
    });
	*/
	fetchData("/fetch/retrieveLocation.php","locationContainer");
	fetchData("/fetch/retrieveSms.php","smsContainer");
	fetchData("/fetch/retrieveAppUsage.php","appUsageContainer");
	document.getElementById('locationButton').click();
});

function fetchData(urlToFetch,idElement){
   $.ajax({    //create an ajax request to display.php
        type: "GET",
        url: urlToFetch,
        dataType: "html",   //expect html to be returned
        success: function(result){
                jq_json_obj = $.parseJSON(result); //Convert the JSON object to jQuery-compatible

                if(typeof jq_json_obj == 'object') { //Test if variable is a [JSON] object
                        jq_obj = eval (jq_json_obj);

                        //Convert back to an array
                        jq_array = [];
                        for(elem in jq_obj){
                                jq_array.push(jq_obj[elem]);
                        }
			addToHTML(jq_array,idElement);
		}
	}
   });
}

function openTabs(evt,tab) {
	var i, tabcontent, tablinks;
  	tabcontent = document.getElementsByClassName("tabcontent");
  	for (i = 0; i < tabcontent.length; i++) {
    		tabcontent[i].style.display = "none";
  	}
  	tablinks = document.getElementsByClassName("tablinks");
  	for (i = 0; i < tablinks.length; i++) {
    		tablinks[i].className = tablinks[i].className.replace(" active", "");
  	}
  	document.getElementById(tab).style.display = "block";
  	evt.currentTarget.className += " active";
}

function addToHTML(data,id){
	for (var i=0;i<data.length;i++){
		row=data[i];
		var text=row[0]+"  -  "+row[1]+" , "+row[2];
		var div=document.getElementById(id);
		var newLi=document.createElement("li");
		text=document.createTextNode(text);
		newLi.appendChild(text)
		div.appendChild(newLi);
	}
}

</script>
</html>

