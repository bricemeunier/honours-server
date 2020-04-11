<?php

include '../DatabaseConfig.php' ;

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
        header('Location: login.php');
        exit();
}

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);

// If there is an error with the connection, stop the script and display the error.
 if ( mysqli_connect_errno() ) {
   http_response_code(500);
 }

 $key=$_SESSION['key'];

 if ($stmt = $con->prepare('select date,address,duration,type from callLogs where idUser=? ORDER BY date desc')){
   // Bind parameters to avoid sql injection
   $stmt->bind_param('s',$key);
   $stmt->execute();
   // Store the result so we can check if the account exists in the database.
   $stmt->store_result();

   //check if there are data for this period of time
   if ($stmt->num_rows > 0) {

     $res="";
     $data=[];
     $stmt->bind_result($date,$address,$duration,$type);

     while ($stmt->fetch()){

       //convert datestamp to date format
       $date=date("d/m/Y H:i:s",($date+3600000)/1000);

       //convert duration to hh:mm:ss
       if (intval($duration)>0) {

         $hour=floor(intval($duration)/3600);
         $min=floor((intval($duration)-$hour*3600)/60);
         $sec=intval($duration)-$hour*3600-$min*60;
         $duration="";
         if ($hour>0) $duration.=$hour." h ";
         if ($min>0) {
           if ($min >9) $duration.=$min." min ";
           else $duration.="0".$min." min ";
         }
         elseif ($min==0 && $hour >0) $duration.="00 min ";
         if ($sec>9) $duration.=$sec." s";
         elseif ($sec>0) $duration.="0".$sec." s";
         else $duration.="00 s";
       }
       else $duration="";

       //convert type from number to "missed call" etc
       if ($type=="1"){
         $type="Incoming call";
       }
       elseif ($type=="2") {
         $type="Outgoing call";
       }
       else {
         $type="Missed call";
       }
       array_push($data,array($date,array($address),$duration,$type));
     }

     echo json_encode($data);
     $stmt->close();
   }
   else {
     http_response_code(204);
     $stmt->close();
   }
 }

?>
