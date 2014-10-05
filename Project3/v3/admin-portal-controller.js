/* Written by Tyler Raborn
 *
 * This AngularJS module is responsible for handling the clientside logic for the User/Admin portal. Uses Google's AngularJS framework.
 */

 angular.module( 'AdministratorPortal', [])

.controller('TicketSubmissionController', ['$scope', '$http', 'TicketSubmissionService', function TicketSubmissionController($scope, $http, TicketSubmissionService) {

  $scope.empty = {};
  $scope.master = {};

  $scope.update = function(ticket) {
    $scope.master = angular.copy(ticket);
  };

  $scope.isUnchanged = function(ticket) {
    return ((angular.equals(ticket.firstName, $scope.master.firstName))
      && (angular.equals(ticket.lastName, $scope.master.lastName))
      && (angular.equals(ticket.email, $scope.master.email))
      && (angular.equals(ticket.problem, $scope.master.problem)))
  };

  $scope.submit = function(ticket) {
    console.log("Submitting form data...");
    $scope.update(ticket);
    $http.post('TicketHandler.php', {email: ticket.email, comments: ticket.problem}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
      //console.log("Angular: Value of id: " + response.data[0]['id']);
      destroyTable();
      angular.forEach(response.data, function(theRow) {
        console.log("Sending current id=" + theRow['id'] + " to updateTableAfterSubmit()...");
        updateTableAfterSubmit(
                               theRow['id'],
                               theRow['rec'],
                               theRow['name'],
                               theRow['email'],
                               theRow['subject'],
                               theRow['tech'],
                               theRow['status']
                              );      
      })
      addTableButtons();  
      //note: this is a promise, and as such server state is not guaranteed.
      //setTimeout(function(){viewMyTickets('0')}, 2000);
      //updateTableAfterSubmit(ticket.email, ticket.comments);
    });
    //alert("Your ticket has been submitted succesfully, an email should be dispatched shortly confirming the submission.");
    $scope.ticket = angular.copy($scope.empty); //reset
  };

  $scope.reset = function() {
    console.log("Resetting form.");
    $scope.ticket = angular.copy($scope.empty); //reset
  };

  $scope.reset();

}])

//this service is responsible for transmitting the entered client data to the serverside PHP script.
.service('TicketSubmissionService', ['$http', function TicketSubmissionService($http) {
  this.transmitTicketData = function(ticketData) {
    console.log("transmitting form data...");
    $http({
      method: 'POST',
      url: 'TicketHandler.php',
      data: {
        firstName: ticketData.firstName,
        lastName: ticketData.lastName,
        email: ticketData.email,
        comments: ticketData.problem
      },
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }); 
    return true;
  };
}])

 .controller( 'AdminPortalSidebarController', ['$scope', '$http', '$window', function AdminPortalSidebarController($scope, $http, $window) {

    $scope.viewAllTickets = function() {
      $window.location.reload();
    };

    $scope.viewUnassignedTickets = function() {
        $http.post('TicketOptions.php', {action: "unassigned"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

    $scope.viewMyTickets = function() {
        $http.post('TicketOptions.php', {action: "mine"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });      
    };

 }])

 .controller('AdminPortalDataDisplayController', ['$scope', '$http', '$window', function AdminPortalDataDisplayController($scope, $http, $window) {

    $scope.ticket = {};
    $scope.pending = false;

    console.log("Entering admin portal controller.");

    $scope.ticket_id = "1";
    $scope.$watch('ticket_id', function(id) {
        console.log("Selected value: " + id);

        $http.post('TicketLookup.php', {ticketId: id}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {

            console.log("Data has been received from server; resulting row has id of : " + response.data[0]['id']);

            $scope.ticket.curId = response.data[0]['id'];
            $scope.ticket.curDate = response.data[0]['rec'];
            $scope.ticket.curName = response.data[0]['name'];
            $scope.ticket.curEmail = response.data[0]['email'];
            $scope.ticket.curSubject = response.data[0]['subject'];
            $scope.ticket.curTech = response.data[0]['tech'];
            $scope.ticket.curStatus = response.data[0]['status'];
        });
    });	

    $scope.orderByID = function() {
      console.log("id");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'id',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });			
      $window.location.reload();
  };

  $scope.orderByDate = function() {
      console.log("rec");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'rec',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });		
      $window.location.reload();
  };

  $scope.orderByName = function() {
      console.log("name");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'name',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });				
      $window.location.reload();
  };

  $scope.orderByEmail = function() {
      console.log("email");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'email',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });				
      $window.location.reload();
  };

  $scope.orderByComments = function() {
      console.log("subject");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'subject',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });			
      $window.location.reload();
  };

  $scope.orderByTech = function() {
      console.log("tech");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'rec',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });				
      $window.location.reload();
  };

  $scope.orderByStatus = function() {
      console.log("status");
      $http({
         method: 'POST',
         url: 'sort.php',
         data: {
            orderby: 'status',
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });	
      $window.location.reload();					
  };

}])

//this controller is responsible for handling the button's functionalities in the popup ticket details window. Data is sent to 
//the serverside php script which modifies the database appropriately.
 .controller( 'AdminPortalTicketDetailsController', ['$scope', '$http', '$window', function AdminPortalTicketDetailsController($scope, $http, $window) {

    $scope.userEmail = {};
    $scope.empty = {};

    $scope.sendUserEmail = function(newUserEmail) {
      $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, ticketEmail: $scope.ticket.curEmail, userEmailMsg: $scope.userEmail.msg, userEmailSubj: $scope.userEmail.subject, action: "userEmail"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
        console.log("User email has been sent to server!");
        $scope.userEmail = angular.copy($scope.empty);
      });      
    };

    $scope.sendAdminEmail = function(newUserEmail) {
      $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, ticketEmail: $scope.ticket.curEmail, adminName: $scope.ticket.curTech, userEmailMsg: $scope.userEmail.msg, userEmailSubj: $scope.userEmail.subject, action: "adminEmail"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
        console.log("Admin email has been sent to server!");
        $scope.userEmail = angular.copy($scope.empty);
      });      
    };    

    $scope.deleteTicket = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, action: "delete"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

    $scope.closeOpen = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, ticketEmail: $scope.ticket.curEmail, action: "closeOpen"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

    $scope.findByCustomer = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, ticketName: $scope.ticket.curName, action: "findByCustomer"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

    $scope.findBySimilar = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, ticketSubject: $scope.ticket.curSubject, action: "findBySimilar"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          destroyTable(); //legacy JS function 
          angular.forEach(response.data, function(theRow) {
            console.log("Sending current id=" + theRow['id'] + " to updateTableAfterSubmit()...");
            updateTableAfterSubmit(
                                   theRow['id'],
                                   theRow['rec'],
                                   theRow['name'],
                                   theRow['email'],
                                   theRow['subject'],
                                   theRow['tech'],
                                   theRow['status']
                                  );      
          })
          addTableButtons(); //another function written in legacy JS            
        });
        //$window.location.reload();
    };

    $scope.assignSelf = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, action: "assign"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

    $scope.removeSelf = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, action: "remove"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

    $scope.contactCustomer = function() {
        $http.post('TicketOptions.php', {ticketId: $scope.ticket.curId, ticketEmail: $scope.ticket.email, ticketName: $scope.ticket.curName, action: "contact"}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          $window.location.reload();
        });
    };

 }])

.directive ('ticketNumberDirective', ['$interval', function($interval) {

	function link(scope, element, attrs) {

		var ticket_id;
		var timeoutId;

		function updateRow() {
			element.text(ticket_id);
		}

		scope.$watch(attrs.ticketNumberDirective, function(value) {
			ticket_id=value;
			updateRow();
		});

		//controller destructor function
		element.on('$destroy', function() {
			$interval.cancel(timeoutId);
		});

		timeoutId = $interval(function() {
			updateRow();
		}, 1000);
	}

	return {
		link: link
	};

}])

//directive for modifying HTML for modal window in real time
.directive ('ticketDataDisplayDirective', [function() {

    return {
        restrict: 'E',
        scope: { ticket: '=' },
        template: '<div><b>ID: </b> {{ticket.curId}}<br> <b>Received: </b> {{ticket.curDate}}<br> <b>Name: </b> {{ticket.curName}}<br> <b>Email: </b> {{ticket.curEmail}}<br> <b>Subject: </b> {{ticket.curSubject}}<br> <b>Tech: </b> {{ticket.curTech}}<br> <b>Status: </b> {{ticket.curStatus}}</div>'
    };
    
}])

.controller ('ResetPasswordController', ['$scope', '$http', function ResetPasswordController($scope, $http) {

  $scope.empty = {};
  $scope.master = {};
  $scope.user = {};

  $scope.transmitPasswordData = function() {
    if ($scope.user.newPassword.localeCompare($scope.user.newPasswordRepeat) != 0) {
      alert("Your new passwords don't match!");
      $scope.reset();
      return;
    }
    $http.post('TicketOptions.php', {action: "pwdReset", originalPassword: $scope.user.originalPassword, newPassword: $scope.user.newPassword}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
      console.log("Reset password response: " + response.data[0]);
      var succeeded = response.data[0];
      if (succeeded == 1) {
        alert("Password has been succesfully reset!");
      }
      else {
        alert("Password reset failed! Your old password was incorrect.");
      }
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

;

