angular.module('SupportTicketSubmission', [])

.service('TicketSubmissionService', ['$http', function TicketSubmissionService($http) {
	this.transmitTicketData = function(ticketData) {
		console.log("transmitting data...");
		$http({
			method: 'POST',
			url: url,
			data: $.param({
				firstName: ticketData.firstName,
				lastName: ticketData.lastName,
				email: ticketData.email,
				comments: ticketData.problem
			}),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		});	
	};
}]);