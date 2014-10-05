<?php
	//table creation query: CREATE TABLE tickets (id INT, rec DATE, name VARCHAR(50), email VARCHAR(50), subject VARCHAR(100), tech VARCHAR(50), status varchar(10));
	require 'SupportPortalLogger.php';

    $filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);
	$data = json_decode(file_get_contents("php://input"));

	$log->debug("Values received from JavaScript: $data->firstName, $data->lastName, $data->email, $data->comments");

	if ((!empty($data->firstName))  
		&& (!empty($data->lastName)) 
		&& (!empty($data->email)) 
		&& (!empty($data->comments)))
	{
		$log->info("Data has been validated by server.");

		$con = new mysqli("localhost", "root", "password", "test");
		if ($con->connect_errno) {
			$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
			exit(0);
		}	

		$fullName = $data->lastName.", ".$data->firstName;		

		$id = 0;
		$id_result = $con->query("SELECT * FROM `tickets` ORDER BY `id` DESC LIMIT 1");
		if ($row = $id_result->fetch_assoc()) {
			$id = $row['id'];
			$id += 1;
		}
		$log->debug("id has been set to $id");

		$insertion_query = "INSERT INTO tickets (id, rec, name, email, subject, tech, status) 
							VALUES (\"".$id."\", NOW(), \"".$fullName."\", \"".$data->email."\", \"".$data->comments."\", NULL, \"open\")";

		$res = $con->query($insertion_query);

		if ($res)
		{
			$log.debug("SUCCESS!");
		}

		$con->close();
	}

?>