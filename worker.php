<?php
//Check if user is logged in
  session_start();

  if(!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    die;
  }

	if(isset($_GET["logout"])) {
    session_destroy();
    header('Location: login.php');
  }
?>

<?php
//Get datas from database
include('config.php');
$con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
if ( mysqli_connect_errno() ) {
    exit('MySQl Connection failed with error: ' . mysqli_connect_error());
}

$role = mysqli_fetch_assoc(mysqli_query($con, "SELECT role FROM accounts WHERE id='".mysqli_real_escape_string($con, $_SESSION['id'])."'"))["role"];
$instancename = mysqli_fetch_assoc(mysqli_query($con, "SELECT value FROM settings WHERE type='instancename'"))["value"];

if($role == "administrator") {
  $autoreload_setting = mysqli_fetch_assoc(mysqli_query($con, "SELECT value FROM settings WHERE type='autoreload'"))["value"];
  $modular_setting = mysqli_fetch_assoc(mysqli_query($con, "SELECT value FROM settings WHERE type='modularwindow'"))["value"];
}

?>

<?php
//Define all users in a $users variable
if($role == "administrator") {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  $users = mysqli_query($con, "SELECT username,id,role FROM accounts");
}
?>

<?php
//Define all groups and services in a $groups and $services variable
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  $groups = mysqli_query($con, "SELECT priority, name, id FROM groups ORDER BY priority DESC");
  $services = mysqli_query($con, "SELECT priority, groupid, name, id, status FROM services ORDER BY priority DESC");
  $incidents = mysqli_query($con, "SELECT id, status, services, text, date, childof FROM incidents ORDER BY id ASC");
  $incidents2 = mysqli_query($con, "SELECT id, status, services, text, date, childof FROM incidents ORDER BY id ASC");

?>

<?php
//Update Username for user
if(isset($_GET["updateusername"]) && isset($_POST['username'])) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
      exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }



  $query="SELECT * FROM accounts WHERE username='".mysqli_real_escape_string($con,$_POST['username'])."'";
  if($result=mysqli_query($con,$query)){
    if(mysqli_num_rows($result)>0){
      header("Location: admin.php?userexist#account");
    }else{
      $statement =   mysqli_query($con,"UPDATE accounts SET username='".mysqli_real_escape_string($con,$_POST['username'])."' WHERE ID='".$_SESSION['id']."'");
        header('Location: worker.php?logout');
    }
  }
}
?>

<?php
//Update Password for user
if(isset($_GET["updatepassword"]) && isset($_POST['password1']) && isset($_POST['password2'])) {

  include('config.php');

if($_POST['password1'] == $_POST['password2']) {

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }


  if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
          $stmt->bind_param('s', $_SESSION['name']);
          $stmt->execute();

          $stmt->store_result();

          if ($stmt->num_rows > 0) {
                  $stmt->bind_result($id, $password);
                  $stmt->fetch();

                  if (password_verify($_POST['oldpassword'], $password)) {
                    $npassword = password_hash($_POST['password1'], PASSWORD_DEFAULT);
                    $statement = mysqli_query($con,"UPDATE accounts SET password='".mysqli_real_escape_string($con,$npassword)."' WHERE ID='".$_SESSION['id']."'");
                    if($statement) {
                      header("Location: admin.php?pwupdated#account");
                    }
                  } else {
                    header("Location: admin.php?pwrong#account");
                  }

          } else {
            header("Location: admin.php?pwrong#account");
          }

          $stmt->close();
  }

  } else {
    header("Location: admin.php?pwnotmatch#account");
  }
}
?>

<?php
//Create a new user
if($role == "administrator") {
  if(isset($_GET["newuser"]) && isset($_POST['username']) && isset($_POST['password'])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
      }

      $query="SELECT * FROM accounts WHERE username='".mysqli_real_escape_string($con,$_POST['username'])."'";
      if($result=mysqli_query($con,$query)){
        if(mysqli_num_rows($result)>0){
          header("Location: admin.php?userexist#users");
        }else{
          $newpw = password_hash($_POST['password'], PASSWORD_DEFAULT);

          $statement = mysqli_query($con, "INSERT INTO accounts (username, password, role) VALUES ('".mysqli_real_escape_string($con,$_POST['username'])."',
             '".mysqli_real_escape_string($con,$newpw)."', '".mysqli_real_escape_string($con, $_POST['role'])."')");
          if($statement) {
            header("Location: admin.php?usercreated#users");
          }
        }
      }
  }
}
?>

<?php
//Update password for another user
if($role == "administrator") {
  if(isset($_GET["updatepwfor"]) && isset($_POST['password1']) && isset($_POST['password2'])) {


    include('config.php');

    if($_POST['password1'] == $_POST['password2']) {

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

      $npassword = password_hash($_POST['password1'], PASSWORD_DEFAULT);
      $statement = mysqli_query($con,"UPDATE accounts SET password='".mysqli_real_escape_string($con,$npassword)."'
        WHERE username='".mysqli_real_escape_string($con,$_GET["updatepwfor"])."'");

      if($statement) {
  	     header("Location: admin.php?edit=".$_GET["updatepwfor"]."&pwupdated#edit");
      }
    } else {
      header("Location: admin.php?edit=".$_GET["updatepwfor"]."&pwnotmatch#edit");
    }
  }
}
 ?>

<?php
//Update username for another user
if($role == "administrator") {
 if(isset($_GET["updateunfor"]) && isset($_POST['username'])) {


   include('config.php');

   $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
   if ( mysqli_connect_errno() ) {
         exit('MySQl Connection failed with error: ' . mysqli_connect_error());
   }

    $statement = mysqli_query($con,"UPDATE accounts SET username='".mysqli_real_escape_string($con,$_POST['username'])."'
      WHERE username='".mysqli_real_escape_string($con, $_GET["updateunfor"])."'");

    if($statement) {
 	    header("Location: admin.php?edit=".$_POST['username']."&unupdated#edit");
    }
 }
}
  ?>

<?php
//Update role for some user
if($role == "administrator") {
  if(isset($_GET["updaterole"])) {


    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    if($_POST['role'] == "administrator") {
      $statement = mysqli_query($con,"UPDATE accounts SET role='".mysqli_real_escape_string($con, $_POST['role'])."'
        WHERE username='".mysqli_real_escape_string($con, $_GET["updaterole"])."'");

      if($statement) {
        header("Location: admin.php?edit=".$_GET["updaterole"]."&roleupdated#edit");
      }
    } else if($_POST['role'] == "moderator") {
      $statement = mysqli_query($con,"UPDATE accounts SET role='".mysqli_real_escape_string($con, $_POST['role'])."'
        WHERE username='".mysqli_real_escape_string($con, $_GET["updaterole"])."'");

      if($statement) {
        header("Location: admin.php?edit=".$_GET["updaterole"]."&roleupdated#edit");
      }
    }
  }
}
?>

<?php
//Deleter user
  if($role == "administrator") {
    if(isset($_GET["deluser"])) {
      include('config.php');

      $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
      if ( mysqli_connect_errno() ) {
            exit('MySQl Connection failed with error: ' . mysqli_connect_error());
      }

      $statement = mysqli_query($con, "DELETE FROM accounts WHERE username='".mysqli_real_escape_string($con, $_GET["deluser"])."'");
      if($statement) {
        header("Location: admin.php?userdeleted#users");
      }
    }
  }
?>

<?php
//Change instance name
  if($role == "administrator") {
    if(isset($_GET["upname"]) && isset($_POST['iname'])) {
      include ('config.php');

      $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
      if ( mysqli_connect_errno() ) {
            exit('MySQl Connection failed with error: ' . mysqli_connect_error());
      }

      $statement = mysqli_query($con,"UPDATE settings SET value='".mysqli_real_escape_string($con, $_POST['iname'])."'
        WHERE type='instancename'");

      if($statement) {
        header("Location: admin.php?nameupdated#settings");
      }
    }
  }
 ?>

 <?php
 //Change autoreload setting
   if($role == "administrator") {
     if(isset($_GET["upautoreload"]) && isset($_POST['autoreload'])) {
       include ('config.php');

       $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
       if ( mysqli_connect_errno() ) {
             exit('MySQl Connection failed with error: ' . mysqli_connect_error());
       }

       $statement = mysqli_query($con,"UPDATE settings SET value='".mysqli_real_escape_string($con, $_POST['autoreload'])."'
         WHERE type='autoreload'");

       if($statement) {
         header("Location: admin.php?reloadupdated=".$_POST['autoreload']."#settings");
       }
     }
   }
  ?>

   <?php
   //Create new service
     if(isset($_GET["createservice"]) && isset($_POST['priority']) && isset($_POST['group']) && isset($_POST['name'])) {
       include ('config.php');

       $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
       if ( mysqli_connect_errno() ) {
             exit('MySQl Connection failed with error: ' . mysqli_connect_error());
       }

        $statement = mysqli_query($con, "INSERT INTO services (priority, groupid, name, status, cmaintenance, sname) VALUES ('".mysqli_real_escape_string($con,$_POST['priority'])."',
          '".mysqli_real_escape_string($con,$_POST['group'])."', '".mysqli_real_escape_string($con, $_POST['name'])."', 'Online', '0',
           '".mysqli_real_escape_string($con, $_POST['sname'])."')");

          if($statement) {
            header("Location: admin.php?serviceadded#services");
          }
      }
   ?>

 <?php
    //Create new group

    if(isset($_GET["creategroup"]) && isset($_POST['priority']) && isset($_POST['name'])) {
      include ('config.php');

      $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
      if ( mysqli_connect_errno() ) {
            exit('MySQl Connection failed with error: ' . mysqli_connect_error());
      }

      $statement = mysqli_query($con, "INSERT INTO groups (priority, name) VALUES ('".mysqli_real_escape_string($con,$_POST['priority'])."',
        '".mysqli_real_escape_string($con, $_POST['name'])."')");

      if($statement) {
        header("Location: admin.php?groupadded#services");
      }
    }
?>

<?php
//Delete service
  if(isset($_GET["delservice"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "DELETE FROM services WHERE id='".mysqli_real_escape_string($con, $_GET["delservice"])."'");
    if($statement) {
      header("Location: admin.php?servicedeleted#services");
    }
  }
?>

<?php
//Delete group
  if(isset($_GET["delgroup"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "DELETE FROM groups WHERE id='".mysqli_real_escape_string($con, $_GET['delgroup'])."'");
    $statement2 = mysqli_query($con, "DELETE FROM services WHERE groupid='".mysqli_real_escape_string($con, $_GET['delgroup'])."'");
    if($statement) {
      header("Location: admin.php?groupdeleted#services");
    }
  }
?>

<?php
//Update Service Status
  if(isset($_GET["updatest"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "UPDATE services SET status = '".mysqli_real_escape_string($con, $_GET['status'])."'
     WHERE id = '".mysqli_real_escape_string($con, $_GET['updatest'])."'");

    if($statement) {
      header("Location: admin.php?manageservice=".$_GET['updatest']."&statusupdated#mservice");
    }
  }
?>

<?php
//Create Incident
  if(isset($_GET["createincident"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $services = "".implode(",",$_POST['services'])."";

    $statement = mysqli_query($con, "INSERT INTO incidents (status, services, text, date, childof) VALUES ('new',
      '".mysqli_real_escape_string($con, $services)."', '".mysqli_real_escape_string($con, $_POST['text'])."', '".mysqli_real_escape_string($con, $_POST['date'])."'
    , '-1')");

    if($statement) {
      header("Location: admin.php?incidentcreated#incidents");
    } else {
    }
  }
?>

<?php
//Create Incident Message
  if(isset($_GET["addmessage"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "INSERT INTO incidents (status, services, text, date, childof) VALUES ('".mysqli_real_escape_string($con, $_POST['state'])."',
      '-1', '".mysqli_real_escape_string($con, $_POST['text'])."', '2021-02-02'
    , '".mysqli_real_escape_string($con, $_POST['childof'])."')");

    if($statement) {
      header("Location: admin.php?messagecreated#incidents");
    }
  }
?>

<?php
//Delete Incident
  if(isset($_GET["rmincident"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "DELETE FROM incidents WHERE id='".mysqli_real_escape_string($con, $_GET['rmincident'])."'");
    $statement2 = mysqli_query($con, "DELETE FROM incidents WHERE childof='".mysqli_real_escape_string($con, $_GET['rmincident'])."'");
    if($statement) {
      header("Location: admin.php?incidentdeleted#incidents");
    }
  }
?>

<?php
//Flush Incidents
  if(isset($_GET["flushincidents"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "DELETE FROM incidents");
    if($statement) {
      header("Location: admin.php?incidentsflushed#incidents");
    } else {
      die("no!");
    }
  }
?>

<?php
//Update Group Name
  if(isset($_GET["upgroupname"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "UPDATE groups SET name = '".mysqli_real_escape_string($con, $_POST['name'])."'
     WHERE id = '".mysqli_real_escape_string($con, $_GET['upgroupname'])."'");

    if($statement) {
      header("Location: admin.php?editgroup=".$_GET['upgroupname']."&nameupdated#groupedit");
    }
  }
?>

<?php
//Update Group Priority
  if(isset($_GET["upgrouppriority"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "UPDATE groups SET priority = '".mysqli_real_escape_string($con, $_POST['priority'])."'
     WHERE id = '".mysqli_real_escape_string($con, $_GET['upgrouppriority'])."'");

    if($statement) {
      header("Location: admin.php?editgroup=".$_GET['upgrouppriority']."&priorityupdated#groupedit");
    }
  }
?>

<?php
//Update Service Priority
  if(isset($_GET["upservicepriority"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "UPDATE services SET priority = '".mysqli_real_escape_string($con, $_POST['priority'])."'
     WHERE id = '".mysqli_real_escape_string($con, $_GET['upservicepriority'])."'");

    if($statement) {
      header("Location: admin.php?manageservice=".$_GET['upservicepriority']."&priorityupdated#mservice");
    }
  }
?>

<?php
//Update Service Name
  if(isset($_GET["upservicename"])) {
    include('config.php');

    $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
    if ( mysqli_connect_errno() ) {
          exit('MySQl Connection failed with error: ' . mysqli_connect_error());
    }

    $statement = mysqli_query($con, "UPDATE services SET name = '".mysqli_real_escape_string($con, $_POST['name'])."'
     WHERE id = '".mysqli_real_escape_string($con, $_GET['upservicename'])."'");

    if($statement) {
      header("Location: admin.php?manageservice=".$_GET['upservicename']."&nameupdated#mservice");
    }
  }
?>

<?php
//Functions
function getStatus($id) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  return mysqli_fetch_assoc(mysqli_query($con, "SELECT status FROM services WHERE id='".mysqli_real_escape_string($con, $id)."'"))["status"];
}

function getGroupName($id) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  return mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM groups WHERE id='".mysqli_real_escape_string($con, $id)."'"))["name"];
}

function getGroupPriority($id) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  return mysqli_fetch_assoc(mysqli_query($con, "SELECT priority FROM groups WHERE id='".mysqli_real_escape_string($con, $id)."'"))["priority"];
}

function getServiceName($id) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  return mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM services WHERE id='".mysqli_real_escape_string($con, $id)."'"))["name"];
}

function getServicePriority($id) {
  include('config.php');

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  return mysqli_fetch_assoc(mysqli_query($con, "SELECT priority FROM services WHERE id='".mysqli_real_escape_string($con, $id)."'"))["priority"];
}

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

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

?>
