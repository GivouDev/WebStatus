<?php
session_start();
if(file_exists("../config.php")) {
  header("Location: ../index.php");
  die();
}
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link type="text/css" rel="stylesheet" href="style.css" />

  <title>WebStatus - Simplesetup</title>
</head>
<body>
  <div class="center">
    <form method="POST" action="index.php">
      <h2 class="text">Setup</h2>
      <?php
        if(isset($_GET['mysqlerror'])) {
          echo '<p class="error">MySQL Connection failed, please check your inputs!</p>';
        }

        if(isset($_GET['passwordnotmatch'])) {
          echo '<p class="error">Passwords do not match!</p>';
        }
       ?>
      <p class="subtext">Website Settings:</p>
      <input type="text" class="" placeholder="Websitetitle" name="websitename" required>

      <p class="subtext">Database Setup:</p>
      <input type="text" placeholder="Host" name="dbhost" required>
      <input type="text" placeholder="Databasename" name="dbname" required>
      <input type="text" placeholder="User" name="dbuser" required>
      <input type="password" placeholder="Password" name="dbpwd" required>

      <p class="subtext">Administrative User:</p>
      <input type="text" placeholder="Username" name="username" required>
      <input type="password" placeholder="Password" name="password" required>
      <input type="password" placeholder="repeat password" name="password2" required>

      <input class="button-green" value="Submit" type="submit">
    </form>
  </div>
</body>
</html>


<?php

if(isset($_POST['dbhost']) && isset($_POST['dbname']) && isset($_POST['dbpwd']) && isset($_POST['dbuser'])
  && isset($_POST['websitename'])  && isset($_POST['username'])  && isset($_POST['password'])  && isset($_POST['password2'])) {
  if(!file_exists("../config.php")) {
    //Inputs read in, check inputs, start infliating database
    $instancename = $_POST['websitename'];

    $dbhost = $_POST['dbhost'];
    $dbname = $_POST['dbname'];
    $dbpwd = $_POST['dbpwd'];
    $dbuser = $_POST['dbuser'];

    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];
    $admin_password2 = $_POST['password2'];
    $admin_password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    //Check if passwords of the administrative user match
    if(strcmp($admin_password, $admin_password2)) {
      header("Location: index.php?passwordnotmatch");
      die();
    }

    $con = mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname) OR header("Location: index.php?mysqlerror") && die();

    //Creating Tables
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS accounts(id int(255) PRIMARY KEY AUTO_INCREMENT, username varchar(255), password varchar(255), role varchar(255));");
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS groups(id int(255) PRIMARY KEY AUTO_INCREMENT, priority int(255), name varchar(255));");
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS incidents(id int(255) PRIMARY KEY AUTO_INCREMENT, status varchar(255), services varchar(255), text varchar(255), date varchar(255), childof int(255));");
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS services(id int(255) PRIMARY KEY AUTO_INCREMENT, priority int(255), groupid int(255), name varchar(255), status varchar(255), cmaintenance int(255), sname varchar(255));");
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS settings(type varchar(255), value varchar(255));");

    //Infliating Settings
    mysqli_query($con, "INSERT INTO settings(type, value) VALUES ('instancename', '".mysqli_real_escape_string($con,$instancename)."')");
    mysqli_query($con, "INSERT INTO settings(type, value) VALUES ('autoreload', 'enabled')");
    mysqli_query($con, "INSERT INTO settings(type, value) VALUES ('impressum', '#')");
    mysqli_query($con, "INSERT INTO settings(type, value) VALUES ('privacy', '#')");
    mysqli_query($con, "INSERT INTO settings(type, value) VALUES ('newtab', 'same tab')");

    //Creating Administrative User
    mysqli_query($con, "INSERT INTO accounts (username, password, role) VALUES ('".mysqli_real_escape_string($con,$admin_username)."',
       '".mysqli_real_escape_string($con,$admin_password_hash)."', 'administrator')");

    //Create Config

    if($con != null) {
      $config = ('<?php
        $config=array(
          "DBHOST"=>"'.$dbhost.'",
          "DBNAME"=>"'.$dbname.'",
          "DBPWD"=>"'.$dbpwd.'",
          "DBUSER"=>"'.$dbuser.'"
        );
      ?>');
      file_put_contents("../config.php",$config);

      header("Location: ../admin.php");
    }

    //Setup File already exists
  } else {
    header("Location: ../index.php");
    die();
  }
}
 ?>
