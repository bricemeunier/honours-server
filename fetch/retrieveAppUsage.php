<?php

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
        exit();
}

include '../DatabaseConfig.php' ;
$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

// If there is an error with the connection, stop the script and display the error.
 if ( mysqli_connect_errno() ) {
   http_response_code(500);
 }

$key=$_SESSION['key'];
$d1=$_GET['date']-1;
$d2=$d1+86400000;
$data=[];

if ($stmt = $con->prepare('select timePeriod,app,timeUsed from usageStat where idUser=? AND timePeriod > ? AND timePeriod < ? AND timeUsed > "0" order by timePeriod asc,convert(timeUsed,decimal) desc')){
  // Bind parameters to avoid sql injection
  $stmt->bind_param('sss',$key,$d1,$d2);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();

  //check if there are data for this period of time
  if ($stmt->num_rows > 0){

  	$data=[];
    $tempHour=[];
    $tmpTime="";

  	$stmt->bind_result($tp,$app,$tu);
    //fetching data and regrouping them in common hours
    $temp = $stmt->fetch();
    $date=date("d/m/Y H:i:s",$tp/1000);
    $tmpTime=$tp;
    $tmpCheckForError=[$app];
    array_push($tempHour,array($tp,$app,$tu));

  	while ($temp = $stmt->fetch()){

      if (abs($tp-$tmpTime)<1000 AND !(in_array($app,$tmpCheckForError))){
        $date=date("d/m/Y H:i:s",$tp/1000);
        array_push($tempHour,array($tp,$app,$tu));
        array_push($tmpCheckForError,$app);
      }
      elseif (abs($tp-$tmpTime)>999) {
        array_push($data,$tempHour);
        $tempHour=[];
        array_push($tempHour,array($tp,$app,$tu));
        $tmpTime=$tp;
        $tmpCheckForError=[$app];
      }
  	}
    array_push($data,$tempHour);

  	echo json_encode($data);
  }
  else {
    http_response_code(204);
  }
}
else {
  http_response_code(500);
}
?>
