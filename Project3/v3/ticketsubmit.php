<?php
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  error_reporting(E_ALL);

  /*
  $submissionStatus = 0;

  if (!empty($_POST["firstName"]) || !empty($_POST["lastName"]) || !empty($_POST["email"]) || !empty($_POST["problem"]))
  {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $subject = $_POST["problem"];

    //use values of submission to determine which values to display
    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($subject))
    {
      $submissionStatus = 1;
    }
    else
    {
      $submissionStatus = 2;
    }
  }
  */

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

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

  <body data-ng-app="SupportTicketSubmission">

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
            <li><a href="index.php">Home</a></li>
            <li class="active"><a href="ticketsubmit.php">User</a></li>
            <li><a href="AdminLogin.php">Admin</a></li>
            <?php if (isset($_SESSION['user'])) echo '<li><a href="AdminLogout.php">Logout</a></li>'; ?>
          </ul>
        </div><!--/.navbar-collapse -->
        <div class="collapse navbar-collapse">

        </div><!--/.nav-collapse -->        
      </div>
    </div>

    <br>
    <br>
    <br>
    <div class="jumbotron" ng-controller="TicketSubmissionController">
      <div class="container">
        <h1>Submit a Ticket</h1>
        <?php
          /*
          if ($submissionStatus == 0) echo '<p>Please be sure to fill out all fields. </p>';
          else if ($submissionStatus == 1) echo '<p> Thank you, your ticket has been succesfully submitted! </p>';
          else echo '<p> Your ticket was NOT submitted, please be sure to fill out all fields! </p>';
          */
        ?>
        <form name="ticketSubmit" class="css-form" novalidate>
          <label>First Name&nbsp</label> <input type="text" name="firstName" ng-model="ticket.firstName" required /><br/>
          <label>Last Name&nbsp</label> <input type="text" name="lastName" ng-model="ticket.lastName" required /><br/>
          <label>Email&nbsp</label> <input type="email" name="email" ng-model="ticket.email" required /><br />
          <label>Problem&nbsp</label> <input type="text" name="problem" ng-model="ticket.problem" required/><br>
          <label> </label><button ng-click="reset()" ng-disabled="isUnchanged(ticket)">Clear</button> 
          <!--<input type="submit" value="Submit Ticket" ng-click="submit(ticket)" ng-disabled="ticketSubmit.$invalid || isUnchanged(ticket)"> -->
          <button ng-click="submit(ticket)" ng-disabled="ticketSubmit.$invalid || isUnchanged(ticket)">Submit Ticket</button>
          <!-- <input type="submit" value="submit"> -->
        </form>
      </div>
    </div>

    <style type="text/css">
      label
      {
          display: inline-block;
          float: left;
          clear: left;
          width: 500px;
          text-align: right;
      }
      button
      {
        float: left;        
      }

      h1
      {
          font-family: 'kievit-normal', Arial, sans-serif;
          padding-bottom: 10px;
          margin-bottom: 30px;
          /*border-bottom: 1px solid #e1e1e1;*/
          text-align: center;         
          font-size: 35px;
          line-height: 24px;
      }

      .css-form input.ng-invalid.ng-dirty 
      {
        display: inline-block;
        float: left;        
        background-color: #FA787E;
      }

      .css-form input.ng-valid.ng-dirty 
      {
        display: inline-block;
        float: left;        
        background-color: #78FA89;
      }

      .css-form input.ng-valid.ng-pristine
      {
        display: inline-block;
        float: left;        
        background-color: #FFFFFF;
      }

      .css-form input.ng-invalid.ng-pristine
      {
        display: inline-block;
        float: left;        
        background-color: #FFFFFF;
      }

      body 
      {
          background-color: #999;
          color: #444;
          text-align:center;
      }   
    </style>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.11/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="ticket-submission-controller.js"></script> 
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
</html>
