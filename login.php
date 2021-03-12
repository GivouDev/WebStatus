<?php
session_start();
if(!file_exists("config.php")) {
  header("Location: install/index.php");
  die();
}

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
  header("Location: admin.php");
}
 ?>
<?php
$wrong=FALSE;

if(isset($_POST['username']) && isset($_POST['password'])) {
include('config.php');

$con = mysqli_connect($config['DBHOST'], $config['DBUSER'], $config['DBPWD'], $config['DBNAME']);
if ( mysqli_connect_errno() ) {
        exit('MySQl Connection failed with error: ' . mysqli_connect_error());
}

if (empty($_POST['username']) || empty($_POST['password'])) {
		header("Location: login.php");
}

if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $password);
                $stmt->fetch();

                if (password_verify($_POST['password'], $password)) {
                        session_regenerate_id();

                        $_SESSION['loggedin'] = TRUE;
                        $_SESSION['name'] = $_POST['username'];
                        $_SESSION['id'] = $id;

												$wrong=FALSE;
                        header("Location: admin.php");
                } else {
									$wrong=TRUE;
                }

        } else {
					$wrong=TRUE;
        }

        $stmt->close();
}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link type="text/css" rel="stylesheet" href="css/login/style.css" />
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">

  <title>WebStatus - Adminlogin</title>
</head>

<body>
  <div id="stars"></div>

  <div class="login">
  <div class="login-screen">
    <div class="login-title">
      <h1>Login</h1>
    </div>

		<?php
    	if($wrong == true) {
				echo '<p class="wpassword">Wrong password or username!</p>';
			}
		?>

		<form action="login.php" method="post">
    	<div class="login-form">
      	<div class="login-box">
        	<input type="text" class="login-field" value="" placeholder="Username" id="username" name="username" required>
        	<label class="login-field-icon fui-user" for="username"></label>
      	</div>

      	<div class="login-box">
        	<input type="password" class="login-field" value="" placeholder="Password" id="password" name="password" required>
        	<label for="password"></label>
      	</div>

      	<input type="submit" value="Login" class="btn btn-primary btn-large btn-block" href="#"></input>
      	<a class="login-link" href="index.php">Back to Statuspage</a>
    	</div>
		</form>

  	</div>
	</div>
</body>
</html>
