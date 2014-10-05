<?php
  session_start();
  error_reporting(E_ALL);
  require_once 'SupportPortalLogger.php';

  //new admin query: insert into admins (id, name, password, email, randval) values (0, "Mike", PASSWORD("password"), "mike@email.com", FLOOR(RAND() * 10000) + 10000);
  //make one table from another: CREATE TABLE bar (m INT) SELECT n FROM foo;

  $filename = preg_replace('/\.php$/', '', __FILE__);
  $log = new SupportPortalLogger($filename);
  
  if (isset($_SESSION['user']))
  {
    $log->info("Resuming admin session: ".$_SESSION['user']);
  }

  $con = new mysqli("localhost", "root", "password", "test");
  if ($con->connect_errno) {
    $log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
    exit(0);
  }   

  $resultArray = array();
  if (!isset($_SESSION['user'])) //if someone isnt already logged in, then a new user is logging in.
  {
    $username = $_POST['username'];
    $log->info("User attempting login: $username");
    $userVerificationQuery = "SELECT * FROM `admins` WHERE `name`=\"".$username."\"";
    $result = $con->query($userVerificationQuery);
    $resultArray = $result->fetch_assoc();    
  }

  if ((isset($_SESSION["user"])) || (!empty($_POST['username'])) && strcmp($resultArray['name'], "$username") == 0) //IF either the session is ongoing OR a succesful login attempt has occured, display portal page.
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
        <meta name="description" content="">
        <meta name="author" content="">
        <!--<link rel="shortcut icon" href="../../assets/ico/favicon.ico">-->

        <title>OpenTech Admin Portal</title>

        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap_admin.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="dashboard.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
      </head>

      <body data-ng-app="AdministratorPortal">

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
                <?php echo "<li><a href=\"#\">Welcome, $curUser</a></li>"; ?>
                <li><a href="index.php">Home</a></li>
                <li><a href="ticketsubmit.php">Ticket</a></li>
                <li class="active"><a href="AdminLogin.php">Admin</a></li>
                <li><a href="AdminLogout.php">Logout</a></li>
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
            <div class="col-sm-3 col-md-2 sidebar">
              <ul class="nav nav-sidebar">
                <li class="active"><a href="#">Overview</a></li>
                <li><a href="#" data-toggle="modal" data-target="#ticketModal">View Selected Ticket</a></li>
                <li><a href="#">View Unassigned Tickets</a></li>
                <li><a href="#">Export</a></li>
              </ul>
              <ul class="nav nav-sidebar">
                <li><a href="">View All Tickets</a></li>
                <li><a href="">View My Tickets</a></li>
                <li><a href="">Sort</a></li>
                <li><a href="">Another nav item</a></li>
                <li><a href="">More navigation</a></li>
              </ul>
              <ul class="nav nav-sidebar">
                <li><a href="">Nav item again</a></li>
                <li><a href="">One more nav</a></li>
                <li><a href="">Another nav item</a></li>
              </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
              <h1 class="page-header">OpenTech Support Ticket Information Center</h1>

              
              <div class="row placeholders">
                <div class="col-xs-6 col-sm-3 placeholder">
                  <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
                  <h4>Open Tickets</h4>
                  <span class="text-muted">Percentage of Total</span>
                </div>
                <div class="col-xs-6 col-sm-3 placeholder">
                  <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
                  <h4>Closed Tickets</h4>
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
            

              <div class="table-responsive" ng-controller="AdminPortalDataDisplayController">
              <h2 class="sub-header">All Tickets (Currently Selected: Ticket <span ticket-number-directive="ticket_id"></span>)</h2>

                <!-- Ticket Modal Definition -->
                <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Close</button>
                            <h4 class="modal-title" id="myModalLabel">Ticket Details</h4>
                            </div>
                            <div class="modal-body">
                                <h3>Modal Body</h3>
                                <ticket-data-display-directive ticket="ticket"></ticket-data-display-directive>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                  </div>
                </div>        

                <table class="table table-striped">
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
                      
                      $tableQuery = " "; 

                      if (!isset($_SESSION['orderby']))
                      {
                        $tableQuery = "SELECT * FROM `tickets` ORDER BY `rec`";
                      }
                      else
                      {
                        $tableQuery = "SELECT * FROM `tickets` ORDER BY `".$_SESSION['orderby']."`";
                      }

                      $log->info("Tabledata display query is: $tableQuery.");   

                      $res = $con->query($tableQuery);
                      $res->data_seek(0);

                      //echo '<div id="ticket_table" ng-controller="AdminPortalSelectedRowController">';
                      while ($row = $res->fetch_assoc())
                      {
                        $checkedVal = ">"; //if id is 0, IT IS CHECKED
                        if ($row['id'] == 0)
                        {
                          $checkedVal = " checked>";
                        }

                        $log->info("Drawing table row with: ".json_encode($row));   
                        echo '
                              <tr>
                                <td><input type="radio" ng-model="ticket_id" name="sel" value="'.$row['id'].'"></td>
                                <td>'.$row['id'].'</td>
                                <td>'.$row['rec'].'</td>
                                <td>'.$row['name'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['subject'].'</td>
                                <td>'.$row['tech'].'</td>
                                <td>'.$row['status'].'</td>
                              </tr>
                             ';
                      }     
                      //echo '</div>';           

                      $con->close();
                    ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <th></th>
                        <th><button class="btn btn-default" ng-click="orderByID()" value="id">Sort</button></th>
                        <th><button class="btn btn-default" ng-click="orderByDate()" value="date">Sort</button></th>
                        <th><button class="btn btn-default" ng-click="orderByName()" value="name">Sort</button></th>
                        <th><button class="btn btn-default" ng-click="orderByEmail()" value="email">Sort</button></th>
                        <th><button class="btn btn-default" ng-click="orderByComments()" value="comments">Sort</button></th>
                        <th><button class="btn btn-default" ng-click="orderByTech()" value="tech">Sort</button></th>
                        <th><button class="btn btn-default" ng-click="orderByStatus()" value="status">Sort</button></th>
                    </tr>
                  </tfoot>

                </table>

              </div>
            </div>
          </div>
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.11/angular.min.js"></script>        
        
        <script src="dist/js/bootstrap_admin.min.js"></script>
        <script src="dist/js/docs.min.js"></script>
        <script src="admin-portal-controller.js"></script>
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
      </style>

    </html>

  <?php

  }
  else
  {
    include("AdminLogin.php");
  }
  ?>