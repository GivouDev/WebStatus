<?php
function sMaintenance() {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  $services = mysqli_query($con, "SELECT priority, groupid, name, id, status FROM services ORDER BY priority DESC");

  $find = False;
  while($servicelist = mysqli_fetch_array($services)){
    if(strpos($servicelist['status'], "Maintenance") !== false) {
      $find = True;
    }
  }

  return $find;
}

function sOffline() {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  $services = mysqli_query($con, "SELECT priority, groupid, name, id, status FROM services ORDER BY priority DESC");

  $find = False;
  while($servicelist = mysqli_fetch_array($services)){
    if(strpos($servicelist['status'], "Offline") !== false) {
      $find = True;
    }
  }

  return $find;
}

function getName($id) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  return mysqli_fetch_assoc(mysqli_query($con, "SELECT sname FROM services WHERE id='".mysqli_real_escape_string($con, $id)."'"))["sname"];
}
?>
