<!DOCTYPE html>
<html>
<head>
<title>Send Mail Results</title>
</head>
<body>
<?php
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

  $userInfoQuery = "SELECT * FROM `all_portal_users` WHERE `id`=\"".$data->id."\"";
  $res = $con->query($userInfoQuery);
  $res->data_seek(0);

  $randVal = "";
  if ($row = $res->fetch_assoc())
  {
    if ((strcmp($row['id'], $data->id) != 0)
        || (strcmp($row['email'], $data->email) != 0))
    {
      $log->info("Invalid credentials submitted ($data->id, $data->email). Exiting script.");
      echo json_encode(false);
      exit(0);
    }

    $randVal = $row['randval']; 
    $link = "http://localhost/php/v3/AdminLogin.php?token=$randVal&user=$data->id";
    //$hash = md5($randval);
    //$newPassword = substr("$hash", 0, 8); //temp password is first 8 digits of hash. 

    //$result = $con->query("UPDATE `admins` SET `password`=\"".$newPassword."\" WHERE `id`=\"".$row['id']."\"");
  }



/*
  $randVal = "";
  $randValQuery = "SELECT * FROM `admins` WHERE `admins.id`=\"".$data->id."\"";
  $res = $con->query($randValQuery);
  if ($row = $res->fetch_assoc())
  {
    $randVal = $row['randval'];
  }
*/
  $subject = "Password Reset";
  $body="Please click on the following link to reset your password: \n\n".$link;
  //$body = "Your new password is: ".$newPassword;

  // Set path for the PHPMailer files.  These must have been previously
  // unzipped and placed into the stated directory.  Be sure to match
  // up the directories in your installation (i.e. you do not have to have
  // your files in the same directory that I have here, as long as you can
  // locate them).  To download / install the necessary files, see:
  // https://github.com/Synchro/PHPMailer
  $mailpath = '/usr/share/libphp-phpmailer';
  
  // Also note that Windows installations have different path names - be sure
  // to follow the syntax correctly.
  // Also, on my Windows version, to get this to work I had to do the following:
  // 	Edit file 'php.ini'  (you need to find where that is)
  //	Locate the line:  extension=php_openssl.dll
  //	If there is a semicolon (;) at the beginning of the line, delete it
  //	Save the file
  //	Start / restart Apache
  
  // Extra path for my id and password files (so I don't have to show them
  // in this handout)
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

  $sender = strip_tags($_POST["sender"]);
  $receiver = strip_tags($_POST["receiver"]);
  $subj = strip_tags($_POST["subject"]);
  $msg = strip_tags($_POST["msg"]);

  $log->info("Sending email message: $data->id, $data->email");

  // Put information into the message
  $mail->addAddress($data->email);
  $mail->SetFrom("tpr11@pitt.edu");
  $mail->Subject = $subject;
  $mail->Body = $body;

  // echo 'Everything ok so far' . var_dump($mail);
  if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
   } 
   else { echo 'Message has been sent'; }

  $con->close();   
?>
</body>
</html>