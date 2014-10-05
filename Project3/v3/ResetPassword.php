<?php
	if (session_status() == PHP_SESSION_NONE) {
	  session_start();
	}
	error_reporting(E_ALL);

	require_once 'SupportPortalLogger.php';

	$filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);	

	if (isset($_POST['newpassword']) && isset($_SESSION['pwdreset'])) //need to verify token is correct from DB AND that flag is set
	{
		$newPassword = $_POST['newpassword'];
		$newPasswordVerify = $_POST['verifynewpassword'];
		$randVal = $_SESSION['pwdreset'];
		$user = $_SESSION['id'];

	    $con = new mysqli("localhost", "root", "password", "test1");
	    if ($con->connect_errno) {
	      $log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
	      exit(0);
	    } 

		srand(date("s"));
		$newRandom = rand() % 100000;			

		if (strcmp($newPassword, $newPasswordVerify)==0)
		{
			$newPasswordQuery = "UPDATE `all_portal_users` SET `password`=MD5(\"".$newPassword."\") WHERE `id`=\"".$user."\"";
			$newRandomQuery = "UPDATE `all_portal_users` SET `randval`=\"".$newRandom."\" WHERE `id`=\"".$user."\"";

			$log->info("Hitting database with new password query: $newPasswordQuery");
			$log->info("Hitting database with new random query: $newRandomQuery");

			$newPasswordResult = $con->query($newPasswordQuery);
			$newRandomResult = $con->query($newRandomQuery);

			echo '
					<b>Your password has succesfully been reset! Please click </b><a href="AdminLogin.php">here</a> <b>to return to the login page.</b>
				 ';
		}
		else
		{
			//go back to AdminLogin.php
			//include "http://localhost/AdminLogin.php?token=$randVal&user=$user";
			header("Location: http://localhost/php/v3/AdminLogin.php?token=$randVal&user=$user");
			exit(0);
		}

		unset($_SESSION['pwdreset']);
		unset($_SESSION['id']); //this is temporarily activated

		$con->close();
	}
?>
