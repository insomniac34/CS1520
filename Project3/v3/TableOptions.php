<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	error_reporting(E_ALL);
	require_once 'SupportPortalLogger.php';

    $filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);

	$data = json_decode(file_get_contents("php://input"));

	$con = new mysqli("localhost", "root", "password", "test1");
	if ($con->connect_errno) {
		$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
		exit(0);
	}		

	$jsonArray = array();
	$rows = array();
	$user = $_SESSION["user"];
	$action = $data->action;

	$log->info("Connecting to TableOptions.php as user ".$user." with action=".$action);

	if (strcmp($action, "viewMyTickets") == 0)
	{
		$query = "SELECT * FROM `tickets` WHERE `tech`=\"".$user."\"";
		
		$res = $con->query($query);
		
		$res->data_seek(0);
		while($row=$res->fetch_assoc())
		{
			$log->info("Currently adding row with id=".$row['id']." to jsonArray.");
			$rows = $row;
			array_push($jsonArray, $rows);
		}

		$res->data_seek(0);
		echo json_encode($jsonArray);
	}
	else if (strcmp($action, "viewSelectedTicket") == 0)
	{

	}
	else if (strcmp($action, "viewAllTickets") == 0)
	{
		$query = "SELECT * FROM `tickets`";
		$res = $con->query($query);
		$res->data_seek(0);
		while($row=$res->fetch_assoc())
		{
			$log->info("Currently adding row with id=".$row['id']." to jsonArray.");
			$rows = $row;
			array_push($jsonArray, $rows);
		}

		$res->data_seek(0);
		echo json_encode($jsonArray);
	}
	else if (strcmp($action, "viewUnassignedTickets") == 0)
	{
		$query = "SELECT * FROM `tickets` WHERE `tech` IS NULL";
		$res = $con->query($query);
		$res->data_seek(0);
		while($row=$res->fetch_assoc())
		{
			$log->info("Currently adding row with id=".$row['id']." to jsonArray.");
			$rows = $row;
			array_push($jsonArray, $rows);
		}

		$res->data_seek(0);
		echo json_encode($jsonArray);		
	}
	else if (strcmp($action, "viewUsersTickets") == 0)
	{
		$query = "SELECT * FROM `tickets` WHERE `name`=\"".$_SESSION['user']."\"";
		$res = $con->query($query);
		$res->data_seek(0);
		while($row=$res->fetch_assoc())
		{
			$log->info("Currently adding row with id=".$row['id']." to jsonArray.");
			$rows = $row;
			array_push($jsonArray, $rows);
		}

		$res->data_seek(0);
		echo json_encode($jsonArray);				
	}
	else if (strcmp($action, "viewUsersUnassignedTickets") == 0)
	{
		$query = "SELECT * FROM `tickets` WHERE `tech` IS NULL AND `name`=\"".$_SESSION['user']."\"";
		$res = $con->query($query);
		$res->data_seek(0);
		while($row=$res->fetch_assoc())
		{
			$log->info("Currently adding row with id=".$row['id']." to jsonArray.");
			$rows = $row;
			array_push($jsonArray, $rows);
		}

		$res->data_seek(0);
		echo json_encode($jsonArray);		
	}	


	$con->close();
?>