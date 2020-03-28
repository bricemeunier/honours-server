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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
    <link rel="shortcut icon" href="img/fav.png">
  </head>

  <body>
    <div id="naviguation">
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
    </div>

    <div id="main">

  		<div class="tab">
        <button id="locationButton" onclick="openTabs(event,'#locationContainer')">Location</button>
        <button id="smsButton" onclick="openTabs(event,'#smsContainer')">SMS</button>
        <button id="appButton" onclick="openTabs(event,'#appUsageContainer')">App Usage</button>
  		</div>
      <div id="monitoredContent">
        <div id="locationContainer">
          <form id="selectDate" method="get">
            <input type="text" class="locationDatepicker"/>
          </form>
          <div id="mapid"></div>
        </div>

        <div id="smsContainer">
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

    		<div id="appUsageContainer">
    			<form id="pickDate" method="get">
            <input type="text" class="appUsageDatepicker"/>
          </form>
        </div>
      </div>
    </div>
  </body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script type="text/javascript">

// jquery loading when page starts
$(document).ready(function() {

  //responsive navbar
  $('.sidenav').sidenav();

  //div fit window on load
  //should be set with resize as $(window).on('load resize',...)
  //but .on("load") does not work in firefox
  var body=$("body").height();
  var nav=$("#naviguation").height();
  $("#main").height(body-nav-(body*0.01));
  var main=$("#main").height();
  var tab=$(".tab").height();
  $("#monitoredContent").height(main-tab-(main*0.01)-1);
  var moni=$("#monitoredContent").height();
  var datePicker=null;

  //fit window on resize
  $(window).on('resize',function () {
    body=$("body").height();
    nav=$("#naviguation").height();
    $("#main").height(body-nav-(body*0.01));
    main=$("#main").height();
    tab=$(".tab").height();
    $("#monitoredContent").height(main-tab-(main*0.01)-1);
    moni=$("#monitoredContent").height();
    if (datePicker!=null){
      datePicker=$("#selectDate").height();
      $("#mapid").height(moni-datePicker-(moni*0.01)-5);
    }
  });

  //get today's date
  var today = new Date();
  var dd = today.getDate();
  var mm = today.getMonth()+1;
  var yyyy = today.getFullYear();

  //sms page loading on click
  $("#smsButton").on('click',function() {

    //empty the actual div to avoid display issues
    $("#numberList").empty();
    //$("#discussion").empty();

    //empty the two other divs
    $("#mapid").empty();
    if(window.myChart != undefined) {
      window.myChart.destroy();
    }

    if (datePicker!=null){
      datePicker=null;
    }
  	fetchSmsContact();
  });

  //location page on click
  $("#locationButton").on('click',function() {

    //empty the two other divs
    $("#numberList").empty();
    $("#discussion").empty();
    if(window.myChart != undefined) {
      window.myChart.destroy();
    }

    datePicker=$("#selectDate").height();
    $("#mapid").height(moni-datePicker-(moni*0.01)-5);
    //set value to datepicker
    getLocationFromDate(new Date(yyyy+"/"+"0"+mm+"/"+dd).getTime());
    document.getElementsByClassName("locationDatepicker")[0].value=yyyy+"/"+"0"+mm+"/"+dd;

    //set datepicker
    $(".locationDatepicker").datepicker({
        format: "yyyy/mm/dd",
        autoClose: true,
        onSelect : function(time){
          var dt=new Date(time);
          getLocationFromDate(dt.getTime());
        }
    });
  });

  //app usage page on onclick
  $("#appButton").on('click',function() {

    //empty the two other divs
    $("#mapid").empty();
    $("#numberList").empty();
    $("#discussion").empty();

    if (datePicker!=null){
      datePicker=null;
    }

    //set value to datepicker
    getAppUsageFromDate(new Date(yyyy+"-"+"0"+mm+"-"+dd).getTime());
    document.getElementsByClassName("appUsageDatepicker")[0].value=yyyy+"/"+"0"+mm+"/"+dd;

    //set datepicker
    $(".appUsageDatepicker").datepicker({
        format: "yyyy/mm/dd",
        autoClose: true,
        onSelect : function(time){
          var dt=new Date(time);
          getAppUsageFromDate(dt.getTime());
        }
    });

  });

	document.getElementById('locationButton').click();

});

function makeMap(data){

  //create a new map each time
  if (window.mymap != undefined) {
    window.mymap.remove();
  }

  //set view in Aberdeen, UK
  window.mymap = L.map('mapid').setView([57.149, -2.1], 13);

	L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 20,
		id: 'mapbox/streets-v11',
		tileSize: 512,
		zoomOffset: -1
	}).addTo(mymap);
  var arrayOfLatLngs=[];
  if (data !=undefined) {
    data.forEach(function(onePlace) {
      arrayOfLatLngs.push([onePlace[0][0],onePlace[0][1]]);
      L.marker([onePlace[0][0],onePlace[0][1]]).addTo(mymap).
        bindPopup(onePlace[1]);
    });
  	L.popup();
    var bounds = new L.LatLngBounds(arrayOfLatLngs);
    mymap.fitBounds(bounds,{maxZoom:13});
  }
}
//fetch locations from a given day
function getLocationFromDate(d){

  $.ajax({    //create an ajax request to display.php
    type: "GET",
    url: "fetch/retrieveLocation.php?date="+d,
    dataType: "html",   //expect html to be returned
    success: function(result){
      if (result!=undefined) {
        jq_json_obj = $.parseJSON(result); //Convert the JSON object to jQuery-compat$

        if(typeof jq_json_obj == 'object') { //Test if variable is a [JSON] object
        	jq_obj = eval (jq_json_obj);

        	//Convert back to an array
        	jq_array = [];
        	for(elem in jq_obj){
            jq_array.push(jq_obj[elem]);
        	}
          jq_array.forEach(function(elem) {
            var str="";
            elem[1].forEach(function(res) {
              str+="<dt>"+  res+"</dt>";
            });
            elem[1]=str;
          });
          makeMap(jq_array);
        }
    	}
      else {
        makeMap();
      }
    },
    error: function(){
      makeMap();
    }
  });

}

//fetch application usage statistics from a given date
function getAppUsageFromDate(d){

  $.ajax({    //create an ajax request to display.php
    type: "GET",
    url: "fetch/retrieveAppUsage.php?date="+d,
    dataType: "html",   //expect html to be returned
    success: function(result){
      if (result!=undefined){
        jq_json_obj = $.parseJSON(result); //Convert the JSON object to jQuery-compat$

      	if(typeof jq_json_obj == 'object') { //Test if variable is a [JSON] object
        	jq_obj = eval (jq_json_obj);

        	//Convert back to an array
        	jq_array = [];
        	for(elem in jq_obj){
            jq_array.push(jq_obj[elem]);
        	}
          var data=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
          var detailedData=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
          jq_array.forEach(function(dataHour) {
            var tmp=d;
            var i=0;

            while (Math.abs(tmp-parseInt(dataHour[0][0]))>3599999 && i<24){
              tmp+=3600000;
              i++;
            }
            if (i<24){
              var total_min=0;
              var tmpTab=[];
              for (var j=0;j<dataHour.length;j++){
                total_min+=parseInt(dataHour[j][2]);
                tmpTab[j]=[dataHour[j][1],dataHour[j][2]];
              }
              total_min=Math.floor(total_min/60);
              if (total_min<1) total_min=1;
              if (total_min>60) total_min=60;
              detailedData[i]=tmpTab;
              data[i]=total_min;
            }
          });
          makeChart(data,detailedData);
      	}
      }
      else {
        makeChart([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],null);
      }
    },
    error: function(){

      makeChart([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],null);
    }
  });

}

//create chart for appUsage page
function makeChart(data, detailedData){

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
        legend: {
          display: false
        },
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              var result="Time spent using the phone: "+
              data['datasets'][0]['data'][tooltipItem['index']]+" min";
              return result;
            },
            afterLabel: function(tooltipItem, data) {
              var d=detailedData[tooltipItem.index];
              var result="\n";
              for (var i=0;i<d.length;i++) {
                if (d[i]!=0){
                  var t=parseInt(d[i][1]);
                  if (t>59){
                    var s=t-Math.floor(t/60)*60;
                    if (s<10){
                      result+=d[i][0]+" "+Math.floor(t/60)+" min 0"+s+" sec\n";
                    }
                    else {
                      result+=d[i][0]+" "+Math.floor(t/60)+" min "+s+" sec\n";
                    }
                  }
                  else {
                    var sec="";
                    if (parseInt(d[i][1])<10) sec="0"+d[i][1]+" sec\n";
                    else sec=d[i][1]+" sec\n";
                    result+=d[i][0]+" "+sec;
                  }
                }
              }
              return result;
            }
          }
        },
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
      if (result!=undefined){
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

// Manage the nav bar containing the monitored information
function openTabs(e,div) {
	$("#monitoredContent").children().hide();
  $(div).show();
  $(".tab button").css("background-color","initial");
  e.currentTarget.style="background-color: #ccc;";
}


</script>
</html>
