<?php
	require_once 'SupportPortalLogger.php';
	$filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename); 

	$con = new mysqli("localhost", "root", "password", "test1");
	if ($con->connect_errno) {
		$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
		exit(0);
	}   

	$query = "select word from Words order by rand() limit 1";
	$result = $con->query($query);
	$rows = $result->num_rows;
	$row = $result->fetch_assoc();

	$log->info("Word is: ".$row["word"]);
	$wordArray = array();
	$jsonArray = array();
	if ($rows >= 1) {
		array_push($wordArray, $row["word"], -2, 2);
		array_push($jsonArray, $wordArray);
		echo json_encode($jsonArray);
	}

	$con->close();
?>
