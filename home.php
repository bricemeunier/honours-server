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
          <li><a href="profile.php"><b><?php echo $_SESSION['name']; ?></b></a></li>
          <li><a href="logout.php"><b>Logout</b></a></li>
        </ul>
      </div>
    </nav>
    <ul class="sidenav" id="mobile-demo">
      <li><a href="profile.php"><b><?php echo $_SESSION['name']; ?></b></a></li>
      <li><a href="logout.php"><b>Logout</b></a></li>
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
                <div class="scrll_hide">
                  <div id="discussion"></div>
                </div>
              </div>
  			    </div>
  		    </div>
      </div>

  		<div id="appUsageContainer" class="tabcontent">
  			<form id="pickDate" method="get">
          <input type="date" id="choosenDate" name="choosenDate"/>
          <input class="submit" type="submit" value="Send" />
        </form>
      </div>
    </div>
  </body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script type="text/javascript">

// jquery loading when page starts
$(document).ready(function() {

  $('.sidenav').sidenav();
	fetchData("fetch/retrieveLocation.php","locationContainer");
	fetchSmsContact();
	document.getElementById('locationButton').click();
  $('#pickDate').submit(function () {
    var datePicked = document.getElementById("choosenDate").value;
    if (datePicked!=""){
      var dt=new Date(datePicked);
      getAppUsageFromDate(dt.getTime());
    }
    return false;
  });

});

function getAppUsageFromDate(d){

  $.ajax({    //create an ajax request to display.php
    type: "GET",
    url: "fetch/retrieveAppUsage.php?date="+d,
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
        var data=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        jq_array.forEach(function(dataHour) {
          var tmp=d;
          var i=0;
          while (Math.abs(tmp-parseInt(dataHour[0][0]))>3599999 && i<24){
            tmp+=3600000;
            i++;
          }
          if (i<24){
            var total_min=0;
            for (var j=0;j<dataHour.length;j++){
              total_min+=parseInt(dataHour[j][2]);
            }
            total_min=Math.floor(total_min/60);
            if (total_min<1) total_min=1;
            if (total_min>60) total_min=60;
            data[i]=total_min;
          }
        });
        makeChart(data);
    	}
    },
    error: function(){

      makeChart([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]);
    }
  });

}

function makeChart(data){

  if(window.myChart != undefined) {
    window.myChart.destroy();
  }
  else {
    var canvas = document.createElement('canvas');
    canvas.id = "myChart";
    var parent=document.getElementById("appUsageContainer");
    parent.appendChild(canvas);
  }

  var ctx = document.getElementById('myChart').getContext('2d');
  window.myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: ['00h', '01h', '02h', '03h', '04h', '05h', '06h', '07h', '08h', '09h', '10h',
           '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h'],
          datasets: [{
              label: 'Time spent using the phone',
              data: data,
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
          }]
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              suggestedMax:60,
              beginAtZero: true
            }
          }]
        }
      }
  });
}

// fetch all the contact numbers
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

// Add contact numbers to HTML
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

// fetch all messages from a contact number
function getMessage(id){

  var previousId=document.getElementsByClassName('active')[0].id;
  var previousActiveDiv=document.getElementById(previousId);
  previousActiveDiv.removeAttribute("class");
  previousActiveDiv.setAttribute("class","collection-item");

  document.getElementById(id).setAttribute("class","collection-item active");

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

// add messages from a contact number to HTML
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

// Manage the nav bar containing the monitored information
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


</script>
</html>
