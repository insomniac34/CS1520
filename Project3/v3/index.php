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
  
  $con = new mysqli("localhost", "root", "password", "test1");
  if ($con->connect_errno) {
    $log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
    exit(0);
  } 

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>OpenTech Web Portal</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">OpenTech Web Portal</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <?php if (isset($_SESSION['user'])) echo '<li><a href="#">Welcome, '.$_SESSION['user'].'</a></li>'; ?>
            <li class="active"><a href="index.php">Home</a></li>
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
                  echo '<li><a href="AdminPortal.php">Admin Portal</a></li>';
                  echo '<li><a href="AdminLogout.php">Logout</a></li>';
                }  
            ?>
          </ul>
        </div><!--/.navbar-collapse -->
        <div class="collapse navbar-collapse">

        </div><!--/.nav-collapse -->        
      </div>
    </div>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1>Welcome to OpenTech</h1>
        <p>OpenTech strives to provide the best tech support experience possible. Submit a ticket today! </p>
        <p><a class="btn btn-primary btn-lg" role="button" href="ticketsubmit.php">Submit a Ticket &raquo;</a></p><br>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <h2>File a Support Ticket in Seconds</h2>
          <p>Simply fill out our easy-to-use service request form, and Corporate IT will dispatch a hard-working professional to your location as soon as they are available. </p>
          <p><a class="btn btn-primary btn-lg" role="button" href="ticketsubmit.php">Submit a Ticket &raquo;</a></p><br>
        </div>
        <div class="col-md-4">
          <h2>Check on your ticket's status at any moment</h2>
          <p> Busy day? We understand. Check the status of your ticket at any time to see if somebody's been dispatched to help you. We'll be sure to let you know! </p>
          <p><a class="btn btn-primary btn-lg" role="button" href="AdminLogin.php">Check Ticket Status &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Convenient Access for IT Professionals</h2>
          <p>Simply provide your access credentials to access a wealth of information on tickets from both your clients and those of your colleagues.</p>
          <p><a class="btn btn-primary btn-lg" role="button" href="AdminLogin.php">Administrator Login &raquo;</a></p>
        </div>
      </div>

      <hr>

      

    </div> <!-- /container -->

    <style>
    *
    {-webkit-font-smoothing: antialiased;}
    </style>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.11/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>-->
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
</html>
