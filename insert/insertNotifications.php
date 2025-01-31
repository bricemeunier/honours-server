<?php
//function insert a notification when sms raised a warning flag
function insertSmsNotification($key,$address,$message,$action) {

  include '../DatabaseConfig.php' ;
  $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

  $element="sms";
  $res="Warning on ";
  if ($action==0) $res.="message received by ";
  else $res.="message sent to ";
  if (strlen($message)>80) {
    $message= substr($message, 0, 80)." ...";
  }
  $res.=$address." : ".$message;

  if ($stmt = $con->prepare('INSERT INTO notification (idUser,text,element) values (?,?,?)')){
  	$stmt->bind_param('sss',$key,$res,$element);
  	$stmt->execute();
    $stmt->close();
  }
}

//function check if a new notification has to be created with the new hourly app usage
function checkAppNotification($key,$timePeriod,$application,$timeUsed){


  include '../DatabaseConfig.php' ;
  $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);


  $today = date('m/d/Y');
  $todayBeginning=strtotime($today)*1000-1;
  $todayEnd=strtotime($today)*1000+86400000;

  if ($timePeriod>$todayBeginning && $timePeriod<$todayEnd) {

    if ($stmt = $con->prepare('select app,SUM(timeUsed) from usageStat where idUser=? AND timePeriod > ? AND timeUsed > "0" group by app')){
      // Bind parameters to avoid sql injection
      $stmt->bind_param('ss',$key,$todayBeginning);
      $stmt->execute();
      // Store the result so we can check if the account exists in the database.
      $stmt->store_result();

      //check if there are data for this period of time
      if ($stmt->num_rows > 0) {

        $res="";
      	$data=[];
        $stmt->bind_result($app,$tu);
        $total_time=0;
        $total_app_time=0;
      	while ($stmt->fetch()){

          $total_time+=$tu;
          if ($app==$application) {
            $total_app_time=$tu;
          }

      	}

        if (floor($total_time/3600)<floor(($total_time+$timeUsed)/3600)) {
          $total_time=floor(($total_time+$timeUsed)/3600);
          if ($total_time>1) $res="Daily alert: The phone has been used more than ".$total_time." hours";
          else $res="Daily alert: The phone has been used more than ".$total_time." hour";

          insertAppNotification($key,$res);
        }

        if (floor($total_app_time/1800) < floor(($total_app_time+$timeUsed)/1800) && floor($total_app_time/1800)<2){
          $total_app_time=floor(($total_app_time+$timeUsed)/1800);
          if ($total_app_time==1) $res="Daily alert: ".$application." has been used more than 30 minutes";
          else $res="Daily alert: ".$application." has been used more than 1 hour";

          insertAppNotification($key,$res);
        }
      }
      $stmt->close();
    }
  }
}

//function insert a notification about app and phone usage
function insertAppNotification($key,$text) {

  include '../DatabaseConfig.php' ;
  $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

  $element="app";

  if ($stmt = $con->prepare('INSERT INTO notification (idUser,text,element) values (?,?,?)')){
  	$stmt->bind_param('sss',$key,$text,$element);
  	$stmt->execute();
    $stmt->close();
  }
}

?>
