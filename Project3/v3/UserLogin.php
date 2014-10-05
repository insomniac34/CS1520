<?php
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  error_reporting(E_ALL);
  
  require_once 'SupportPortalLogger.php';
  $filename = preg_replace('/\.php$/', '', __FILE__);
  $log = new SupportPortalLogger($filename);

  $con = new mysqli("localhost", "root", "password", "test1");
  if ($con->connect_errno) {
    $log->error("Failed to connect to MySQL: (" . $con->connect_errno . ") " . $con->connect_error);
    exit(0);
  }     

  if (isset($_SESSION["user"]))
  {

    $userRole = " ";
    $userIdentificationQuery = "SELECT * FROM `all_portal_users` WHERE `name`=\"".$_SESSION['user']."\"";
    $res = $con->query($userIdentificationQuery);
    if ($row=$res->fetch_assoc()) 
    {
      $userRole = $row['role'];
    }  

    if (strcmp($userRole, "user")==0)
    {
      include("UserPortal.php");  
    }
    else if (strcmp($_SESSION["user"], "admin")==0)
    {
      //say something about admin trying to log in as user
      include("AdminLogin.php");
    }
  }
  else if (isset($_GET['token']) && isset($_GET['user'])) //password reset
  {
    $user = $_GET['user'];
    $token = $_GET['token'];  

    $tokenQuery = "SELECT * FROM `all_portal_users` WHERE `id`=\"".$user."\"";
    $result = $con->query($tokenQuery);
    if ($row = $result->fetch_assoc())
    {
      if ((strcmp($row['id'], $user) == 0) && (strcmp($row['randval'], $token) == 0))
      {
        $_SESSION['pwdreset'] = $token;
        $_SESSION['id'] = $user;

        echo '<b>Please enter your new password: </b><br><br>
                <form name="pwdreset" method="post" action="ResetPassword.php">
                <label>New Password: </label> <input type="password" name="newpassword">
                <label>Retype new password: </label> <input type="password" name="verifynewpassword">
                <input type="submit" value="submit">
                </form>
        ';
      }
      else
      {

      }
    }

    //$con->close();
  }
  else 
  {

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

    <title>OpenTech User Portal</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron.css" rel="stylesheet">

    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0-beta.11/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>

    <script>
      angular.module('AdminPortalLogin', [])

      .controller ('ForgottenPasswordController', ['$scope', '$http', function ForgottenPasswordController($scope, $http) {

        $scope.empty = {};
        $scope.master = {};
        $scope.user = {};

        $scope.transmitEmailData = function() {
          $http.post('SupportPortalEmailService.php', {action: "pwd", id: $scope.user.userId, email: $scope.user.email}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
            console.log("Email data has been sent to server!");
            $scope.succeeded = response.data[0]['status'];
          });  
          $scope.reset();
        };

        $scope.isUnchanged = function(user) {
          return ((angular.equals(user.userId, $scope.master.userId)) && (angular.equals(user.email, $scope.master.email)))
        };        

        $scope.reset = function() {
          console.log("Resetting form.");
          $scope.user = angular.copy($scope.empty); //reset
        };

      }])

      .controller('UserRegistrationController', ['$scope', '$http', function UserRegistrationController($scope, $http) {
          
          $scope.empty = {};
          $scope.master = {};
          $scope.newUser = {};

          //for result directive
          $scope.result = {};

        $scope.register = function(newUser) {
            $http.post('UserRegistration.php', {username: $scope.newUser.username, email: $scope.newUser.email, password: $scope.newUser.password, passwordRepeat: $scope.newUser.passwordRepeat}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(result) {
              console.log("Value of registration result is: " + result.data[0]);
              switch(result.data[0])
              {
                case "3":
                  alert("That username is already in use!");
                  break;

                case "2":
                  alert("That email address is already linked to another account.");
                  break;

                case "1":
                  alert("Your passwords don't match!");
                  break;

                case "0":
                  alert("You have been succesfully registered!");
                  break;
              }              
            });
          $scope.reset();
        };

        $scope.isUnchanged = function(user) {
          return ((angular.equals(newUser.username, $scope.master.username)) 
            && (angular.equals(newUser.email, $scope.master.email))
            && (angular.equals(newUser.password, $scope.master.password))
            && (angular.equals(newUser.passwordRepeat, $scope.master.passwordRepeat)))
        };        

        $scope.reset = function() {
          console.log("Resetting form.");
          $scope.newUser = angular.copy($scope.empty); //reset
        };
      }])

      //angular directive for displaying result of registration
      .directive('UserRegistrationDirective', [function() {
        return {
          restrict: 'E',
          scope: {result: '=' },
          template: '<div></div>'
        };
      }])

      ;
    </script>  

  </head>

  <body data-ng-app="AdminPortalLogin">

    <!-- password reminder modal window -->
    <div class="modal fade" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="reminderModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" ng-controller="ForgottenPasswordController">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Close</button>
                <h4 class="modal-title" id="myModalLabel">Forgot your password?</h4>
                </div>
                <div class="modal-body">
                    <h3>Enter your credentials below, and we'll email you instructions.</h3>
                    <div class="form-group">
                      <!-- <label for="userid" class="control-label">ID<sup>*</sup></label> -->
                      <input type="text" class="form-control" id="userid" ng-model="user.userId" placeholder="Administrator ID" style="margin:0 0 5px 0;" required>
                    </div>
                    <div class="form-group">
                      <!-- <label for="userEmail" class="control-label">Email<sup>*</sup></label> -->
                      <input type="text" class="form-control" id="userEmail" ng-model="user.email" placeholder="Email Address" style="margin:0 0 5px 0;" required>
                    </div>                    

                </div>
                <div class="modal-footer" style="margin:0 0 5px 0;">
                    <button type="button" class="btn btn-primary" data-ng-click="reset()" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary" data-ng-click="reset()">Clear</button>
                    <button type="button" class="btn btn-primary" data-ng-click="transmitEmailData()" data-dismiss="modal">Send</button>
            </div>
        </div>
      </div>
    </div>    

    <!-- new user registration modal window -->
    <div class="modal fade" id="userRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="userRegistrationModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" ng-controller="UserRegistrationController">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">Close</button>
                <h4 class="modal-title" id="myModalLabel">New User Registration</h4>
                </div>
                <div class="modal-body">
                    <h3>Enter your desired credentials below:</h3>
                    <div class="form-group">
                      <!-- <label for="userid" class="control-label">ID<sup>*</sup></label> -->
                      <input type="text" class="form-control" id="newUsername" ng-model="newUser.username" placeholder="New Username" style="margin:0 0 5px 0;" required>
                    </div>
                    <div class="form-group">
                      <!-- <label for="userEmail" class="control-label">Email<sup>*</sup></label> -->
                      <input type="text" class="form-control" id="newEmail" ng-model="newUser.email" placeholder="Email Address" style="margin:0 0 5px 0;" required>
                    </div>    
                    <div>
                      <input type="text" class="form-control" id="newPass" ng-model="newUser.password" placeholder="Password" style="margin:0 0 5px 0;" required>                      
                    </div>
                    <div>
                      <input type="text" class="form-control" id="newPassRepeat" ng-model="newUser.passwordRepeat" placeholder="New Password" style="margin:0 0 5px 0;" required>                      
                    </div>                

                </div>
                <div class="modal-footer" style="margin:0 0 5px 0;">
                    <button type="button" class="btn btn-primary" data-ng-click="reset()" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary" data-ng-click="reset()">Clear</button>
                    <button type="button" class="btn btn-primary" data-ng-click="register()" data-dismiss="modal">Register</button>
            </div>
        </div>
      </div>
    </div>            

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">OpenTech User Portal</a>
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
                if (!isset($_SESSION['user'])) echo '<li class="active"><a href="UserLogin.php">User</a></li>'; 
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

    <br>
    <br>
    <br>
    <div class="jumbotron">
      <div class="container">
        <h1>User Login</h1>
        <form name="adminLogin" action="UserPortal.php" method="post" novalidate>
          <label>Username&nbsp</label><input type="text" name="username"/><br/>
          <label>Password&nbsp</label><input type="password" name="password"/><br/>
          <label> </label><input type="submit" name="login" value="Log In" class="btn btn-default"><br/>        
        </form>  
        <!-- <li><a href="#" data-toggle="modal" data-target="#reminderModal">Forgot your password?</a></li> -->
        <label> </label><li><a href="#" data-toggle="modal" data-target="#reminderModal">Forgot your password?</a></li><br>
        <label> </label><li><a href="#" data-toggle="modal" data-target="#userRegistrationModal">New User Registration</a></li>

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


    </style>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  

  </body>
</html>

<?php
}
$con->close();
?>