<?php
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  error_reporting(E_ALL);
  require_once 'SupportPortalLogger.php';

  //new admin query: insert into admins (id, name, password, email, randval) values (0, "Mike", PASSWORD("password"), "mike@email.com", FLOOR(RAND() * 10000) + 10000);
  //make one table from another: CREATE TABLE bar (m INT) SELECT n FROM foo;

  $filename = preg_replace('/\.php$/', '', __FILE__);
  $log = new SupportPortalLogger($filename);
  
  if (isset($_SESSION['user']))
  {
    if (strcmp($_SESSION['user'], "admin") == 0)
    {
      $log->info("Resuming admin session: ".$_SESSION['user']);      
    }
    else
    {

    }
  }

  $con = new mysqli("localhost", "root", "password", "test1");
  if ($con->connect_errno) {
    $log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
    exit(0);
  }   

  $userRole = " ";
  $resultArray = array();
  if (!isset($_SESSION['user'])) //if someone isnt already logged in, then a new user is logging in.
  {
    $username = $_POST['username'];
    $attemptedPassword = $_POST['password'];
    $hashedPassword = md5($attemptedPassword);
    $log->info("User attempting login: $username / $hashedPassword");

    $userVerificationQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$username."\" AND `role`=\"admin\"";
    $result = $con->query($userVerificationQuery);
    $resultArray = $result->fetch_assoc();    
    $log->info("Comparing ".$resultArray['password']."to $hashedPassword");
  }
  else
  {
    $userIdentificationQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$_SESSION['user']."\"";
    $res = $con->query($userIdentificationQuery);
    if ($row=$res->fetch_assoc()) 
    {
      $userRole = $row['role'];
    }          
  }

  //if ((isset($_SESSION["user"])) || ((!empty($_POST['username'])) && ((strcmp($resultArray['name'], "$username") == 0) && (strcmp($resultArray['password'], md5($attemptedPassword)) == 0)))) //IF either the session is ongoing OR a succesful login attempt has occured, display portal page.
  //if ((isset($_SESSION['user'])) || ((!empty($_POST['username'])) && (strcmp($resultArray['name'], $username) == 0) && (strcmp($resultArray['password'], md5($attemptedPassword)))))
  if ((isset($_SESSION["user"]) && strcmp($userRole, "admin") == 0) || (!empty($_POST['username'])) && strcmp($resultArray['name'], "$username") == 0 && strcmp($resultArray['password'], md5($attemptedPassword)) == 0) //IF either the session is ongoing OR a succesful login attempt has occured, display portal page.
  {
    if (isset($username)) $_SESSION["user"] = $username; //start session 
    $curUser = $_SESSION["user"];

    ?>
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="CS1520 Project 2">
        <meta name="author" content="Tyler Raborn">
        <!--<link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->

        <title>OpenTech Admin Portal</title>

        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap_admin.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="dashboard.css" rel="stylesheet">

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.11/angular.min.js"></script>        
        
        <script src="dist/js/bootstrap_admin.min.js"></script>
        <script src="dist/js/docs.min.js"></script>
        <script src="admin-portal-controller.js"></script>        

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
      </head>

      <body data-ng-app="AdministratorPortal" onload = "startRefresh()">

        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">OpenTech Admin Portal</a>
            </div>
            <div class="navbar-collapse collapse">
              <ul class="nav navbar-nav navbar-right">
              <?php if (isset($_SESSION['user'])) echo '<li><a href="#">Welcome, '.$_SESSION['user'].'</a></li>'; ?>
              <li><a href="index.php">Home</a></li>
              <?php 

                  if (isset($_SESSION['user']))
                  {
                    $role = " ";
                    $username = $_SESSION['user'];
                    $userIdentificationQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$_SESSION['user']."\"";
                    $res = $con->query($userIdentificationQuery);
                    if ($row=$res->fetch_assoc()) 
                    {
                      $role = $row['role'];
                    }                   
                  }

                  //if NOBODY is logged in display option to log in as USER, and if a USER is logged in display link to USER PORTAL.
                  if (!isset($_SESSION['user'])) echo '<li><a href="UserLogin.php">User</a></li>'; 
                  else if (isset($_SESSION['user']) && strcmp($role, "user") == 0)
                  {
                    echo '<li><a href="UserPortal.php">User Portal</a></li>';
                    echo '<li><a href="UserLogout.php">Logout</a></li>';
                  }

                  //if NOBODY is logged in display option to log in as ADMIN, and if an ADMIN is logged in display link to ADMIN PORTAL.
                  if (!isset($_SESSION['user'])) echo '<li><a href="AdminLogin.php">Admin</a></li>';
                  else if (isset($_SESSION['user']) && strcmp($role, "admin") == 0)
                  {
                    echo '<li class="active"><a href="AdminPortal.php">Admin Portal</a></li>';
                    echo '<li><a href="AdminLogout.php">Logout</a></li>';
                    echo '<li><a href="#" data-toggle="modal" data-target="#resetModal">Change My Password</a></li>';
                  }  
              ?>
              </ul>
            </div><!--/.navbar-collapse -->
            <div class="collapse navbar-collapse">

            </div><!--/.nav-collapse -->        
          </div>
        </div>

        <br>
        <br>
        <br>

        <div class="container-fluid">
          <div class="row">

            <!-- Sidebar -->
            <div class="col-sm-3 col-md-2 sidebar" ng-controller="AdminPortalSidebarController">
              <ul class="nav nav-sidebar">
                <li><a href="#">Overview</a></li>
                <li><a href="#" data-toggle="modal" data-target="#ticketModal">View Selected Ticket</a></li>
                <li><a href="#" onclick="viewMyTickets('2')">View Unassigned Tickets</a></li> <!-- data-ng-click="viewUnassignedTickets()" -->
                <li><a href="#">Export</a></li>
              </ul>
              <ul class="nav nav-sidebar">
                <li><a href="" onclick="viewMyTickets('1')">View All Tickets</a></li> <!-- data-ng-click="viewAllTickets()" -->
                <li><a href="" onclick="viewMyTickets('0')">View My Tickets</a></li> <!-- data-ng-click="viewMyTickets()" -->
                <li><a href="">Sort</a></li>
              </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
              <h1 class="page-header">OpenTech Support Ticket Information Center</h1>
  
              <div class="row placeholders">
                <div class="col-xs-6 col-sm-3 placeholder">
                  <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
                  <h4>Open Tickets: </h4>
                  <span class="text-muted">Percentage of Total</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                  <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
                  <h4>Closed Tickets: </h4>
                  <span class="text-muted">Percentage of Total</span>
                </div><!--
                <div class="col-xs-6 col-sm-3 placeholder">
                  <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
                  <h4>Label</h4>
                  <span class="text-muted">Something else</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                  <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
                  <h4>Label</h4>
                  <span class="text-muted">Something else</span>
                </div> -->
              </div>
            

              <div id="myTable" class="table-responsive" ng-controller="AdminPortalDataDisplayController">
              <h2 class="sub-header">All Tickets (Currently Selected: Ticket <span ticket-number-directive="ticket_id"></span>)</h2>

              <!--
                1.     Close / reopen the ticket.  If the ticket is open, this option marks the ticket as "closed" or "resolved" in the database and sends an email to the submitter indicating as such.  If the ticket is already "closed" this simply marks its status back to "open".
                2.     Assign self to the ticket.  This option should only be available to unassigned tickets.  The ticket will subsequently be marked as assigned to the current administrator.
                3.     Remove self from ticket.  This option should only be available to tickets assigned to the current administrator.  The ticket will subsequently be marked as unassigned.
                4.     Email the submitter.  This option allows the administrator to write and send an email to the submitter (for example to offer a solution to the problem or to request additional information about the problem).
                5.     Delete the ticket.  This is only used for bogus tickets and deletes the ticket completely from the database.  Since it is likely that the submitter is also bogus, no email is sent in this case.
                6.     Find all other tickets from the same submitter.  This will show (in a table formatted in the same way as the original tickets, including the radiobuttons for selection) all tickets submitted by the person that submitted the ticket in question.  In the example above if selected for Ticket # 9 it would show ticket #s 9 and 13.
                7.     Find all similar tickets.  This will show (in a table formatted in the same way as the original tickets, including the radiobuttons for selection) all tickets whose subjects have 1 or more words in common with the ticket in question.  In the example above if selected for Ticket # 5 it would show ticket #s 5, 8, 12 and 13.
                8.     Go back to the main administrator page.  This will take the administrator back to the table shown after logging in.              
              -->

                <!-- Ticket Modal Definition -->
                <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModal" aria-hidden="true">
                    <div class="modal-dialog" style="height: 685px;">
                        <div class="modal-content" ng-controller="AdminPortalTicketDetailsController" style="height: 685px;">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Close</button>
                            <h4 class="modal-title" id="myModalLabel">Ticket Details</h4>
                            </div>
                            <div class="modal-body">
                                <!--<h3>Ticket Information</h3>-->
                                <ticket-data-display-directive ticket="ticket"></ticket-data-display-directive>
                                <br>
                                <label><b>Send Email to User</b></label>
                                <input class="form-control" type="text" ng-model="userEmail.subject" placeholder="Subject" style="margin:0 0 5px 0;">
                                <textarea class="form-control" rows="3" ng-model="userEmail.msg" placeholder="Message" style="margin:0 0 5px 0;"></textarea>
                                <button type="button" class="btn btn-default" data-ng-click="sendUserEmail(userEmail)" style="margin:0 0 5px 0;">Send</button>
                            </div>

                            <div class="modal-footer">
                            <div style="float: left;"><b>&nbsp Ticket Options</b></div><br><br>
                                <!--
                                <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
                                <button type="button" class="btn btn-primary" data-ng-click="">Assign Self to Ticket</button>
                                <button type="button" class="btn btn-primary">Remove Self from Ticket</button>
                                <button type="button" class="btn btn-primary">Contact Customer</button>
                                <button type="button" class="btn btn-primary">Delete Ticket</button>
                                <button type="button" class="btn btn-primary">Find All By Customer</button>
                                <button type="button" class="btn btn-primary">Find Similar Tickets</button>
                              -->

                              <div class="navbar" role="navigation">
                                <div class="container" style="position: relative;">
                                  <ul class="nav nav-sidebar" style="position: absolute; width: 500px; left: 80px">
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-default" data-dismiss="modal">Back</button></li>
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary" data-ng-click="assignSelf()" data-dismiss="modal">Assign Self to Ticket</button></li>
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary" data-ng-click="removeSelf()" data-dismiss="modal">Remove Self from Ticket</button></li>
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary">Contact Customer</button></li>
                                  </ul>
                                  <ul class="nav nav-sidebar" style="position: absolute; width: 500px; left: -330px;">                              
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary" data-ng-click="deleteTicket()" data-dismiss="modal">Delete Ticket</button></li>
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary" data-ng-click="closeOpen()" data-dismiss="modal">Close/Open</button></li>
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary" data-ng-click="findByCustomer()" data-dismiss="modal">Find All By Customer</button></li>
                                    <li style="margin:0 0 5px 0;"><button type="button" class="btn btn-primary" data-ng-click="findBySimilar()" data-dismiss="modal">Find Similar Tickets</button></li>        
                                  </ul>                                  
                                </div>
                              </div>

                        </div>
                    </div>
                  </div>
                </div>        

                <!-- password reset modal window -->
                <div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" ng-controller="ResetPasswordController">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Close</button>
                            <h4 class="modal-title" id="myModalLabel">Reset User Password</h4>
                            </div>
                            <div class="modal-body">
                                <h3>Enter your old and new passwords:</h3>
                                <div class="form-group">
                                  <!-- <label for="userid" class="control-label">ID<sup>*</sup></label> -->
                                  <input type="password" class="form-control" id="userid" ng-model="user.originalPassword" placeholder="Current Password" style="margin:0 0 5px 0;" required>
                                </div>
                                <div class="form-group">
                                  <!-- <label for="userid" class="control-label">ID<sup>*</sup></label> -->
                                  <input type="password" class="form-control" id="userid" ng-model="user.newPassword" placeholder="New Password" style="margin:0 0 5px 0;" required>
                                </div>                                
                                <div class="form-group">
                                  <!-- <label for="userEmail" class="control-label">Email<sup>*</sup></label> -->
                                  <input type="password" class="form-control" id="userEmail" ng-model="user.newPasswordRepeat" placeholder="Re-Enter New Password" style="margin:0 0 5px 0;" required>
                                </div>                    

                            </div>
                            <div class="modal-footer" style="margin:0 0 5px 0;">
                                <button type="button" class="btn btn-primary" data-ng-click="reset()" data-dismiss="modal">Back</button>
                                <button type="button" class="btn btn-primary" data-ng-click="reset()">Clear</button>
                                <button type="button" class="btn btn-primary" data-ng-click="transmitPasswordData()" data-dismiss="modal">Submit</button>
                        </div>
                    </div>
                  </div>
                </div>                      

                <form name = "pollForm">
                <table class="table table-hover" id="theTable">

                  <thead>
                    <tr>
                      <th>Select</th>
                      <th>ID</th>
                      <th>Date</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Comments</th>
                      <th>Tech</th>
                      <th>Status</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    /*
                        $passlist = file("password.php");
                        $passwd = rtrim($passlist[1]);
                        $passwd = preg_replace('/#/','',$passwd);
                    */
                        $db = new mysqli('localhost', 'root', "password", 'test1');
                        if ($db->connect_error):
                          die ("Could not connect to db " . $db->connect_error);
                        endif;
                        $result = $db->query("select * from tickets");
                        $rows = $result->num_rows;
                        for ($ctr = 1; $ctr <= $rows; $ctr++):
                            //echo "<tr align = 'center'>";
                            $row = $result->fetch_array();
                            $id = $row["id"];
                            $date = $row["rec"];
                            $name = $row["name"];
                            $email = $row["email"];
                            $subject = $row["subject"];
                            $tech = $row["tech"];
                            $status = $row["status"];

                            echo '<td><input type="radio" ng-model="ticket_id" name="sel" value="'.$row['id'].'"></td>';
                            echo "<td>$id</td>";
                            echo "<td>$date</td>";
                            echo "<td>$name</td>";
                            echo "<td>$email</td>";
                            echo "<td>$subject</td>";
                            echo "<td>$tech</td>";
                            echo "<td>$status</td>";
                          
                            /*echo "<td><input type = 'radio' name = 'options' 
                                           value = '$id'
                                           onclick = 'processData(1, $id)'></td>";*/
                            echo "</tr>";
                        endfor;
                    ?>                   
                  </tbody>

                  <tfoot>
                    <tr>
                      <th></th>
                        <th><button class="btn btn-default" onclick="sortTable('0')" value="id">Sort</button></th> <!--ng-click="orderByID()"-->
                        <th><button class="btn btn-default" onclick="sortTable('1')" value="date">Sort</button></th> <!-- ng-click="orderByDate()" -->
                        <th><button class="btn btn-default" onclick="sortTable('2')" value="name">Sort</button></th> <!-- ng-click="orderByName()" -->
                        <th><button class="btn btn-default" onclick="sortTable('3')" value="email">Sort</button></th> <!-- ng-click="orderByEmail()" -->
                        <th><button class="btn btn-default" onclick="sortTable('4')" value="comments">Sort</button></th> <!-- ng-click="orderByComments()" --> 
                        <th><button class="btn btn-default" onclick="sortTable('5')" value="tech">Sort</button></th> <!-- ng-click="orderByTech()" --> 
                        <th><button class="btn btn-default" onclick="sortTable('6')" value="status">Sort</button></th> <!-- ng-click="orderByStatus()" --> 
                    </tr>
                  </tfoot>

                </table>

                <!--
                <br />
                Name: <input type = "text" name = "name" value = ""><br />
                Email: <input type = "text" name = "email" value = ""><br />
                Subject: <input type = "text" name = "subject" value=""><br />
                <input type = 'button' value = "Enter" onclick = 'processWritein()'>
                <input type = "reset" name = "Reset">
                <br />
                <br />
                -->

                </form>

              </div>
            </div>
          </div>
        </div>

      <script>
      // CS 1520 Summer 2014 -- Compare to CDpoll2.php
      // This has the same functionality as CDpoll2.php, with the addition of 
      // an automatic update of the table rows if any have been added to the DB.
      // A timer calls an update method once every minute, and if any new rows
      // have been added to the DB, they are passed on to this script and added
      // to the table.  Note that we still do not update counts from the server
      // automatically -- they only change if we select a given CD.  Also, we now
      // have to consider data consistency on the client side -- see more comments
      // about this below.

          function processData() {
              var httpRequest;
       
              var type = arguments[0];  // get type of call

              if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                  httpRequest = new XMLHttpRequest();
                  if (httpRequest.overrideMimeType) {
                      httpRequest.overrideMimeType('text/xml');
                  }
              }
              else if (window.ActiveXObject) { // IE
                  try {
                      httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                      }
                  catch (e) {
                      try {
                          httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                      }
                      catch (e) {}
                  }
              }
              if (!httpRequest) {
                  alert('Giving up :( Cannot create an XMLHTTP instance');
                  return false;
              }
       
              var data;
              if (type == 1)
              {
                  var choice = arguments[1];
                  data = 'type=' + type + '&' + 'select=' + choice;  
                  //alert(data);
              }
              else // type == 2
              {
                  var rows = arguments[1];
                  var id = arguments[2];
                  var name = arguments[3];
                  var email = arguments[4];
                  var subject = arguments[5];
                  data = 'type=' + type + '&rows=' + rows + '&name=' + name + '&email=' + email + '&subject=' + subject; 
                  alert(data);
              }

              httpRequest.open('POST', 'tabulate-update.php', true);
              httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

              if (type == 1)
                    httpRequest.onreadystatechange = function() { showResults(httpRequest, choice); };
              else
                    httpRequest.onreadystatechange = function() { addNewRows(httpRequest, id, name, email, subject); } ;
              httpRequest.send(data);
          }

          function processWritein()
          {
              var numrows = document.getElementById("theTable").rows.length-1;
                var name = document.pollForm.name.value;
                var email = document.pollForm.email.value;
                var subject = document.pollForm.subject.value;

                var T = document.getElementById("theTable");
                var id = T.rows.length;
                //var id = document.


                var ok = true;
                if (name == "")
                {
                    alert("Please enter a title.");
                    document.pollForm.name.focus();
                    ok = false;
                }
                if (email == "")
                {
                    alert("Please enter an artist.");
                    document.pollForm.email.focus();
                    ok = false;
                }
                if (subject == "")
                {
                    alert("Please enter a subject.");
                    document.pollForm.subject.focus();
                    ok = false;
                }          
                // If the request is ok, and there is no other request pending,
                // process it and send the AJAX request.  Otherwise, tell the user
                // to try again.
                if (ok && !pending)
                {
                  pending = true;
                    document.pollForm.name.value = "";
                    document.pollForm.email.value = "";
                    document.pollForm.subject.value = "";
                    processData(2, numrows, id, name, email, subject);
                }
                else if (ok)
                {
                  alert("Previous update processing...please try again");
                }
          }

          function showResults(httpRequest, choice)
          {
              if (httpRequest.readyState == 4)
              {
                 if (httpRequest.status == 200)
                 {
                     var newCount = httpRequest.responseText;

                     var T = document.getElementById("theTable");
                     var R = T.rows[choice];
                     var C = R.cells;
                     var oldChild = C[2].childNodes[0];
                     var txt = document.createTextNode(newCount);
                     C[2].replaceChild(txt, oldChild);
                     //C[2].innerHTML = newCount;
                 }
                 else
                 {   alert('Problem with request'); }
             }
          }

          /*
            So basically this function accepts an integer as an argument, which identifies which type of query
            to request from the server in our AJAX call. Regardless of the specific query, we know it must 
            return an array of arrays (rows), so to make life easy i just destroy the table and rebuild it with 
            data in the passed in JSON array.
          */
          function viewMyTickets(action) {

            xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "TableOptions.php", true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

            switch(action)
            {
              case '0':
                console.log("Viewing my tickets.");
                xmlhttp.send(JSON.stringify({action: "viewMyTickets"}));
                break;

              case '1':
                console.log("Viewing all tickets.");
                xmlhttp.send(JSON.stringify({action: "viewAllTickets"}));
                break;

              case '2':
                console.log("Viewing unassigned tickets.");
                xmlhttp.send(JSON.stringify({action: "viewUnassignedTickets"}));
                break;

              case '3':
                console.log("Viewing selected ticket.");
                xmlhttp.send(JSON.stringify({action: "viewSelectedTickets"}));
                break;

              default:
                break;
            }

            xmlhttp.onreadystatechange = function() {
              if (xmlhttp.readyState==4 && xmlhttp.status==200) 
              {

                var response = JSON.parse(xmlhttp.responseText); //get response JSON obj as string
                //var tableObj = JSON.parse(response); //convert string JSON data into object 
                console.log("viewMyTickets: ResponseText is: " + response);
                console.log("viewMyTickets: Value of current id: " + response[0].id);
                //delete table
                var numRows = document.getElementById("theTable").rows.length;
                for (var j = 0; j < numRows; j++) {
                  document.getElementById("theTable").deleteRow(0);
                }

                //create table head/column names:
                var T = document.getElementById("theTable");
                var header = T.createTHead();
                var row = header.insertRow(0);

                var cell = row.insertCell(0);
                cell.innerHTML = "<b>Select</b>";

                cell = row.insertCell(1);
                cell.innerHTML = "<b>ID</b>";

                cell = row.insertCell(2);
                cell.innerHTML = "<b>Date</b>";

                cell = row.insertCell(3);
                cell.innerHTML = "<b>Name</b>";

                cell = row.insertCell(4);
                cell.innerHTML = "<b>Email</b>";

                cell = row.insertCell(5);
                cell.innerHTML = "<b>Comments</b>";

                cell = row.insertCell(6);
                cell.innerHTML = "<b>Tech</b>";

                cell = row.insertCell(7);
                cell.innerHTML = "<b>Status</b>";                    
                
                console.log("about to enter for loop, response.length is " + response.length);
                //re-populate table with new rows
                for (var i = 0; i < response.length; i++)
                {
                  console.log("viewMyTickets: Value of current id: " + response[i].id);
                  addRow(
                         response[i].id, 
                         response[i].rec,
                         response[i].name, 
                         response[i].email, 
                         response[i].subject,
                         response[i].tech,
                         response[i].status
                        );
                }        

                //re-add the now-deleted sort buttons at the bottom
                var len = 8; //hard code it as this won't be changing
                var theFooter = T.createTFoot();
                var R = theFooter.insertRow(0); 

                for (i = 0; i < len; i++)
                {
                  console.log("Adding button...");

                  var C = R.insertCell(i);

                  if (i != 0) 
                  {
                    var sortId = i - 1;
                    console.log("sortId is: " + sortId);
                    var txt = document.createElement("BUTTON");
                    txt.setAttribute("class", "btn btn-default");
                    txt.setAttribute("onclick", "sortTable(\'" + sortId + "\')");
                    txt.setAttribute("value", "");
                    txt.innerHTML = 'Sort';
                    C.appendChild(txt);
                  }
                }                        
              }              
            }
          }

          // Add 1 or more rows to the table (based on the responseText
          // string).  
          function addNewRows(httpRequest, id, name, email, subject)
          {
              console.log("AddNewRows: id=" + id + " name="+name + " email=" + email + " subject="+subject);
              if (httpRequest.readyState == 4)
              {
                 if (httpRequest.status == 200)
                 {
                     var ack = httpRequest.responseText;
                   //alert(ack);
                     if (ack == "OK")
                     {
                   //addRow(id, name, email, subject);
                     }
                     else
               {
                   var newRows = ack.split("^");
                   for (var i = 0; i < newRows.length; i++)
                   {
                     var theRow = newRows[i].split("|");
                     //addRow(theRow[1], theRow[2], theRow[3], theRow[4]);
                   }
                     //addRow(id, name, email, subject);
               }
                 }
                 else
                 {   alert('Problem with request'); }
                 // Process has completed so reset the pending variable to allow
                 // another update to occur
                 pending = false;
             }
          }

          // This is similar to the addNewRows function above, but does
          // not add any local rows -- only rows from the server are added
          // if they exist.
          function updateRows(httpRequest)
          {
              if (httpRequest.readyState == 4)
              {
                 if (httpRequest.status == 200)
                 {
                     var ack = httpRequest.responseText;
                     if (ack != "")
                     {
                        var newRows = ack.split("^");
                  for (var i = 0; i < newRows.length; i++)
                  {
                      var theRow = newRows[i].split("|");
                //addRow(theRow[0], theRow[1], theRow[2], theRow[3]);
                  }
                        window.status="Table updated at " + (new Date()).toString();
                    }
                    else
                    {
                        window.status="";
                    }
                }
                else
                {   alert('Problem with request'); }
                // Process has completed so reset the pending variable to allow another
                // update to occur
                pending = false;
             }
          }

          function sortTable(sortBy)
          {
            console.log("Entering sortTable: param value is: " + sortBy);
            //get number of rows in table

            //for each row R in the table do:
              //sortObj = {}
              //sortObj.key = value to sort table by

              //sortObj.id =R.id;
              //sortObj.date = R.date;
              //sortObj.name = R.name;
              //sortObj.email = R.email;
              //sortObj.subject = R.subject;
              //sortObj.tech = R.tech;
              //sortObj.status = R.status;

              //keyArray.push(sortObj);
              //R.destroy()

            //keyArray = sortByKey(keyArray):

            //for each key in keyArray do:
              //get its respective row R from object
              //table.add(R)
              //draw row

            var keyArray = [];
            var numRows = document.getElementById("theTable").rows.length;
            for (var i = 1; i < numRows-1; i++)
            {
              var sortObj = {};

              sortObj.id = document.getElementById("theTable").rows[i].cells[1].innerHTML;
              sortObj.date = document.getElementById("theTable").rows[i].cells[2].innerHTML;
              sortObj.name = document.getElementById("theTable").rows[i].cells[3].innerHTML;
              sortObj.email = document.getElementById("theTable").rows[i].cells[4].innerHTML;
              sortObj.subject = document.getElementById("theTable").rows[i].cells[5].innerHTML;
              sortObj.tech = document.getElementById("theTable").rows[i].cells[6].innerHTML;
              sortObj.status = document.getElementById("theTable").rows[i].cells[7].innerHTML;

              console.log("sortObj.id = " + sortObj.id);
              console.log("sortObj.date = " + sortObj.date);
              keyArray.push(sortObj);
            }

            console.log("keyArray generated. keyArray.length = " + keyArray.length);

            for (var j = 0; j < numRows; j++)
            {
              document.getElementById("theTable").deleteRow(0);
            }

            document.getElementById("theTable").deleteTHead();
            document.getElementById("theTable").deleteTFoot();

            switch(sortBy)
            {
              case "0": //sortby: id
                console.log("Sorting by: ID");
                keyArray.sort(function(a, b) {
                  if (parseInt(a.id) < parseInt(b.id)) {
                    return -1;
                  }
                  else if (parseInt(a.id) > parseInt(b.id)) {
                    return 1;
                  }
                  return 0;
                });
                break;

              case "1": //sortby: date
                console.log("Sorting by: date");
                keyArray.sort(function(a, b) {
                  aDate = new Date(a.date);
                  bDate = new Date(b.date);
                  if (aDate < bDate) {
                    return -1;
                  }
                  else if (aDate > bDate) {
                    return 1;
                  }
                  return 0;
                });
                break;

              case "2": //sortby: name
                console.log("Sorting by: name");
                keyArray.sort(function(a, b) {
                  if (a.name.localeCompare(b.name) == -1) {
                    return -1;
                  } 
                  else if (a.name.localeCompare(b.name) == 1) {
                    return 1;
                  }
                  return 0;
                });
                break;

              case "3": //sortby: email
                console.log("Sorting by: email");
                keyArray.sort(function(a, b) {
                  if (a.email.localeCompare(b.email) == -1) {
                    return -1;
                  } 
                  else if (a.email.localeCompare(b.email) == 1) {
                    return 1;
                  }
                  return 0;
                });
                break;

              case "4": //sortby: subject
                console.log("Sorting by: subject");
                keyArray.sort(function(a, b) {
                  if (a.subject.localeCompare(b.subject) == -1) {
                    return -1;
                  } 
                  else if (a.subject.localeCompare(b.subject) == 1) {
                    return 1;
                  }
                  return 0;
                });
                break;

              case "5":
                console.log("Sorting by: tech");
                keyArray.sort(function(a, b) {
                  if (a.tech.localeCompare(b.tech) == -1) {
                    return -1;
                  } 
                  else if (a.tech.localeCompare(b.tech) == 1) {
                    return 1;
                  }
                  return 0;
                });
                break;

              case "6":
                console.log("Sorting by: status");
                keyArray.sort(function(a, b) {
                  if (a.status.localeCompare(b.status) == -1) {
                    return -1;
                  } 
                  else if (a.status.localeCompare(b.status) == 1) {
                    return 1;
                  }
                  return 0;
                });
                break;                                    
            }

            //create table head/column names:
            var T = document.getElementById("theTable");
            var header = T.createTHead();
            var row = header.insertRow(0);

            var cell = row.insertCell(0);
            cell.innerHTML = "<b>Select</b>";

            cell = row.insertCell(1);
            cell.innerHTML = "<b>ID</b>";

            cell = row.insertCell(2);
            cell.innerHTML = "<b>Date</b>";

            cell = row.insertCell(3);
            cell.innerHTML = "<b>Name</b>";

            cell = row.insertCell(4);
            cell.innerHTML = "<b>Email</b>";

            cell = row.insertCell(5);
            cell.innerHTML = "<b>Comments</b>";

            cell = row.insertCell(6);
            cell.innerHTML = "<b>Tech</b>";

            cell = row.insertCell(7);
            cell.innerHTML = "<b>Status</b>";                                                                                    

            //create data rows
            for (i = 0; i < keyArray.length; i++)
            {
              addRow(
                     keyArray[i].id, 
                     keyArray[i].date,
                     keyArray[i].name, 
                     keyArray[i].email, 
                     keyArray[i].subject,
                     keyArray[i].tech,
                     keyArray[i].status
                    );
            }

            //re-add the now-deleted sort buttons at the bottom
            var len = 8; //hard code it as this won't be changing
            var theFooter = T.createTFoot();
            var R = theFooter.insertRow(0); 

            for (i = 0; i < len; i++)
            {
              console.log("Adding button...");

              var C = R.insertCell(i);

              if (i != 0) 
              {
                var sortId = i - 1;
                console.log("sortId is: " + sortId);
                var txt = document.createElement("BUTTON");
                txt.setAttribute("class", "btn btn-default");
                txt.setAttribute("onclick", "sortTable(\'" + sortId + "\')");
                txt.setAttribute("value", "");
                txt.innerHTML = 'Sort';
                C.appendChild(txt);
              }
            }

              //now after updating the DOM, re-sync the newly sorted table elements with angularJS. 
              angular.element(document.getElementById('theTable')).scope().$apply();
          }

          function addRow(id, date, name, email, subject, tech, status)
          {
                var T = document.getElementById("theTable");
                var len = T.rows.length;
                var R = T.insertRow(len); 

                /*<input type="radio" ng-model="ticket_id" name="sel" value="'.$row['id'].'">*/
                var C = R.insertCell(0);
                var txt = document.createElement("INPUT");
                txt.setAttribute("type", "radio");
                txt.setAttribute("ng-model", "ticket_id");
                txt.setAttribute("name", "sel");
                txt.setAttribute("value", id);
                txt.setAttribute("onclick", "transmitRadioStatusToAngular(\'" + id + "\')");
                C.appendChild(txt);

                C = R.insertCell(1);
                txt = document.createTextNode(id);
                C.appendChild(txt);

                C = R.insertCell(2);
                txt = document.createTextNode(date);
                C.appendChild(txt);                

                C = R.insertCell(3);
                txt = document.createTextNode(name);
                C.appendChild(txt);

                C = R.insertCell(4);
                txt = document.createTextNode(email);
                C.appendChild(txt);

                C = R.insertCell(5);
                txt = document.createTextNode(subject);
                C.appendChild(txt);

                C = R.insertCell(6);
                txt = document.createTextNode(tech);
                C.appendChild(txt);                

                C = R.insertCell(7);
                txt = document.createTextNode(status);
                C.appendChild(txt);                    

                /*    
                var rb = document.createElement('input');
                rb.setAttribute('type', 'radio');
                rb.setAttribute('name', 'options');
                rb.setAttribute('value', len);
                rb.onclick = function() { processData(1, len); };
                C.appendChild(rb);
                */
          }

          // This function is called when the page is loaded.  It sets a timer
          // to call the refreshPage function after 1 minute.
          function startRefresh()
          {
             t = setTimeout("refreshPage()", 60000);
          } 

          // Make an AJAX call to see if there are any new rows in the DB. 
          // If so, the callback method adds them to the table.  Note that the
          // callback method sets the timer again (and this repeats
          // indefinitely).
          // Note: Now our logic is complicated just a bit.  It is possible that
          // a user could enter a new CD just as the system is automatically updating
          // the list, but before the update has completed.  In this case we could have
          // a consistency problem:  Both requests have gone to the server but neither
          // has returned, so upon return the table is updated twice with the new CD
          // entries.  To avoid this problem, we can keep a state variable.  The
          // pending variable will only allow one active request to update the page
          // at a time.  When the request completes the variable is reset to allow
          // another update.  In the case of the autoupdate below, if there is a pending
          // request we simply don't do the update request and reset the timer for the
          // next request.  However, in the case of the user entered information, we
          // need to tell the user to try again -- see processWritein
          function refreshPage()
          {
              if (!pending)
              {
                  pending = true;
                  var httpRequest;
       
                  if (window.XMLHttpRequest) { // Mozilla, Safari, ...
                      httpRequest = new XMLHttpRequest();
                      if (httpRequest.overrideMimeType) {
                          httpRequest.overrideMimeType('text/xml');
                      }
                  }
                  else if (window.ActiveXObject) { // IE
                      try {
                          httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                      }
                      catch (e) {
                          try {
                              httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                          }
                          catch (e) {}
                      }
                  }
                  if (!httpRequest) {
                      alert('Giving up :( Cannot create an XMLHTTP instance');
                      return false;
                  }
       
                  var type = 3; 
                  var rows = document.getElementById("theTable").rows.length-1;
                  var data = 'type=' + type + '&rows=' + rows;

                  httpRequest.open('POST', 'tabulate-update.php', true);
                  httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                  httpRequest.onreadystatechange = function() { updateRows(httpRequest); } ;
                  httpRequest.send(data);   
              }
              t = setTimeout("refreshPage()", 60000);
          }

          /*
            This function tells AngularJS which radio button is selected so that it can prepare the proper modal dialogue.
          
            In more detail: Screwing with the DOM like we do above essentially breaks Angular by making it out-of-sync with the current state of the page. So,
            to fix this, you click the radio button, which is linked to this function. It in turn acquires the AngularJS "scope" of the table, which 
            is the name of the HTML div element which houses the AngularJS controller whose member field we want to modify. We then use the acquired scope to
            set this value. THEN, AngularJS takes over completely. The AngularJS Directive function responsible for modifying the modal dialogue HTML is 
            refreshed so that it matches the data for the ticket ID # we passed in originally - giving us a constantly up-to-date "ticket details" dialogue
            with no hard refreshes, even when the DOM is being changed dynamically by the above JS.

          */
          function transmitRadioStatusToAngular(selectedRadioButton)
          {
              console.log("TRANSMITTING \"" + selectedRadioButton + "\" TO ANGULAR!");
              var theAngularScope = angular.element($("#myTable")).scope();
              theAngularScope.$apply(function() {
                theAngularScope.ticket_id = selectedRadioButton.toString();
              });
          }

        </script>        

        <script type="text/javascript">
            var win, t, pending;
            pending = false;
        </script>        
      </body>

      <style>
          *
          {-webkit-font-smoothing: antialiased;}

          .btn-default 
          {
            color: #fff;
            background-color: #428bca;
            border-color: #357ebd;
          }   

          .btn-default:hover,
          .btn-default:focus,
          .btn-default:active,
          .btn-default.active,
          .open .dropdown-toggle.btn-default 
          {
            color: #fff;
            background-color: #3276b1;
            border-color: #285e8e;
          }                 

          .sidebar a:hover { background-color: #428bca !important;
                           color: #ffffff; }

          .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
              background-color: #40E0D0;
            }
      </style>

    </html>

  <?php

  }
  else
  {
    include("AdminLogin.php");
  }
  ?>