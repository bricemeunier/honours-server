<?php

include '../DatabaseConfig.php' ;

 $con = mysqli_connect($HostName,$HostUser,$HostPass,$DatabaseName);
 $key = $_POST['key'];
 $phone = $_POST['phone'];
 $name = $_POST['name'];

 if ($stmt = $con->prepare('select phone,name from contact where idUser=? AND phone=? OR name =?')){
  $stmt->bind_param('sss',$key,$phone,$name);
  $stmt->execute();
  $stmt->store_result();
	if ($stmt->num_rows > 0) {
		$stmt->bind_result($p,$n);
		$stmt->fetch();
    if ($n!=$name){
      if ($stmt1 = $con->prepare('UPDATE contact SET name=? WHERE phone=? AND idUser=?')){
      	$stmt1->bind_param('sss',$name,$phone,$key);
      	$stmt1->execute();
     	  $stmt1->close();
      }
    }
    else {
      if ($stmt1 = $con->prepare('UPDATE contact SET phone=? WHERE phone=? AND idUser=?')){
      	$stmt1->bind_param('sss',$phone,$p,$key);
      	$stmt1->execute();
     	  $stmt1->close();
      }
    }
  }
  else {
    if ($stmt1 = $con->prepare('INSERT INTO contact (idUser,phone,name) values (?,?,?)')){
    	$stmt1->bind_param('sss',$key,$phone,$name);
    	$stmt1->execute();
   	  $stmt1->close();
    }
  }
   $stmt->close();
 }
?>
