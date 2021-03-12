<?php
session_start();
include('config.php');
$con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
if ( mysqli_connect_errno() ) {
    exit('MySQl Connection failed with error: ' . mysqli_connect_error());
}

$status = mysqli_query($con, "SELECT id,status FROM services ORDER BY priority DESC");
$overall;

$jsonObj = new stdClass();

while($statuses = mysqli_fetch_array($status)){
  if(strcmp($statuses['status'], "Offline") == 0) {
    $overall = "offline";
  } else if((strcmp($statuses['status'], "Maintenance") == 0) && !$overall == "offline"){
    $overall = "maintenance";
  }

  $id = $statuses['id'];
  $jsonObj->$id = $statuses['status'];
}

switch($overall) {
  case "online":
    $jsonObj->overall = "Online";
    break;

  case "maintenance":
    $jsonObj->overall = "Maintenance";
    break;

  case "offline":
    $jsonObj->overall = "Offline";
    break;

  default:
    $jsonObj->overall = "Online";
    break;
}

$json = json_encode($jsonObj);
echo $json;
 ?>
