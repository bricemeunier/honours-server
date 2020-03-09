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
  <head>
		<meta charset="utf-8">
		<title>Home Page</title>
    <!-- materialize -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="style/style.css">

  </head>

  <body>
    <nav>
      <div class="nav-wrapper">
        <a href="#" class="brand-logo">Monitoring Dashboard</a>
        <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
          <li><a href="profile.php"><?php echo $_SESSION['name']; ?></a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </nav>
    <ul class="sidenav" id="mobile-demo">
      <li><a href="profile.php"><?php echo $_SESSION['name']; ?></a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>

    <div id="main">

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
              <div class="col-xs-3 b-r" >
                <div class="scrll_hide">
                  <div class="collection" id="numberList"></div>
                </div>
              </div>
              <div class="col-xs-9 b-r" >
                <div class="scrll_hide" id="discussion"></div>
              </div>
  			    </div>
  		    </div>
      </div>

  		<div id="appUsageContainer" class="tabcontent">
  			<ul></ul>
      </div>

    </div>
  </body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script type="text/javascript">


$(document).ready(function() {
  $('.sidenav').sidenav();
	fetchData("fetch/retrieveLocation.php","locationContainer");
	fetchSmsContact();
	fetchData("fetch/retrieveAppUsage.php","appUsageContainer");
	document.getElementById('locationButton').click();

});

function fetchSmsContact(){
	$.ajax({    //create an ajax request to display.php
    type: "GET",
    url: "fetch/retrieveSmsContact.php",
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
        document.getElementsByClassName('active')[0].click();
    	}
    }
  });
}

function addContactToSmsContainer(data,id){

  for (var i=0;i<data.length;i++){

    var row=data[i];
    var newP=document.createElement("p");
    var text=document.createTextNode(row[0]);
    var div=document.getElementById("numberList");
    var newDiv=document.createElement("div");

    if (i==0){
    	newDiv.setAttribute("class", "collection-item active");
    }
    else {
      newDiv.setAttribute("class", "collection-item");
    }
    newDiv.setAttribute("id",row[0]);
    newDiv.setAttribute("onclick","getMessage('"+row[0]+"')");

    newP.appendChild(text);
    newDiv.appendChild(newP)
    div.appendChild(newDiv);

  }

}


function getMessage(id){

  var previousId=document.getElementsByClassName('active')[0].id;
  var previousActiveDiv=document.getElementById(previousId);
  previousActiveDiv.removeAttribute("class");
  previousActiveDiv.setAttribute("class","collection-item");

  document.getElementById(id).setAttribute("class","collection-item active");
  console.log(id);
  $.ajax({    //create an ajax request to display.php
    type: "GET",
    url: "fetch/retrieveSmsMessages.php?address="+id,
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

    var row=data[data.length-i-1];
    var text=document.createTextNode(row[1]);
    var newDiv=document.createElement("div");
    if (row[0]==0){
      newDiv.setAttribute("class","bubble sender tooltipped");
      newDiv.setAttribute("data-position","right");
    }
    else {
    	newDiv.setAttribute("class","bubble recipient tooltipped");
      newDiv.setAttribute("data-position","left");
    }
    newDiv.setAttribute("data-tooltip",row[2]);
    newDiv.appendChild(text);
    sec.appendChild(newDiv);

  }
  $('.tooltipped').tooltip();
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
    		tablinks[i].className = tablinks[i].className.replace(" current", "");
  	}
  	document.getElementById(tab).style.display = "block";
  	evt.currentTarget.className += " current";
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
