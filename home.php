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
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
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
		    <div class="container-fluid">
    			<div class="row">
    				<div class="col-xs-3 b-r padding_bottom_70" >
				      <div class="scrll_hide" id="numberList">
				      </div>
    				</div>

   				 <div class="col-xs-9 b-r padding_bottom_70" >
      					<div class="scrll_hide" id="discussion">
					</div>
    				</div>
			</div>
		    </div>
                </div>

		<div id="appUsageContainer" class="tabcontent">
			<ul></ul>
                </div>
        </body>
		<script type="text/javascript">

 $(document).ready(function() {

	fetchData("/fetch/retrieveLocation.php","locationContainer");
	fetchSmsContact();
	fetchData("/fetch/retrieveAppUsage.php","appUsageContainer");
	document.getElementById('locationButton').click();

});

function fetchSmsContact(){
	$.ajax({    //create an ajax request to display.php
	        type: "GET",
        	url: "/fetch/retrieveSmsContact.php",
        	dataType: "html",   //expect html to be returned
        	success: function(result){
                	jq_json_obj = $.parseJSON(result); //Convert the JSON object to jQuery-compat$

                	if(typeof jq_json_obj == 'object') { //Test if variable is a [JSON] object
                        	jq_obj = eval (jq_json_obj);

                        	//Convert back to an array
                        	jq_array = [];
                        	for(elem in jq_obj){
                                	jq_array.push(jq_obj[elem]);
                        	}
                       		addContactToSmsContainer(jq_array);
                	}
        	}
   	});
}

function addContactToSmsContainer(data,id){
        for (var i=0;i<data.length;i++){
                row=data[i];
		var newP=document.createElement("p");
                var text=document.createTextNode(row[0]);
		newP.appendChild(text);
                var div=document.getElementById("numberList");
                var newDiv=document.createElement("div");
		if (i==0){
			newDiv.setAttribute("id",row[0]+" current");
		}
		else {
			newDiv.setAttribute("id",row[0]);
                }
		newDiv.setAttribute("onclick","getMessage("+row[0]+")");
		newDiv.appendChild(newP)
                div.appendChild(newDiv);
        }
}


function getMessage(id){
        $.ajax({    //create an ajax request to display.php
                type: "GET",
                url: "/fetch/retrieveSmsMessages.php?address="+id,
                dataType: "html",   //expect html to be returned
                success: function(result){
                        jq_json_obj = $.parseJSON(result); //Convert the JSON object to jQuery-$

                        if(typeof jq_json_obj == 'object') { //Test if variable is a [JSON] obj$
                                jq_obj = eval (jq_json_obj);

                                //Convert back to an array
                                jq_array = [];
                                for(elem in jq_obj){
                                        jq_array.push(jq_obj[elem]);
                                }
                                addMessageToHTML(jq_array);
                        }
                }
        });
}

function addMessageToHTML(data){
        var sec=document.getElementById("discussion");
	sec.innerHTML="";
        for (var i=0;i<data.length;i++){
                row=data[data.length-i-1];
                var text=document.createTextNode(row[1]);
                var newDiv=document.createElement("div");
		if (row[0]==0){
                	newDiv.setAttribute("class","bubble sender");
                }
		else {
			newDiv.setAttribute("class","bubble recipient");
		}
                newDiv.appendChild(text);
                sec.appendChild(newDiv);
        }
}


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

