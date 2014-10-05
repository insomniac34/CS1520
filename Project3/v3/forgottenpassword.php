<?php
  session_start();
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

    <title>OpenTech Admin Portal</title>

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
          <a class="navbar-brand" href="index.php">OpenTech Admin Portal</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <?php if (isset($_SESSION['user'])) echo '<li><a href="#">Welcome, '.$_SESSION['user'].'</a></li>'; ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="ticketsubmit.php">User</a></li>
            <li class="active"><a href="AdminLogin.php">Admin</a></li>
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
    <div class="jumbotron">
      <div class="container">
        <h1>Enter your credentials, and we'll email you instructions.</h1>
           <b>Who are you sending the email to?</b>
          <input type = "text" name = "receiver" size = "30" maxlength = "30">
          <br /><br />
          <b>What is your email address?</b>
          <input type = "text" name = "sender" size = "30" maxlength = "30">
          <br /><br />
          <b>Subject?</b>
          <input type = "text" name = "subject" size = "60" maxlength = "60">
          <br /><br />
          <b>Please type your message below:</b><br />
          <br />
          <textarea name="msg" rows="5" cols="60"></textarea>
          <br /><br />
          <input type = "submit" value = "Submit">
        <label> </label><li><a href="ticketsubmit.php">Forgot your password?</a></li>
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
      input 
      {
        display: inline-block;
        float: left;
      }
      button
      {
        float: left;        
      }
      li
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
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
</html>
