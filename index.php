<?php
  //Fetch data from database;
  include('config.php');
  include('functions.php');

  //Checking if setup is complete
  if(!file_exists("config.php")) {
    header("Location: install/index.php");
    die();
  }

  $con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
  if ( mysqli_connect_errno() ) {
      exit('MySQl Connection failed with error: ' . mysqli_connect_error());
  }

  $instancename = mysqli_fetch_assoc(mysqli_query($con, "SELECT value FROM settings WHERE type='instancename'"))["value"];
  $autoreload_setting = mysqli_fetch_assoc(mysqli_query($con, "SELECT value FROM settings WHERE type='autoreload'"))["value"];
  $modular_setting = mysqli_fetch_assoc(mysqli_query($con, "SELECT value FROM settings WHERE type='modularwindow'"))["value"];

  $groups = mysqli_query($con, "SELECT priority, name, id FROM groups ORDER BY priority DESC");
  $services = mysqli_query($con, "SELECT priority, groupid, name, id, status FROM services ORDER BY priority DESC");
  $incidents = mysqli_query($con, "SELECT id, status, services, text, date, childof FROM incidents ORDER BY id ASC");
  $incidents2 = mysqli_query($con, "SELECT id, status, services, text, date, childof FROM incidents ORDER BY id ASC");
 ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link type="text/css" rel="stylesheet" href="css/style.css" />
  <link type="text/css" rel="stylesheet" href="css/mobile.css" />
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">

<?php
  echo '<title>'.$instancename.' - Home</title>';
?>
</head>
<body>

<?php
  echo '<h1>'.$instancename.'</h1>';
?>

   <!-- GLOBAL STATUS-->
<?php
    if(sOffline()) {
      echo '
      <div class="global-status">
        <div id="status" class="status status-problems">
   		     <div class="loading fa fa-times">
   		</div>

   		<div>
   			<div class="status-text">Some Systems may not work at the moment</div>
   			   <div id="timer" class="status-time"></div>
   		  </div>
      </div>';
    } else if(sMaintenance()) {
      echo '
      <div class="global-status">
        <div id="status" class="status status-maintenance">
            <div class="loading fa fa-times">
       </div>

       <div>
         <div class="status-text">Systems currently under maintenance</div>
            <div id="timer" class="status-time"></div>
         </div>
      </div>';
    } else {
      echo '
      <div class="global-status">
        <div id="status" class="status status-okay">
            <div class="loading fa fa-times">
       </div>

       <div>
         <div class="status-text">All Systems operational!</div>
            <div id="timer" class="status-time"></div>
         </div>
      </div>';
    }
?>

   <!-- AUTORELOAD CHECKBOX-->
<?php
  if($autoreload_setting == "enabled") {
    echo '
      <p>
 		   <input type="checkbox" id="cb1" checked>
 			   <label for="cb1">Autoreload</label>
      </p>';
  }
?>
   </div>

<?php
  mysqli_data_seek($groups, 0);
  while($grouplist = mysqli_fetch_array($groups)){
      echo '
      <div class="service-block">
   		<ul class="services">
   			<h3 class="services-header service-title">'.$grouplist['name'].'</h3>
      ';

      mysqli_data_seek($services, 0);
      while($servicelist = mysqli_fetch_array($services)){
        if($grouplist['id'] == $servicelist['groupid']) {
          if($servicelist['status'] == "Online") {
            echo '
              <li id="status-'.$servicelist['id'].'" class="service service-okay services-okay">
                <div class="service-icon"></div>
                <div class="service-name">'.$servicelist['name'].'</div>
              </li></a>
            ';
          } else if($servicelist['status'] == "Maintenance") {
            echo '
              <li id="status-'.$servicelist['id'].'" class="service service-maintenance services-maintenance">
                <div class="service-icon"></div>
                <div class="service-name">'.$servicelist['name'].'</div>
                <div id="alert-'.$servicelist['id'].'" class="status-annoucement"></div>
              </li></a>
            ';
          } else if($servicelist['status'] == "Offline") {
            echo '
              <li id="status-'.$servicelist['id'].'" class="service service-troubles services-troubles">
                <div class="service-icon"></div>
                <div class="service-name">'.$servicelist['name'].'</div>
                <div id="alert-'.$servicelist['id'].'" class="status-annoucement"></div>
              </li></a>
            ';
          }
        }
      }
      echo '
      </ul>
     </div>
      ';
  }

?>

    <!-- TIMELINE REPORT NEW -->
<?php
mysqli_data_seek($incidents, 0);
$count = 3;

if(isset($_GET['lmore'])) {
  $add = (int) $_GET['lmore'];
  $count += $add;
}
while($incidentlist = mysqli_fetch_array($incidents)){
  if($count != 0) {
      if(strpos($incidentlist['childof'], "-1") !== false) {
        $count--;
        echo '
          <div class="timeline">
            <div class="timeline-date">
     			    <h1>'.str_replace("-", ".", $incidentlist['date']).'</h1>
     	      </div>

            <div class="timeline-info">
              <h2>Affected Services:</h2>
            </div>

            <div class="timeline-services">
              <ul class="vertical">';

     $servicearray = explode(",", $incidentlist['services']);

    foreach ($servicearray as $index) {
          echo '<li>'.getName($index).'</li>';
    }

    echo '
              </ul>
            </div>

            <div class="item">
          	 <div class="circle"></div>
          	   <div class="message">';

   if(strpos($incidentlist['status'], "new") !== false) {
     echo '
                <div class="timeline-icon timeline-new"></div>
                  '.$incidentlist['text'].'
                </div>
              </div>';
   } else if(strpos($incidentlist['status'], "working") !== false) {
     echo '
                <div class="timeline-icon timeline-working"></div>
                  '.$incidentlist['text'].'
                </div>
              </div>';
  } else if(strpos($incidentlist['status'], "update") !== false) {
    echo '
                <div class="timeline-icon timeline-update"></div>
                  '.$incidentlist['text'].'
                </div>
              </div>';
  } else if(strpos($incidentlist['status'], "finished") !== false) {
    echo '
                <div class="timeline-icon timeline-finished"></div>
                  '.$incidentlist['text'].'
                </div>
              </div>';
  }

  mysqli_data_seek($incidents2, 0);
    while($incidentlist2 = mysqli_fetch_array($incidents2)){
      if(strpos($incidentlist['id'], $incidentlist2['childof']) !== false) {
        echo '
              <div class="item">
                <div class="circle"></div>
                  <div class="message">';
        if(strpos($incidentlist2['status'], "new") !== false) {
          echo '
                  <div class="timeline-icon timeline-new"></div>
                '.$incidentlist2['text'].'
                </div>
              </div>';

        } else if(strpos($incidentlist2['status'], "working") !== false) {
          echo '
                <div class="timeline-icon timeline-working"></div>
                  '.$incidentlist2['text'].'
                </div>
             </div>';

       } else if(strpos($incidentlist2['status'], "update") !== false) {
         echo '
               <div class="timeline-icon timeline-update"></div>
                '.$incidentlist2['text'].'
               </div>
             </div>';
      } else if(strpos($incidentlist2['status'], "finished") !== false) {
        echo '
              <div class="timeline-icon timeline-finished"></div>
                '.$incidentlist2['text'].'
            </div>
          </div>
          ';
      }
     }
    }
echo '</div>';
      }
    }
  }
?>

      <!-- FOOTER -->
    <footer class="footer">
        <p class="footer">Powered by <a href="#" target="_blank">WebStatus</a></p>
		<p class="footer-subtext">Made with <span class="footer-heart"><i class="fa fa-heart"></i></span> by Givou</p>
    </footer>

	<div class="legend">
		<span class="timeline-new legend-text"> Annoucement</span>
		<span class="timeline-update legend-text"> Update</span>
		<span class="timeline-working legend-text"> Working on a fix</span>
		<span class="timeline-finished legend-text"> Finished / No Problems</span>
		<span class="legend-maintenance legend-text"> Maintenance</span>
		<span class="legend-lastdown legend-text"> Last time down</span>
		<span class="legend-problems legend-text"> Offline / Problems</span>
	</div>
</body>


<?php
  if($autoreload_setting == "enabled") {
    echo '<script type="text/javascript" src="js/checking.js"></script>';
  }
?>
</html>
