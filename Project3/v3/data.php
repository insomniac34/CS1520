<?php
	global $mailuser;
	global $mailpass;

  require_once 'SupportPortalLogger.php';

  $filename = preg_replace('/\.php$/', '', __FILE__);
  $log = new SupportPortalLogger($filename);

	$con = new mysqli("localhost", "root", "password", "test1");
	if ($con->connect_errno) {
		$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
		exit(0);
	}	

	$userQuery = "SELECT * FROM credentials";
	$result = $con->query($userQuery);

	if ($row=$result->fetch_assoc())
	{
		//$mailuser = "tpr11";
		$mailuser = $row['username'];
		//$mailpass = "1mport@nt";	
		$mailpass = $row['password'];	
		$log->info("fetching global mail name/pass from DB: $mailuser and $mailpass");
	}

	$con->close();
?>
