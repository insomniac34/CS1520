<?php
	session_start();
	unset($SESSION['user']);
	session_destroy();
	include('AdminLogin.php'); //redirect
?>