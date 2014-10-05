<?php
	error_reporting(E_ALL);
	require_once 'SupportPortalLogger.php';
    $filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);

	$data = json_decode(file_get_contents("php://input"));

	$log->debug("UserRegistration: Attempting to create new user: ".$data->username." with email ".$data->email);

	$username = $data->username;
	$email = $data->email;
	$password = md5($data->password);
	$passwordRepeat = md5($data->passwordRepeat);
	$role = "user";

	$jsonArray = array();

	$con = new mysqli("localhost", "root", "password", "test1");
	if ($con->connect_errno) 
	{
		$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
		exit(0);
	}	

	$usernameValidationQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$username."\"";
	$res = $con->query($usernameValidationQuery);
	$resultArray = $res->fetch_assoc();
	if (!empty($resultArray)) 
	{
		$log->info("Invalid username; the username $username already exists in the system.");
		array_push($jsonArray, "3");
		echo json_encode($jsonArray); //status of 3 indicates invalid username
		$con->close();
		exit(0);
	}

	$emailValidationQuery = "SELECT * FROM `all_portal_users` WHERE `email`=\"".$email."\"";
	$res = $con->query($emailValidationQuery);
	$resultArray = $res->fetch_assoc();
	if (!empty($resultArray))
	{
		$log->info("Invalid email; that email already exists in the system.");
		array_push($jsonArray, "2");
		echo json_encode($jsonArray); //status of 2 indicates invalid email address
		$con->close();
		exit(0);
	}

	if (strcmp($password, $passwordRepeat) != 0)
	{
		$log->info("User-entered passwords do not match!");
		array_push($jsonArray, "1");
		echo json_encode($jsonArray); //status of 1 indicates that the passwords do not match
		$con->close();
		exit(0);		
	}

	$userCreationQuery = "INSERT INTO `all_portal_users` VALUES (NULL, \"".$username."\", \"".$password."\", \"".$email."\", FLOOR(RAND() * 10000) + 10000, \"".$role."\")";
	$con->query($userCreationQuery);
	$log->info("User $username has been succesfully created.");
	array_push($jsonArray, "0");
	echo json_encode($jsonArray); //success!
	$con->close();
?>
