<?php

	require 'SupportPortalLogger.php';

    $filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);
	$data = json_decode(file_get_contents("php://input"));

	$log->debug("TicketLookup: values received: $data->ticketId");

	$con = new mysqli("localhost", "root", "password", "test");
	if ($con->connect_errno) {
		$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
		exit(0);
	}	

	$log->info("TicketLookup: Connection to database established.");

	$ticketQuery = "SELECT * FROM `tickets` WHERE `id`=\"".$data->ticketId."\"";
	$log->info("TicketLookup: Query to be used: ".$ticketQuery);

	$result = $con->query($ticketQuery);
	$result->data_seek(0);

	$jsonArray=array();
	$rows=array();
	while ($row = $result->fetch_assoc())
	{
		$rows = $row;
		array_push($jsonArray, $rows);
		//$rows[] = array("id"=>$row['id'], "rec"=>$row['rec'], "name"=>$row['name'], "email"=>$row['email'], "subject"=>$row['subject'], "tech"=>$row['tech']); 
		//$log->info("TicketLookup: email is ".$row['email']);
	}

	$result->data_seek(0);
	echo json_encode($jsonArray);

	$con->close();
	
?>
