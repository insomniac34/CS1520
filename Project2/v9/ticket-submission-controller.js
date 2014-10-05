/* Written by Tyler Raborn
 *
 */

angular.module('SupportTicketSubmission', [])

.controller('TicketSubmissionController', ['$scope', 'TicketSubmissionService', function TicketSubmissionController($scope, TicketSubmissionService) {

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
		TicketSubmissionService.transmitTicketData(ticket);
		$scope.ticket = angular.copy($scope.empty); //reset
	};

	$scope.reset = function() {
		console.log("Resetting form.");
		$scope.ticket = angular.copy($scope.empty); //reset
	};

	$scope.reset();

}])

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
	};
}]);


