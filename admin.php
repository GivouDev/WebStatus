<?php
  session_start();
  if(!file_exists("config.php")) {
    header("Location: install/index.php");
    die();
  }

  if(!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    die;
  }

  include('worker.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<link type="text/css" rel="stylesheet" href="css/admin/style.css" />
<link type="text/css" rel="stylesheet" href="css/admin/mobile.css" />
<link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">

<?php
  echo '<title>'.$instancename.' - Administration</title>';
 ?>
</head>

<body>
  <a id="opener" class="open" onclick="openNav()">☰</a>
  <!--SIDEBAR-->
  <div id="sidebar" class="sidebar">

     <h1><?=$_SESSION['name']?></h1>
     <a href="#dashboard">Dashboard</a>
     <a href="#account">Account</a>
     <?php
      if($role == "administrator") {
        echo '<a href="#users">Users</a>';
      }
      ?>
     <a href="#services">Services</a>
     <a href="#incidents">Incidents</a>
     <?php
      if($role == "administrator") {
        echo '<a href="#settings">Settings</a>';
      }
      ?>

     <a class="bottom close" onClick="closeNav()">Close</a>
     <a class="bottom" href="admin.php?logout">Logout</a>
  </div>



  <!--DASHBOARD-->
  <div id="dashboard" class="more-overlay">
    <div class="more-popup">
      <h2>Dashboard</h2>
      <div class="content">Welcome back, <font color="green"><?=$_SESSION['name']?></font></div>
      <?php
        if(sOffline()) {
          echo '<div class="content troubles">Some Systems may not work correctly!</div>';
        } else if(sMaintenance()) {
          echo '<div class="content maintenance">Systems currently under maintenance!</div>';
        } else {
          echo '<div class="content up">All Systems operational!</div>';
        }
        ?>
      </div>
  </div>

  <!--ACCOUNT-->
  <div id="account" class="more-overlay">
    <div class="more-popup">
      <h2>Account</h2>
      <div class="content">Account name: <font color="green"><?=$_SESSION['name']?></font></div>
      <?php
        if(isset($_GET["userexist"])) {
          echo '<p class="wpassword">User already exists!</p>';
        }
       ?>
      <form action="admin.php?updateusername" method="post">
        <input type="text" class="username-field" value="" placeholder="New Username" id="username" name="username" required>
        <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
      </form>

      <div class="placeholder"></div>

      <div class="content">Password</div>
      <?php
        if(isset($_GET["pwnotmatch"])) {
          echo '<p class="wpassword">The passwords do not match</p>';
        }

        if(isset($_GET["pwupdated"])) {
          echo '<p class="upassword">Passwort updated successfully!</p>';
        }

        if(isset($_GET["pwrong"])) {
          echo '<p class="wpassword">Old password is not correct!</p>';
        }
       ?>
      <form action="admin.php?updatepassword" method="post">
        <input type="password" class="username-field field-newline" value="" placeholder="Old password" id="oldpassword" name="oldpassword" required>
        <div class="placeholder"></div>
        <input type="password" class="username-field field-newline" value="" placeholder="Password" id="password1" name="password1" required>
        <input type="password" class="username-field" value="" placeholder="Retype password" id="password2" name="password2" required>
        <input style="display: none;" type="submit"><button class="button-green">Submit</button></input>
      </form>
      </div>
  </div>

  <!--USERS WHEN ADMIN PRIVILEGES ARE SET-->
<?php
   if($role == "administrator") {
     echo '
      <div id="users" class="more-overlay">
        <div class="more-popup">
          <h2>Users</h2>
            <div class="content">Create Account</div>
          <form action="admin.php?newuser" method="post">
            <input type="text" class="username-field" value="" placeholder="Username" id="username" name="username" required>
            <input type="password" class="username-field" value="" placeholder="Password" id="password" name="password" required>
            <select name = "role">
               <option value = "administrator" selected>Administrator</option>
               <option value = "moderator">Moderator</option>
            </select>
            <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
          </form>

          <div class="placeholder"></div>
          <div class="content">Users</div>
      ';

      if(isset($_GET["userdeleted"])) {
          echo '<p class="wpassword">User deleted successfully!</p>';
      }

      if(isset($_GET["userexist"])) {
          echo '<p class="wpassword">User already exists!</p>';
      }

      if(isset($_GET["usercreated"])) {
          echo '<p class="upassword">User created successfully!</p>';
      }
        }

      if($role == "administrator") {
        while($userlist = mysqli_fetch_array($users)){
          if($userlist['username'] != $_SESSION['name']) {
            echo '
              <input type="text" id="username" name="username" class="username-field" value="'.$userlist['username'].'" readonly>
              <input type="text" class="username-field" value="ID='.$userlist['id'].'" readonly>
              <input type="text" class="username-field" value="'.$userlist['role'].'" readonly>
              <a href="?edit='.$userlist['username'].'#edit"><button class="button-green">Edit</button></a>
              <a href="?deluser='.$userlist['username'].'#users"><button class="button-red">Delete</button></a>
              <div class="userlist-newline"></div>';
          }
        }
      }

      if($role == "administrator") {
           echo '</div></div>';
      }
?>
<!--USERS EDIT-->
<?php
if(isset($_GET["edit"]) && $role == "administrator") {
  $username = $_GET["edit"];
  echo '
    <div id="edit" class="more-overlay">
      <div class="more-popup">
        <h2>'.$username.'</h2>

        <form action="admin.php?updateunfor='.$username.'#edit" method="post">
          <div class="content">Username</div>';

          if(isset($_GET["unupdated"])) {
            echo '<p class="upassword">Username updated sucessfully</p>';
          }

          echo '
          <input type="text" class="username-field" value="" placeholder="'.$username.'" id="username" name="username" required>
          <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
        </form>

        <div class="placeholder"></div>

        <form action="admin.php?updatepwfor='.$username.'#edit" method="post">
          <div class="content">Password</div>';

          if(isset($_GET["pwnotmatch"])) {
            echo '<p class="wpassword">The passwords do not match</p>';
          }

          if(isset($_GET["pwupdated"])) {
            echo '<p class="upassword">Passwort updated successfully!</p>';
          }

          echo '
          <input type="password" class="username-field field-newline" value="" placeholder="Password" id="password1" name="password1" required>
          <input type="password" class="username-field" value="" placeholder="Retype password" id="password2" name="password2" required>
          <input style="display: none;" type="submit"><button class="button-green">Submit</button></input>
        </form>

        <div class="placeholder"></div>

        <form action="admin.php?updaterole='.$username.'#edit" method="post">
          <div class="content">Role</div>';

          if(isset($_GET["roleupdated"])) {
            echo '<p class="upassword">Role updated successfully!</p>';
          }

          echo '
          <select name = "role">
            <option value = "administrator" selected>Administrator</option>
            <option value = "moderator">Moderator</option>
          </select>
          <input style="display: none;" type="submit"><button class="button-green">Submit</button></input>
        </form>
      </div>
  </div>
  ';
}
?>

<!--Manage Service-->
<?php
if(isset($_GET["manageservice"])) {
  $id = $_GET["manageservice"];
  $servicename = getServiceName($_GET["manageservice"]);
  $incidentid = 0;

  echo '
  <div id="mservice" class="more-overlay">
    <div class="more-popup">
      <h2>'.$servicename.'</h2>

      <div class="content">Edit service settings</div>

      ';
      if(isset($_GET["statusupdated"])) {
        echo '<p class="upassword">Status updated successfully!</p>';
      }

      if(isset($_GET["priorityupdated"])) {
        echo '<p class="upassword">Priority updated successfully!</p>';
      }

      if(isset($_GET["nameupdated"])) {
        echo '<p class="upassword">Name updated successfully!</p>';
      }
      echo '

      <form action="admin.php">
      <input type="hidden" name="updatest" value="' . $id . '">


      <input type="text" class="username-field" value="'.getStatus($id).'" readonly>
        <select name = "status">
          <option value = "Online" selected>Online</option>
          <option value = "Maintenance">Maintenance</option>
          <option value = "Offline">Offline</option>
        </select>
        <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
      </form>

      <form action="admin.php?upservicename='.$id.'" method="post">
        <input type="text" class="username-field" value="'.$servicename.'" readonly>
        <input type="text" class="username-field" placeholder="New Name" id="name" name="name" required>
        <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
      </form>

      <form action="admin.php?upservicepriority='.$id.'" method="post">
        <input type="text" class="username-field" value="'.getServicePriority($id).'" readonly>
        <input type="number" class="username-field" placeholder="Number" placeholder="Priority" id="priority" name="priority" required>
        <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
      </form>

      <div class="placeholder"></div>
      <div class="content">Recent Incidents</div>
      ';

      mysqli_data_seek($incidents, 0);
      $count = 3;

      if(isset($_GET['lmore'])) {
        $add = (int) $_GET['lmore'];
        $count += $add;
      }


      while($incidentlist = mysqli_fetch_array($incidents)){
        if($count != 0) {
        if(strpos($incidentlist['services'], $id) !== false) {
          if(strpos($incidentlist['childof'], "-1") !== false) {
            $count--;
            echo '
            <div class="placeholder"></div>
              <div class="content">'.$incidentlist['date'].':
                <a href="?rmincident='.$incidentlist['id'].'&manageservice='.$id.'&sname='.$servicename.'#mservice"><button class="button-red-mini button-mini-bigger">Delete</button></a>
              </div>

              <div class="incident">';
                if($incidentlist['status'] == "new") {
                  echo '<div class="timeline-icon timeline-new"></div>';
                } else if($incidentlist['status'] == "working") {
                  echo '<div class="timeline-icon timeline-working"></div>';
                } else if($incidentlist['status'] == "update") {
                  echo '<div class="timeline-icon timeline-update"></div>';
                } else if($incidentlist['status'] == "finished") {
                  echo '<div class="timeline-icon timeline-finished"></div>';
                }

                echo ''.$incidentlist['text'].'
            </div>
            ';

            mysqli_data_seek($incidents2, 0);
            while($incidentlist2 = mysqli_fetch_array($incidents2)){
              if(strpos($incidentlist['id'], $incidentlist2['childof']) !== false) {
                echo '
                  <div class="incident">';
                    if($incidentlist2['status'] == "new") {
                      echo '<div class="timeline-icon timeline-new"></div>';
                    } else if($incidentlist2['status'] == "working") {
                      echo '<div class="timeline-icon timeline-working"></div>';
                    } else if($incidentlist2['status'] == "update") {
                      echo '<div class="timeline-icon timeline-update"></div>';
                    } else if($incidentlist2['status'] == "finished") {
                      echo '<div class="timeline-icon timeline-finished"></div>';
                    }

                    echo ''.$incidentlist2['text'].'
                    <a href="?rmincident='.$incidentlist2['id'].'&manageservice='.$id.'&sname='.$servicename.'#mservice"><button style="margin-top: 1px;" class="button-red-mini">Delete</button></a>
                </div>
                ';
              }
            }
          }
        }
      } else {
        break;
      }
    }

    if($count == 0) {
      if(isset($_GET['lmore'])) {
        $more = (int) $_GET['lmore'];
        $more += 3;
      } else {
        $more = 3;
      }

      echo '<a href="?manageservice='.$id.'&sname='.$servicename.'&lmore='.$more.'#mservice"><button class="button-center button-green button-bigger">Load more</button></a';
    }
      echo '
    </div>
  </div>
  ';
}
 ?>

 <!--Manage Group-->
 <div id="groupedit" class="more-overlay">
   <div class="more-popup">
     <h2><?php echo getGroupName($_GET['editgroup']); ?></h2>
     <div class="content">Edit Group</div>
     <?php
      if(isset($_GET["messagecreated"])) {
        echo '<p class="upassword">Name updated successfully!</p>';
      }

      if(isset($_GET["priorityupdated"])) {
        echo '<p class="upassword">Priority updated successfully!</p>';
      }
      ?>
     <?php echo '<form action="admin.php?upgroupname='.$_GET['editgroup'].'" method="post">' ?>
       <input type="text" class="username-field" value="<?php echo getGroupName($_GET['editgroup']); ?>" readonly>
       <input type="text" class="username-field" placeholder="New Name" id="name" name="name" required>
       <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
     </form>

     <?php echo '<form action="admin.php?upgrouppriority='.$_GET['editgroup'].'" method="post">' ?>
       <input type="text" class="username-field" value="<?php echo getGroupPriority($_GET['editgroup']); ?>" readonly>
       <input type="number" class="username-field" placeholder="Number" placeholder="Priority" id="priority" name="priority" required>
       <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
     </form>
 </div>
</div>

 <!--Create Incidents-->
 <div id="incidents" class="more-overlay">
   <div class="more-popup">
     <h2>Incidents</h2>
<?php
      echo '
      <div class="content">Create Incident</div>
      ';

      if(isset($_GET["incidentcreated"])) {
        echo '<p class="upassword">Incident created successfully!</p>';
      }

      echo '
      <form action="admin.php?createincident" method="post">
        <textarea class="field-newline" rows="5" cols="52" name="text" placeholder="Enter incident text" maxlength="500" required></textarea>
        <select style="margin-top: -3px;" name = "services[]" multiple required>
          ';
          mysqli_data_seek($services, 0);
          while($servicelist = mysqli_fetch_array($services)){
            echo '
              <option value = "'.$servicelist['id'].'">'.$servicelist['name'].'</option>
            ';
          }
          echo '
        </select>
        <input type="date" class="username-field incident-date" placeholder="DD.MM.YYYY" value="" id="date" name="date" required>

        <button style="" class="button-green incident-submit">Submit</button>
      </form>

      <div class="placeholder"></div>
      <div class="content">Add Message</div>
      ';

      if(isset($_GET["messagecreated"])) {
        echo '<p class="upassword">Message created successfully!</p>';
      }

      echo '
      <form action="admin.php?addmessage" method="post">
        <textarea class="field-newline" rows="5" cols="52" name="text" placeholder="Enter incident message text" maxlength="500" required></textarea>
        <select style="position: relative; top: -5px;" id="childof" name="childof" required>
          ';
          mysqli_data_seek($incidents, 0);
          while($incidentlist = mysqli_fetch_array($incidents)){
            if(strpos($incidentlist['childof'], "-1") !== false) {
              echo '
                <option value = "'.$incidentlist['id'].'">'.$incidentlist['date'].'</option>
              ';
            }
          }
          echo '
        </select>

        <select style="position: relative; top: -5px;" id="state" name="state" required>
          <option value = "new">New</option>
          <option value = "working">Working</option>
          <option value = "update">Update</option>
          <option value = "finished">Finished</option>
        </select>
        <button style="left: -3px; top: -5px;" class="button-green">Submit</button>
      </form>

      <div class="placeholder"></div>
      <div class="content content-danger">Danger Zone</div>
      ';
      if(isset($_GET["incidentsflushed"])) {
        echo '<p class="wpassword">All Incidents flushed!!</p>';
      }
      echo '

      <a href="?flushincidents"><button style="top: 5px;" class="button-red button-bigger">Clear all Incidents</button></a>
      ';
?>
     </div>
 </div>

<!--SETTINGS WHEN ADMIN PRIVILEGES ARE SET-->
<?php
  if($role == "administrator") {
    echo '
    <div id="settings" class="more-overlay">
      <div class="more-popup">
        <h2>Settings</h2>
        <div class="content">Instance name</div>';

        if(isset($_GET["nameupdated"])) {
          echo '<p class="upassword">Instance name successfully updated!</p>';
        }
        echo '
        <form action="admin.php?upname" method="post">
          <div><input type="text" class="username-field" value="'.$instancename.'" readonly>
          <input type="text" class="username-field" placeholder="New name" value="" id="iname" name="iname" required>
          <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input></div>
        </form>

        <div class="placeholder"></div>

        <div class="content">Autoreload Status</div>';
        if(isset($_GET["reloadupdated"])) {
          if($_GET['reloadupdated'] == "enabled") {
            echo '<p class="upassword">Autoreload value set to: '.$_GET['reloadupdated'].'!</p>';
          } else {
            echo '<p class="wpassword">Autoreload value set to: '.$_GET['reloadupdated'].'!</p>';
          }
        }
        echo '
        <form action="admin.php?upautoreload" method="post">
          <input type="text" class="username-field" value="'.$autoreload_setting.'" readonly>
            <select name = "autoreload">
              <option value = "enabled" selected>Enabled</option>
              <option value = "disabled">Disabled</option>
            </select>
          <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
        </form>

        <div class="placeholder"></div>
        <div class="content">Impressum & Privacy Policity</div>';
        if($_GET['impressumset']) {
          echo '<p class="upassword">Impressum link set to: '.$_GET['impressumset'].'!</p>';
        } else if($_GET['privacyset']) {
          echo '<p class="upassword">Privacy policity link set to: '.$_GET['privacyset'].'!</p>';
        }
        echo '
        <form action="admin.php?upimpressum" method="post">
          <input type="text" class="username-field" value="'.$impressum.'" readonly>
          <input type="text" class="username-field" placeholder="Impressum link" name="impressum">
          <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
        </form>

        <form action="admin.php?upprivacy" method="post">
          <input type="text" class="username-field" value="'.$privacy.'" readonly>
          <input type="text" class="username-field" placeholder="Privacy policity link" name="privacy">
          <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input>
        </form>
      </div>
    </div>';
  }
?>

<!--SERVICES EDIT-->
<div id="services" class="more-overlay">
  <div class="more-popup">
    <h2>Services</h2>
    <div class="content">Create group/service</div>
    <?php
        if(isset($_GET["groupadded"])) {
          echo '<p class="upassword">Group successfully added!</p>';
        }

        if(isset($_GET["serviceadded"])) {
          echo '<p class="upassword">Service successfully added!</p>';
        }
     ?>
    <form action="admin.php?creategroup" method="post">
      <div><input type="text" class="username-field" value="" placeholder="New group" id="name" name="name" required>
      <input type="number" class="username-field" value="" placeholder="Priority" id="priority" name="priority" required>
      <input style="display: none;" type="submit"><button class="button-green button-up">Submit</button></input></div>
    </form>


    <form action="admin.php?createservice" method="post">
      <div><input type="text" class="username-field" value="" placeholder="New service" id="name" name="name" required>
        <input type="text" class="username-field" value="" placeholder="short name" id="sname" name="sname" maxlength="7" required>
        <select name = "group">
          <?php
            while($grouplist = mysqli_fetch_array($groups)){
              echo '<option value = '.$grouplist['id'].' selected>'.$grouplist['name'].'</option>';
            }
            ?>
          </select>
      <input type="number" class="username-field" value="" placeholder="Priority" id="priority" name="priority" required>
      <input style="display: none;" type="submit"><button class="button-green">Submit</button></div></input>
    </form>

    <div class="placeholder"></div>
    <?php
        if(isset($_GET["servicedeleted"])) {
          echo '<p class="wpassword">Service successfully deleted!</p>';
        }

        if(isset($_GET["groupdeleted"])) {
          echo '<p class="wpassword">Group successfully deleted!</p>';
        }
     ?>
<?php
    mysqli_data_seek($groups, 0);
      while($grouplist = mysqli_fetch_array($groups)){
        echo '<div class="content">'.$grouplist['name'].'
          <a href="?editgroup='.$grouplist['id'].'#groupedit"><button class="button-green-mini">Edit</button></a>
          <a href="?delgroup='.$grouplist['id'].'#services"><button class="button-red-mini">Delete</button></a></div>';
          mysqli_data_seek($services, 0);
          while($servicelist = mysqli_fetch_array($services)){
            if($grouplist['id'] == $servicelist['groupid']) {
              echo '
              <div class="block"><input type="text" class="username-field" value="" placeholder="'.$servicelist['name'].'" id="servicename" name="servicename" readonly>
              <input type="text" class="username-field" value="" placeholder="'.$servicelist['status'].'" id="status" name="status" size="500" readonly>
              <input type="text" class="username-field" value="" placeholder="'.$servicelist['priority'].'" id="priority" name="priority" readonly>
              <a href="?manageservice='.$servicelist['id'].'#mservice"><button class="button-orange">Manage</button></a>
              ';
              if($role != "administrator") {
                echo'</div>';
              } else {
                echo '<a href="?delservice='.$servicelist['id'].'#services"><button class="button-red">Delete</button></a>';
              }
            }
          }
          echo'<div class="placeholder"></div>';
    }
?>

</body>
<script type="text/javascript" src="js/sidebar.js"></script>
<script type="text/javascript" src="js/redirect.js"></script>
</html>
