<?php
	session_start();

	require_once 'SupportPortalLogger.php';
    $filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);

	$data = json_decode(file_get_contents("php://input"));
	$log->debug("Sort.php: Value of post is: ".$data->orderby);

	$_SESSION['orderby'] = $data->orderby;
	include('AdminLogin.php');
?>