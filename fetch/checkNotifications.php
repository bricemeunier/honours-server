<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
  header('Location: index.html');
  exit();
}


include '../DatabaseConfig.php' ;

$con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

// If there is an error with the connection, stop the script and display the error.
 if ( mysqli_connect_errno() ) {
   http_response_code(500);
 }

$key=$_SESSION['key'];
$date = date_create();
$datems=date_timestamp_get($date)*1000-259200000;
$notif=[];
$datetmp = date('m/d/Y');
$today=date(date('m',strtotime($datetmp))."/".date('d',strtotime($datetmp))."/".date('Y',strtotime($datetmp)));
$today=strtotime($today)*1000;


if ($stmt = $con->prepare('select action,address,message,date from sms where idUser=? AND warning="1" AND date>? order by date desc')){
  // Bind parameters to avoid sql injection
  $stmt->bind_param('ss',$key,$datems);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();
	$stmt->bind_result($action,$address,$message,$date);
	while ($temp = $stmt->fetch()){
    $date=date("d/m H:i",($date+7200000)/1000);
    $res=$date." Warning on ";
    if ($action==0) $res.="message received by ";
    else $res.="message sent to ";
    if (strlen($message)>80) {
      $message= substr($message, 0, 80)." ...";
    }
    $res.=$address." : ".$message;
    array_push($notif,array($res,"smsButton"));
	}
}
else {
  http_response_code(500);
}


if ($stmt = $con->prepare('select app,SUM(timeUsed) from usageStat where idUser=? AND timePeriod > ? AND timeUsed > "0" group by app')){
  // Bind parameters to avoid sql injection
  $stmt->bind_param('ss',$key,$today);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();

  //check if there are data for this period of time
  if ($stmt->num_rows > 0) {

  	$data=[];
    $stmt->bind_result($app,$tu);
    $total_time=0;
  	while ($stmt->fetch()){
      $total_time+=$tu;
      if ($tu>1800){
        $s=$app." has been used more than 30 minutes today";
        array_push($data,array($s,"appButton"));
      }
  	}
    if ($total_time>3600) {
      $total_time=floor($total_time/3600);
      if ($total_time>1) array_push($notif,array("The phone has been used more than ".$total_time." hours today","appButton"));
      else array_push($notif,array("The phone has been used more than ".$total_time." hour today","appButton"));

    }

    $notif=array_merge($notif,$data);
  }
  else {
    http_response_code(204);
  }
}
else {
  http_response_code(500);
}

echo json_encode($notif);

?>
