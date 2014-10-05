<?php
#  CS 1520 AJAX Example -- server script
#  Compare to tabulate2.php
#  This script adds the "update" functionality to tabulate2.php.  To
#  do this it accepts 3 types of requests:
#  1) Update count for a preexisting CD
#  2) Add a new entry (possibly also returning recently added entries)
#  3) Return recently added entries without adding anything new
#  Previous comments have been removed.  See some additional new
#  comments below.

   
  require_once 'SupportPortalLogger.php';
  $filename = preg_replace('/\.php$/', '', __FILE__);
  $log = new SupportPortalLogger($filename);

   $db = new mysqli('localhost', 'root', "password", 'test1');
   if ($db->connect_error):
     die ("Could not connect to db " . $db->connect_error);
   endif;

   $type = $_POST["type"];
   if ($type == 1):
      $choice = $_POST["select"];
      
      /*
      $query = "select Votes from CDs where id = '$choice'";
      $result = $db->query($query);
      $row = $result->fetch_array();
      $count = $row["Votes"];
      $newcount = $count + 1;
      $query = "update CDs set Votes = '$newcount' where id = '$choice'";
      $result = $db->query($query) || die("BOGUS A $type");
      echo "$newcount";
      */
      echo '0';

   elseif ($type == 2):
      $numrows = strip_tags($_POST["rows"]);
      $email = strip_tags($_POST["email"]);
      $subject = strip_tags($_POST["subject"]);
      $name = strip_tags($_POST["name"]);

      $log->info("Request of type 2 received. Values are: numrows=$numrows, email=$email, subject=$subject");

      $newrows = "";
      $query = "lock tables tickets write";
      $result = $db->query($query) || die($db->error);
      $query = "select * from tickets";
      $rr = $db->query("select * from tickets");
      $resrows = $rr->num_rows;
      if ($numrows < $resrows):
          for ($i = $numrows; $i < $resrows; $i++):
	       $rr->data_seek($i);
	       $curr = $rr->fetch_array();
	       $newrows .= $curr["id"] . "|";
	       $newrows .= $curr["name"] . "|";
	       $newrows .= $curr["email"] . "|";
	       $newrows .= $curr["subject"];
	       if ($i < $resrows-1):
		   $newrows .= "^";
      endif;
	  endfor;
   endif;

   if ($newrows == "") $newrows = "OK";
      $query = "insert into tickets values (NULL, NOW(), '$name', '$email', '$subject', NULL, \"open\")";
      $result = $db->query($query) || die($db->error);
      $query = "unlock tables";
      $result = $db->query($query) || die($db->error);
      echo "$newrows";
   else: // type = 3
      // This code is very similar to that for type 2 above, except that
      // we do not add anything to the DB here.  We are simply retrieving
      // any rows in the DB that were not already on the client page
      $numrows = strip_tags($_POST["rows"]);
      $newrows="";
      $rr = $db->query("select * from tickets");
      $resrows = $rr->num_rows;
      if ($numrows < $resrows):
          for ($i = $numrows; $i < $resrows; $i++):
	       # Find the appropriate row and retrieve it.  Then append the
	       # fields to the return string.
	       $rr->data_seek($i);
	       $curr = $rr->fetch_array();
	       $newrows .= $curr["id"] . "|";
	       $newrows .= $curr["name"] . "|";
	       $newrows .= $curr["email"] . "|";
	       $newrows .= $curr["subject"];
	       if ($i < $resrows-1):
		   $newrows .= "^";
               endif;
	  endfor;
      endif;
      echo "$newrows";
   endif;
?>
