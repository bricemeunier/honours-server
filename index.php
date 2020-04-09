<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
        exit();
}

?>
<!DOCTYPE html>
<html>
  <head>
		<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Monitoring Dashboard</title>
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
              <li id="notif-dropdown"><a class="dropdown-trigger" href="#!" data-target="notification">
                <i class="material-icons white-text notif">notifications</i>
                <small class="notification-badge"></small>
              </a></li>
              <li><a href="javascript:showProfileInfo();"><b><?php echo $_SESSION['name']; ?></b></a></li>
              <li><a href="logout.php"><b>Logout</b></a></li>
            </ul>
          </div>
        </nav>
        <ul class="sidenav" id="mobile-demo">
          <li><a href="#" class="brand-logo">Monitoring Dashboard</a></li>
          <li><a id="linkNotification" href="#"><b id="countOfNotification">Notification</b></a></li>
          <li id="locationButton-mobile"><a href="javascript:openTabs(null,'#locationButton');"><b>Location</b></a></li>
          <li id="smsButton-mobile"><a href="javascript:openTabs(null,'#smsButton');"><b>SMS</b></a></li>
          <li id="appButton-mobile"><a href="javascript:openTabs(null,'#appButton');"><b>App Usage</b></a></li>
          <li id="contactButton-mobile"><a href="javascript:openTabs(null,'#contactButton');"><b>Contact List</b></a></li>
          <li><a href="javascript:showProfileInfo();"><b>Profile</b></a></li>
          <li><a href="logout.php"><b>Logout</b></a></li>
        </ul>


        <!-- Dropdown Structure -->
        <ul id='notification' class='dropdown-content'>
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
          <div id="notificationContainer"></div>
          <div id="profileContainer">
    				<h5>Account details</h5>
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
    						<td id="longString"><?=$_SESSION['email']?></td>
    					</tr>
    				</table>
    			</div>
        </div>

      </div>
    </div>
  </body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="js/script.js" type="text/javascript">


</script>
</html>
