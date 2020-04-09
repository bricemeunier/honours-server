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
$notif=[];
$totalUnseen=0;
$datetmp = date('m/d/Y');
$today=strtotime($datetmp)*1000-7200000;
$month=date('m',strtotime($datetmp));
$day=date('d',strtotime($datetmp));


if ($stmt = $con->prepare('select id,date,text,element,seen from notification where idUser=? order by date desc')){

  // Bind parameters to avoid sql injection
  $stmt->bind_param('s',$key);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
  	$stmt->bind_result($id,$date,$text,$elem,$seen);
  	while ($temp = $stmt->fetch()){
      if ($seen==0) $totalUnseen+=1;
      if (date("m",strtotime($date))==$month && date("d",strtotime($date))==$day) {
        $date=date("H:i",strtotime($date));
      }
      else $date=date("d/m/Y H:i",strtotime($date));

      array_push($notif,array($id,$date,$text,$elem,$seen));
  	}

    $result=[$totalUnseen];
    array_push($result,$notif);
    echo json_encode($result);
  }
  else {
    http_response_code(204);
  }
}
else {
  http_response_code(500);
}

?>
