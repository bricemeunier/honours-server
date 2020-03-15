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
$d1=$_GET['date'];
$d2=$d1+86400000;
$d1= date('Y-m-d H:i:s', $d1/1000);
$d2= date('Y-m-d H:i:s', $d2/1000);

if ($stmt = $con->prepare('select date,lat,lon from location where idUser=? AND date > ? AND date < ? order by date,lat,lon')){
  // Bind parameters to avoid sql injection
  $stmt->bind_param('sss',$key,$d1,$d2);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();
  //check if there are data for this period of time
  if ($stmt->num_rows > 0){

  	$data=[];
    $res=[];
    $previousLat="";
    $previousLon="";

  	$stmt->bind_result($date,$lat,$lon);
    //fetching data and regrouping them in common hours

    $stmt->fetch();

    $tmp=[];
    $tmp2=[];
    $latBelow=$lat-0.00015;
    $latAbove=$lat+0.00015;
    $lonBelow=$lon-0.00015;
    $lonAbove=$lon+0.00015;

    $date= date('H:i', strtotime($date));
    $firstElem=array($date,$lat,$lon);
    array_push($tmp,$firstElem);

  	while ($stmt->fetch()){
      $date= date('H:i', strtotime($date));
      array_push($data,array($date,$lat,$lon));
  	}
    $o=0;
    foreach ($data as $elem) {

      $d=$elem[0];
      $la=$elem[1];
      $lo=$elem[2];

      if ($la>$latBelow && $la<$latAbove && $lo>$lonBelow && $lo<$lonAbove){
        array_push($tmp,array($d,$la,$lo));
      }
      else {
        //$tmpLat=$latBelow+0.00015;
        //$tmpLon=$lonBelow+0.00015;

        $latBelow=$la-0.00015;
        $latAbove=$la+0.00015;
        $lonBelow=$lo-0.00015;
        $lonAbove=$lo+0.00015;

        if (sizeof($tmp)>1){
          array_push($tmp2,array(array($tmp[0][1],$tmp[0][2]),"From ".$tmp[0][0]." to ".$tmp[sizeof($tmp)-1][0]));
        }
        else {
          array_push($tmp2,array(array($tmp[0][1],$tmp[0][2]),"At ".$tmp[0][0]));
        }
        $tmp=[array($d,$la,$lo)];

      }
    }

    if (sizeof($tmp)>1){
      array_push($tmp2,array(array($tmp[0][1],$tmp[0][2]),"From ".$tmp[0][0]." to ".$tmp[sizeof($tmp)-1][0]));
    }
    else {
      array_push($tmp2,array(array($tmp[0][1],$tmp[0][2]),"At ".$tmp[0][0]));
    }

    $res=[];
    for ($i=0;$i<sizeof($tmp2);$i++){
      if ($tmp2[$i]!=null){
        $latBelow=$tmp2[$i][0][0]-0.00015;
        $latAbove=$tmp2[$i][0][0]+0.00015;
        $lonBelow=$tmp2[$i][0][1]-0.00015;
        $lonAbove=$tmp2[$i][0][1]+0.00015;
        $resultStr=[$tmp2[$i][1]];

        for ($j=$i+1;$j<sizeof($tmp2);$j++){
          if ($tmp2[$j]!=null){
            $la=$tmp2[$j][0][0];
            $lo=$tmp2[$j][0][1];
            $s=$tmp2[$j][1];
            if ($la>$latBelow && $la<$latAbove && $lo>$lonBelow && $lo<$lonAbove){
              array_push($resultStr,$s);
              $tmp2[$j]=null;
            }
          }
        }

        array_push($res,array(array($tmp2[$i][0][0],$tmp2[$i][0][1]),$resultStr));

      }
    }

  	echo json_encode($res);
  }
  else {
    http_response_code(204);
  }
}
else {
  http_response_code(404);
}
?>
