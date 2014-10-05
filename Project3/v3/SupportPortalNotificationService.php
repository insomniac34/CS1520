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

  $sender = strip_tags($_SESSION["sender"]);
  $receiver = strip_tags($_SESSION["receiver"]);
  $subj = strip_tags($_SESSION["subject"]);
  $msg = strip_tags($_SESSION["msg"]);

  // Put information into the message
  $mail->addAddress($receiver);
  $mail->SetFrom("tpr11@pitt.edu");
  $mail->Subject = $subj;
  $mail->Body = $msg;

  $log->info("Sending email message $msg to $receiver");

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