<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	error_reporting(E_ALL);
	unset($_SESSION['user']);
	session_destroy();
	include('AdminLogin.php'); //redirect
?>
