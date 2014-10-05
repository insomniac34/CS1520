/* Written by Tyler Raborn
 *
 */

 angular.module( 'AdministratorPortal', [])

 .controller('AdminPortalTicketDisplayController', ['$scope', '$http', '$window', function AdminPortalTicketDisplayController($scope, $http, $window) {

	/*
	$scope.$on('selectedRow', function(event, args) {

	});
 */

/*	
	$scope.ticketData = {};
	console.log("inside of ticket display controller...");

	$.getJSON('TicketLookup.php', {'ticketID' : $scope.ticket_id}, function(data) {
	console.log("Result from PHP: " + data[]);
    $scope.ticketData = angular.copy(data);
	});
 */		


}])

 .controller('AdminPortalDataDisplayController', ['$scope', '$http', '$window', function AdminPortalDataDisplayController($scope, $http, $window) {

    $scope.ticket = {};

     console.log("Entering admin portal controller.");

     $scope.ticket_id = "0";
     $scope.$watch('ticket_id', function(id) {
         console.log("Selected value: " + id);
       //$scope.$emit('selectedRow', {row: id}); //emit value of selected row such that the ticket controller can receive it.

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
/*
    $http({
        method: 'POST',
        url: 'TicketLookup.php',
        data: {
           ticketId: id,
        },
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    });	


    $.getJSON('TicketLookup.php', {'ticketID' : $scope.ticket_id}, function(data) {
          console.log("Result from PHP: " + data.toString());
          $scope.ticketData = angular.copy(data);
    });
     */


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

//directive for modifying modal in real time
.directive ('ticketDataDisplayDirective', [function() {
    
    return {
        restrict: 'E',
        scope: { ticket: '=' },
        template: '<div><b>ID: </b> {{ticket.curId}}<br> <b>Received: </b> {{ticket.curDate}}<br> <b>Name: </b> {{ticket.curName}}<br> <b>Email: </b> {{ticket.curEmail}}<br> <b>Subject: </b> {{ticket.curSubject}}<br> <b>Tech: </b> {{ticket.curTech}}<br> <b>Status: </b> {{ticket.curStatus}}</div>'
    };
    
}])

;
