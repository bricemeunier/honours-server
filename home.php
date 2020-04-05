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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Home Page</title>
    <!-- materialize -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
    <link rel="shortcut icon" href="img/fav.png">
  </head>

  <body>
    <div id="pageContainer">
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
          <li><a href="#" class="brand-logo">Monitoring Dashboard</a></li>
          <li id="locationButton-mobile"><a href="javascript:openTabs(event,'#locationContainer',true);"><b>Location</b></a></li>
          <li id="smsButton-mobile"><a href="javascript:openTabs(event,'#smsContainer',true);"><b>SMS</b></a></li>
          <li id="appButton-mobile"><a href="javascript:openTabs(event,'#appUsageContainer',true);"><b>App Usage</b></a></li>
          <li id="contactButton-mobile"><a href="javascript:openTabs(event,'#contactContainer',true);"><b>Contact List</b></a></li>
          <li><a href="profile.php"><b>Profile</b></a></li>
          <li><a href="logout.php"><b>Logout</b></a></li>
        </ul>
      </div>

      <div id="main">

    		<div class="tab">
          <button id="locationButton" onclick="openTabs(event,'#locationContainer')">Location</button>
          <button id="smsButton" onclick="openTabs(event,'#smsContainer')">SMS</button>
          <button id="appButton" onclick="openTabs(event,'#appUsageContainer')">App Usage</button>
          <button id="contactButton" onclick="openTabs(event,'#contactContainer')">Contact List</button>
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
                  <div class="num b-r" >
                    <div class="scrll_hide">
                      <div class="collection" id="numberList"></div>
                    </div>
                  </div>
                  <div class="text b-r" >
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
            <div id="chartContainer"></div>
            <div id="appUsageDetails"></div>
          </div>

          <div id="contactContainer"></div>
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
  $('.sidenav')
    .sidenav()
    .on('click tap', 'li a', () => {
      $('.sidenav').sidenav('close');
    });

  if (!$('.hide-on-med-and-down').is(':visible')) {
    $('.tab').hide();
  }

  //div fit window on load
  //should be set with resize as $(window).on('load resize',...)
  //but .on("load") does not work in firefox
  var body=$("body").height();
  var nav=$("#naviguation").height();
  $("#main").height(body-nav-(body*0.01));
  var main=$("#main").height();
  if (!$('.tab').is(':visible')) {
    var tab=0;
  }
  else {
    var tab=$(".tab").height();
  }
  $("#monitoredContent").height(main-tab-(main*0.01));
  var moni=$("#monitoredContent").height();
  var datePicker=null;

  //fit window on resize
  $(window).on('resize',function () {
    if (!$('.hide-on-med-and-down').is(':visible')) {
      $('.tab').hide();
    }
    else {
      $('.tab').show();
    }

    body=$("body").height();
    nav=$("#naviguation").height();
    $("#main").height(body-nav-(body*0.01));
    main=$("#main").height();
    if (!$('.tab').is(':visible')) {
      tab=0;
    }
    else {
      tab=$(".tab").height();
    }
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

  //redirect on click on mobile view
  $("#smsButton-mobile").on('click',function() {
    document.getElementById('smsButton').click();
  });

  //sms page loading on click
  $("#smsButton").on('click',function() {

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

  //redirect on click on mobile view
  $("#locationButton-mobile").on('click',function() {
    document.getElementById('locationButton').click();
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

  //redirect on click on mobile view
  $("#appButton-mobile").on('click',function() {
    document.getElementById('appButton').click();
  });

  //app usage page on click
  $("#appButton").on('click',function() {

    //empty the two other divs
    $("#mapid").empty();
    $("#numberList").empty();
    $("#discussion").empty();
    $("#appUsageDetails").empty();
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

  //redirect on click on mobile view
  $("#contactButton-mobile").on('click',function() {
    document.getElementById('contactButton').click();
  });

  //contact list page on click
  $("#contactButton").on('click', function() {

    //empty other tabs
    $("#mapid").empty();
    $("#numberList").empty();
    $("#discussion").empty();
    if(window.myChart != undefined) {
      window.myChart.destroy();
    }

    addContactListToContactContainer();
  });

	document.getElementById('locationButton').click();

});

//for contact list, fetch every contact
function fetchContactList(){

  return $.ajax({    //create an ajax request to display.php
    type: "GET",
    url: "fetch/retrieveContactList.php",
    dataType: "html",   //expect html to be returned
    success: function(result){
      return result;
    },
    error: function(){
      return null;
    }
  });
}

//for contact list, add contact list to contactContainer
function addContactListToContactContainer(){
  $.when(fetchContactList()).done(function(result){
    if (result!=undefined){
      jq_json_obj = $.parseJSON(result); //Convert the JSON object to jQuery-compat$

      if(typeof jq_json_obj == 'object') { //Test if variable is a [JSON] object
        jq_obj = eval (jq_json_obj);

        //Convert back to an array
        jq_array = [];
        for(elem in jq_obj){
          jq_array.push(jq_obj[elem]);
        }
        list=jq_array;
      }
    }

    if (list==null){
      $('#contactContainer').html("Error while fetching contact list, please try again");
    }
    else {
      var div=document.getElementById("contactContainer");
      var t=document.createElement("table");
      t.setAttribute("class","highlight");
      var thead=document.createElement("thead");
      var trhead=document.createElement("tr");
      var thName=document.createElement("th");
      var thPhone=document.createElement("th");
      thName.append("Name");
      thPhone.append("Phone");
      trhead.appendChild(thName);
      trhead.appendChild(thPhone);
      thead.appendChild(trhead);
      t.appendChild(thead);
      var tbody=document.createElement("tbody");

      for (var i=0;i<list.length;i++) {
        var row=list[i];
        var tr=document.createElement("tr");
        var tdName=document.createElement("td");
        var tdPhone=document.createElement("td");
        tdName.append(row[0]);
        tdPhone.append(row[1]);
        tr.appendChild(tdName);
        tr.appendChild(tdPhone);
        tbody.appendChild(tr);
      }
      t.appendChild(tbody);
      div.appendChild(t);
    }
  });

}

//for location, create the open street map
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
  mymap.zoomControl.setPosition('topright');
}

//for location, fetch locations from a given day
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

//for app usage, fetch application usage statistics from a given date
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
          addAppUsageDetailsToHTML(data, detailedData);
      	}
      }
      else {
        makeChart([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],null);
        addAppUsageDetailsToHTML(null,null);
      }
    },
    error: function(){

      makeChart([0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],null);
      addAppUsageDetailsToHTML(null,null);
    }
  });

}

//for app usage, create chart for appUsage page
function makeChart(data, detailedData){

  if(window.myChart != undefined) {
    window.myChart.destroy();
  }
  else {
    var canvas = document.createElement('canvas');
    canvas.id = "myChart";
    var parent=document.getElementById("chartContainer");
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
        maintainAspectRatio: false,
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


function addAppUsageDetailsToHTML(data,detailedData) {

  var total_time_in_apps=0;
  var average_time_used_by_hour=0;
  var counting_active_hour=0;

  if (data!=null) {
    for (var i=0;i<data.length;i++){
      if (data[i]>0){
          counting_active_hour++;
          total_time_in_apps+=parseInt(data[i]);
      }
    }
    average_time_used_by_hour=total_time_in_apps;
    //calcul to determine how many hours a day has been spent on the phone
    if (total_time_in_apps>59){
      var m=total_time_in_apps-Math.floor(total_time_in_apps/60)*60;
      if (m<10) {
        total_time_in_apps=Math.floor(total_time_in_apps/60)+" h 0"+m+" min";
      }
      else {
        total_time_in_apps=Math.floor(total_time_in_apps/60)+" h "+m+" min";
      }
    }
    else {
      total_time_in_apps=total_time_in_apps+" min";
    }
  }
  else total_time_in_apps="0 min"


  $("#appUsageDetails").empty();
  var div=document.getElementById("appUsageDetails");


  //create table for total time using Phone
  var table_total_time=document.createElement("table");
  table_total_time.setAttribute("class","highlight");
  var thead_total=document.createElement("thead");
  var trhead_total=document.createElement("tr");
  var thStat=document.createElement("th");
  var thTime_total=document.createElement("th");
  thStat.append("Stats");
  thTime_total.append("Time");
  trhead_total.appendChild(thStat);
  trhead_total.appendChild(thTime_total);
  thead_total.appendChild(trhead_total);
  table_total_time.appendChild(thead_total);

  var tbody_total=document.createElement("tbody");
  var tr_total_time=document.createElement("tr");
  var td_total_time_span=document.createElement("td");
  var td_total_time=document.createElement("td");
  td_total_time_span.append("Total time used this day");
  td_total_time.append(total_time_in_apps);
  tr_total_time.appendChild(td_total_time_span);
  tr_total_time.appendChild(td_total_time);
  tbody_total.appendChild(tr_total_time);

  //adding average time used by hour
  min=Math.floor(Math.floor(average_time_used_by_hour*60/24)/60);
  sec=Math.floor(average_time_used_by_hour*60/24)-min*60;
  res="";
  if (min>0) {
    res=min+" min ";
  }
  if (sec<10 && sec!=0) {
    res+="0"+sec+" s";
  }
  else if (sec!=0) {
    res+=sec+" s";
  }
  else if (min>0){
    res+=sec+" s";
  }

  if (res=="") {
    res+="N/A";
  }

  var tr_average_time=document.createElement("tr");
  var td_average_time_span=document.createElement("td");
  var td_average_time=document.createElement("td");
  td_average_time_span.append("Average Time spent per hour");
  td_average_time.append(res);
  tr_average_time.appendChild(td_average_time_span);
  tr_average_time.appendChild(td_average_time);
  tbody_total.appendChild(tr_average_time);


  //adding average time used by active hour
  min=Math.floor(Math.floor(average_time_used_by_hour*60/counting_active_hour)/60);
  sec=Math.floor(average_time_used_by_hour*60/counting_active_hour)-min*60;
  res="";
  if (counting_active_hour!=0){
    if (min>0) {
      res=min+" min ";
    }

    if (sec<10 && sec!=0) {
      res+="0"+sec+" s";
    }
    else if (sec!=0) {
      res+=sec+" s";
    }
    else if (min>0){
      res+=sec+" s";
    }

    if (res=="") {
      res+="N/A";
    }
  }
  else res="N/A";

  var tr_active_time=document.createElement("tr");
  var td_active_time_span=document.createElement("td");
  var td_active_time=document.createElement("td");
  td_active_time_span.append("Average Time spent per active hour");
  td_active_time.append(res);
  tr_active_time.appendChild(td_active_time_span);
  tr_active_time.appendChild(td_active_time);
  tbody_total.appendChild(tr_active_time);


  //adding the table with 3 lines to the div
  table_total_time.appendChild(tbody_total);
  div.appendChild(table_total_time);



  //calcul to determine how long each app have been used on the selected day
  if (detailedData!=null) {
    var result = new Map();
    details=[];

    for (var i=0;i<detailedData.length;i++) {
      if (detailedData[i]!=0) details=details.concat(detailedData[i]);
    }

    for (var i=0;i<details.length;i++) {
      if (!(result.has(details[i][0]))){
        app_name=details[i][0];
        time_app=parseInt(details[i][1]);

        for (var j=i+1;j<details.length;j++){
          if (app_name==details[j][0]) {
            time_app+=parseInt(details[j][1]);
          }
        }
        result.set(app_name,time_app);
      }
    }
    result = new Map([...result.entries()].sort((a, b) => b[1] - a[1]));


    //create table for total time using every app
    var table=document.createElement("table");
    table.setAttribute("class","highlight centered");
    var thead=document.createElement("thead");
    var trhead=document.createElement("tr");
    var thApp=document.createElement("th");
    var thTime=document.createElement("th");
    thApp.append("App");
    thTime.append("Total time this day");
    trhead.appendChild(thApp);
    trhead.appendChild(thTime);
    thead.appendChild(trhead);
    table.appendChild(thead);
    var tbody=document.createElement("tbody");

    for (var [a,t] of result) {

      s=t-Math.floor(t/60)*60;
      if (s>9) s=s+" s";
      else if (s!=0) s="0"+s+" s";
      else s="";

      t=Math.floor(t/60);
      if (t>59){
        var m=t-Math.floor(t/60)*60;
        if (m<10) {
          t=Math.floor(t/60)+" h 0"+m+" min "+s;
        }
        else {
          t=Math.floor(t/60)+" h "+m+" min "+s;
        }
      }
      else if (t!=0) t=t+" min "+s;
      else t=s;

      var tr=document.createElement("tr");
      var tdA=document.createElement("td");
      var tdT=document.createElement("td");
      tdA.append(a);
      tdT.append(t);
      tr.appendChild(tdA);
      tr.appendChild(tdT);
      tbody.appendChild(tr);
    }
    table.appendChild(tbody);
    div.appendChild(table);

  }
}

//for sms, fetch all the contact numbers
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
          listContact=[];
          $.when(fetchContactList()).done(function(result){
            if (result!=undefined){
              jq_json_obj_1 = $.parseJSON(result); //Convert the JSON object to jQuery-compat$

              if(typeof jq_json_obj_1 == 'object') { //Test if variable is a [JSON] object
                jq_obj_1 = eval (jq_json_obj_1);

                //Convert back to an array
                jq_array_1 = [];
                for(elem in jq_obj_1){
                  jq_array_1.push(jq_obj_1[elem]);
                }
                listContact=jq_array_1;
              }
            }
            if (listContact!=null){
              for(elem in jq_obj){
                for (var i=0;i<listContact.length;i++){
                  if (jq_obj[elem][0]==listContact[i][1]) {
                    jq_obj[elem][1]=listContact[i][0];
                  }
                }
                jq_array.push(jq_obj[elem]);
            	}
           		addContactToSmsContainer(jq_array);
              document.getElementsByClassName('active')[0].click();
            }
          });
      	}
      }
    }
  });
}

//for sms, Add contact numbers to HTML
function addContactToSmsContainer(data){

  for (var i=0;i<data.length;i++){

    var row=data[i];
    var newP=document.createElement("p");
    if (row.length>1){
      var text=document.createTextNode(row[1]);
    }
    else {
      var text=document.createTextNode(row[0]);
    }
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

//for sms, fetch all messages from a contact number
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

//for sms, add messages from a contact number to HTML
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
  var msg = $(".b-r")[1];
  msg.scrollTop = msg.scrollHeight;
}

//Manage the nav bar containing the monitored information
function openTabs(e,div,bool) {
	$("#monitoredContent").children().hide();
  $(div).show();
  if (bool){
    $("#mobile-demo button").css("background-color","initial");
  }
  else {
    $(".tab button").css("background-color","initial");
    e.currentTarget.style="background-color: #ccc;";
  }
}


</script>
</html>
