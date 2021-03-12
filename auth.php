<?php
session_start();

$DATABASE_HOST = 'nerdcity.at';
$DATABASE_USER = 'webstatus';
$DATABASE_PASS = 'wPsCi548z6fKJB2u';
$DATABASE_NAME = 'webstatus';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	exit('MySQl Connection failed with error: ' . mysqli_connect_error());
}

if ( !isset($_POST['username'], $_POST['password']) ) {
	exit('Please fill both the username and password fields!');
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

			header("Location: admin.php");
		} else {
			//Incorrect Data
			echo 'Incorrect username and/or password!';
		}

	} else {
		//Incorrect Data
		echo 'Incorrect username and/or password!';
	}

	$stmt->close();
}


?>
