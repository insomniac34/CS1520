<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	error_reporting(E_ALL);

	function sendNotification($toList, $curSubject, $curMessage) 
	{
	  $mailpath = '/usr/share/libphp-phpmailer';
	  $incpath = '/var/www/php/v3';
	  // Add the new path items to the previous PHP path
	  $path = get_include_path();
	  set_include_path($path . PATH_SEPARATOR . $mailpath . PATH_SEPARATOR . $incpath);
	  require '/usr/share/php/libphp-phpmailer/PHPMailerAutoload.php';
	  include('data.php');
	  
	  // PHPMailer is a class -- we will discuss classes and PHP object-oriented
	  // programming soon.  However, you should be able to copy / use this
	  // technique without fully understanding PHP OOP.
	  $mail = new PHPMailer();
	 
	  $mail->IsSMTP(); // telling the class to use SMTP
	  $mail->SMTPAuth = true; // enable SMTP authentication
	  $mail->SMTPSecure = "tls"; // sets tls authentication
	  $mail->Host = "smtp.pitt.edu"; // sets Pitt as the SMTP server
	  $mail->Port = 587; // set the SMTP port for the Pitt server
	  $mail->Username = "$mailuser"; // Pitt username
	  $mail->Password = "$mailpass"; // Pitt password

	  $subj = $curSubject;
	  $msg = $curMessage;

	  foreach($toList as $addr)
	  {
	  	$mail->addAddress($addr);
	  }

	  // Put information into the message
	  //$mail->addAddress($receiver);
	  $mail->SetFrom("tpr11@pitt.edu");
	  $mail->Subject = $subj;
	  $mail->Body = $msg;

	  // echo 'Everything ok so far' . var_dump($mail);
	  if(!$mail->send()) {
	    //echo 'Message could not be sent.';
	    //echo 'Mailer Error: ' . $mail->ErrorInfo;
	   } 
	   else { /*echo 'Message has been sent';*/ }
 
	}

	//table creation query: CREATE TABLE tickets (id INT, rec DATE, name VARCHAR(50), email VARCHAR(50), subject VARCHAR(100), tech VARCHAR(50), status varchar(10));
	require_once 'SupportPortalLogger.php';

    $filename = preg_replace('/\.php$/', '', __FILE__);
	$log = new SupportPortalLogger($filename);
	$data = json_decode(file_get_contents("php://input"));

	//$log->debug("Values received from JavaScript: $data->firstName, $data->lastName, $data->email, $data->comments");

	if ((!empty($data->email)) && (!empty($data->comments)))
	{
		$log->info("Data has been validated by server.");

		$con = new mysqli("localhost", "root", "password", "test1");
		if ($con->connect_errno) {
			$log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
			exit(0);
		}	

		//$fullName = $data->lastName.", ".$data->firstName;	
		$fullName = $_SESSION['user'];	

		$id = 0;
		$id_result = $con->query("SELECT * FROM `tickets` ORDER BY `id` DESC LIMIT 1");
		if ($row = $id_result->fetch_assoc()) {
			$id = $row['id'];
			$id += 1;
		}
		$log->debug("id has been set to $id");

		$insertion_query = "INSERT INTO tickets (id, rec, name, email, subject, tech, status) 
							VALUES (NULL, NOW(), \"".$fullName."\", \"".$data->email."\", \"".$data->comments."\", NULL, \"open\")";

		$res = $con->query($insertion_query);

		$targetEmail = $data->email;

		//insert email sending code here....

		if ($res)
		{
			$log->debug("SUCCESS!");
		}

		$emailList = array();
		$adminRole = "admin";
		$getAllAdminsQuery = "SELECT * FROM `all_portal_users` WHERE `role`=\"".$adminRole."\"";
		$result = $con->query($getAllAdminsQuery);
		while ($row=$result->fetch_assoc())
		{
			array_push($emailList, $row['email']);
		}

		array_push($emailList, $data->email);
		$log->info("Notifying user $fullName and Portal Administrators of ticket.");
		sendNotification(
						 $emailList,
						 "Ticket has been submitted.",
						 "This email is notifying either a portal administrator or the submitting customer that a succesful ticket submission has occured."
						);

		$getAllUserTicketsQuery = "SELECT * FROM `tickets` WHERE `name`=\"".$_SESSION['user']."\"";
		$ticketResult = $con->query($getAllUserTicketsQuery);
		$con->close();

		$jsonArray = array();
		$rows = array();

		while($row = $ticketResult->fetch_assoc())
		{
			$rows = $row;
			array_push($jsonArray, $rows);			
		}


		echo json_encode($jsonArray);
	}

?>