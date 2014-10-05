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

	$log->info("Connection to database established.");

	$user = $_SESSION["user"];
	$action = $data->action;
	$role = "user";

	//get user security credentials/status:
	$theRole = "admin";
	$userPrivilegeQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$_SESSION['user']."\" AND `role`=\"".$theRole."\"";
	$privResult = $con->query($userPrivilegeQuery);
	$privResultArray = $privResult->fetch_assoc();
	if (!empty($privResultArray)) {
		$role = "admin";
	}

	if (strcmp($action, "delete") == 0) 
	{
		$deletionQuery = "DELETE FROM `tickets` WHERE `id`=\"".$data->ticketId."\"";
		$res = $con->query($deletionQuery);
		echo json_encode($res);
	}
	else if (strcmp($action, "closeOpen") == 0)
	{
		$log->debug("closeOpen!!");

		$status = " ";
		$statusQuery = "SELECT * FROM `tickets` WHERE `id`=\"".$data->ticketId."\"";
		$res = $con->query($statusQuery);
		if ($row = $res->fetch_assoc()) {
			$status = $row['status'];
		}		

		$emailFlag = 0;
		$statusModificationQuery = " ";
		if (strcmp($status, "open") == 0)
		{
			$statusModificationQuery = "UPDATE `tickets` SET `status`=\""."closed"."\" WHERE `id`=\"".$data->ticketId."\"";
			$emailFlag = 1;
		}
		else if (strcmp($status, "closed") == 0)
		{
			$statusModificationQuery = "UPDATE `tickets` SET `status`=\""."open"."\" WHERE `id`=\"".$data->ticketId."\"";
		}

		$res = $con->query($statusModificationQuery);

		if ($emailFlag == 1) //send notification email to user stating that their ticket has been closed
		{
			$log->debug("EMAIL FLAG IS 1");

			$_SESSION['receiver'] = $data->ticketEmail;
			$_SESSION['subject'] = "Your ticket has been closed!";
			$_SESSION['msg'] = "Your ticket has been closed! Come on in and grab your working technology!";
			include("SupportPortalNotificationService.php");
		}		

		echo json_encode($res);
	} 
	else if (strcmp($action, "findByCustomer") == 0) 
	{
		//$customerTicketQuery = "SELECT * FROM `tickets` WHERE `name`=\"".$data->ticketName."\"";
		$_SESSION['where'] = $data->ticketName;
	}
	else if (strcmp($action, "findBySimilar") == 0) 
	{
		$log->info("Finding similar tickets to ticket with comments: ".$data->ticketSubject);
		$curComments = $data->ticketSubject;
		$masterWordsArray = explode(" ", $curComments);

		$fetchAllQuery = "SELECT * FROM tickets";
		$res = $con->query($fetchAllQuery);
		$res->data_seek(0);

		$idArray = array();
		$rows = array();
		$similarTickets = array();
		while ($row = $res->fetch_assoc()) //for each comments paragraph P in tickets, do:
		{
			$curWords = explode(" ", $row['subject']);
			foreach($curWords as $Wp) //for each word <Wp> in P do:
			{
				foreach($masterWordsArray as $Wcur) //for each word <Wt> in the current ticket do:
				{
					$log->info("Checking if $Wcur is equal to $Wp...");
					if (strcmp($Wcur, $Wp) == 0) //if Wt == Wp do:
					{
						$log->info("IT IS! Adding ticket ID to array...");
						if ((strcmp($row['name'], $_SESSION['user']) == 0) || (strcmp($role, "admin") == 0)) //IF the row belongs to the cur user OR the cur user is an admin...
						{
							if (!in_array($row['id'], $idArray))
							{
								$rows = $row;
								array_push($similarTickets, $rows); //add comments' ticket to list of similar tickets								
								array_push($idArray, $row['id']);
							}
						}
					}
				}					
			}
		}

		$res->data_seek(0); //reset array iterator

		//now, each # in $similarTickets is filled with the ID's of rows which contain at least one matching word to the selected ticket.
		//$_SESSION['similar'] = $similarTickets;

		echo json_encode($similarTickets);
	}
	else if (strcmp($action, "assign") == 0) 
	{
		$assignQuery = "UPDATE `tickets` SET `tech`=\"".$user."\" WHERE `id`=\"".$data->ticketId."\"";
		$res = $con->query($assignQuery);
		echo json_encode($res);
	}
	else if (strcmp($action, "remove") == 0) 
	{
		$assignQuery = "UPDATE `tickets` SET `tech`=NULL WHERE `id`=\"".$data->ticketId."\"";
		$res = $con->query($assignQuery);
		echo json_encode($res);
	}
	else if (strcmp($action, "contact") == 0) 
	{

	}
	else if (strcmp($action, "unassigned") == 0) 
	{
		$_SESSION['tech'] = "NULL";
	}
	else if (strcmp($action, "mine") == 0)
	{
		$_SESSION['tech'] = $_SESSION["user"];
	}
	else if (strcmp($action, "userEmail") == 0)
	{
		$targetAddr = $data->ticketEmail;
		$subj = $data->userEmailSubj;
		$msg = $data->userEmailMsg;

		$_SESSION['receiver'] = $targetAddr;
		$_SESSION['subject'] = $subj;
		$_SESSION['msg'] = $msg;
		include("SupportPortalNotificationService.php");
	}
	else if (strcmp($action, "adminEmail") == 0)
	{
		$theId = $data->ticketId;
		$adminEmailQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$data->adminName."\"";
		$res = $con->query($adminEmailQuery);
		$row = $res->fetch_assoc();

		$targetAddr = $row['email'];
		$subj = $data->userEmailSubj;
		$msg = $data->userEmailMsg;

		$_SESSION['receiver'] = $targetAddr;
		$_SESSION['subject'] = $subj;
		$_SESSION['msg'] = $msg;
		include("SupportPortalNotificationService.php");
	}	
	else if (strcmp($action, "pwdReset") == 0)
	{
		$jsonArray = array();
		$resultArray = array();
		$attemptedOldPassword = md5($data->originalPassword);
		$newPassword = md5($data->newPassword);

		$passwordVerificationQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$_SESSION['user']."\"";
		$res = $con->query($passwordVerificationQuery);
		$row = $res->fetch_assoc();
		$oldPassword = $row['password'];

		if (strcmp($oldPassword, $attemptedOldPassword) == 0)
		{
			$passwordUpdateQuery = "UPDATE `all_portal_users` SET `password`=\"".$newPassword."\" WHERE `name`=\"".$_SESSION['user']."\"";
			$res = $con->query($passwordUpdateQuery);

			array_push($jsonArray, "1"); //1 = success
			echo json_encode($jsonArray);
		}
		else 
		{
			array_push($jsonArray, "0"); //0 = failure
			echo json_encode($jsonArray);
		}
	}

	$con->close();
?>