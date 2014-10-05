
/*
 *  CS-1520 Project 4
 *  Written by Tyler Raborn
 */

//angular.module('ModalControl', ['ngRoute', 'ngAnimate', 'ui.bootstrap'])

angular.module( 'JHangman', ['ngRoute', 'ngAnimate', 'ui.bootstrap'])

    .config(function($routeProvider) {
      $routeProvider
      .when('/', {templateUrl: 'page-home.html', controller: 'HomeController'})
      .when('/highscore', {templateUrl: 'page-highscore.html', controller: 'HighScoreController'})
      .when('/hangman', {templateUrl: 'page-hangman.html', controller: 'HangmanController'})
      ;
    })

    .controller('HighScoreController', ['$scope', function HighScoreController($scope) {
      $scope.pageClass='page-highscore';
    }])

    .controller('HangmanController', ['$scope', '$http', '$log', '$modal', '$rootScope', function HangmanController($scope, $http, $log, $modal, $rootScope) {
      $scope.pageClass='page-hangman';
      $scope.theWord = " ";
      $scope.letters = []; //an array containing each of the letters of the word.
      $scope.guessed = {};
      $scope.succesfulGuesses = 0;
      $scope.notifications = [];
      $scope.words = []; //for ensuring no duplicates IN A SINGLE ROUND. Duplicates arent checked for in between restarts obviously.

      $scope.globalScore = 0;
      $scope.totalGuesses = 0;
      $scope.incorrectGuesses = 0;
      $scope.started = true;
      $scope.showWord = false;
      $scope.totalRounds = 0;
      $scope.roundsWon = 0;

      $scope.divList = [];
      $scope.master = {};
      $scope.guessedList = [];

      //check for event emitted by external modal window controller function, use this to trigger next word
      $rootScope.$on('modalResult', function(result) {$scope.giveUp(); $scope.startNewRound()});

      $scope.startNewRound = function() {
        $scope.started = false;
        $http.post('Hangman.php', {action: 'getNewWord'}, {'Content-Type': 'application/x-www-form-urlencoded'}).then(function(response) {
          
          //check if word has already been used or if words are done...
          if ($scope.words.length === 39) {
            $scope.notifications.push({msg: 'Congratulations, you won!', type: 'success'});
            return;
          }
          if ($scope.words.indexOf(response.data[0][0]) != -1) {
            $scope.reset();
            return;
          }
          $scope.theWord = response.data[0][0];
          $scope.divList = new Array($scope.theWord.length);
          for (var i = 0; i < $scope.theWord.length; i++) {
            $scope.divList[i] = false; //initialize div colors...
            $scope.letters.push({
                name: $scope.theWord.charAt(i), 
                display: '?',
                guessed: false,
                guesses: 0,
                id: i
            });
          }
          $scope.guessed.letters = new Array($scope.theWord.length);
        });
      };

      $scope.guess = function(id, $event) {
        if (!$scope.guessed.letters[id]) {
          return;
        }
        //ensure letter has not already been guessed:
        if ($scope.guessedList.indexOf($scope.guessed.letters[id]) != -1) {
          $scope.guessed.letters[id] = ''; //clear if already guessed
          return;
        }
        if ($scope.guessed.letters[id].length > 1) {
          return;
        }

        $scope.guessedList.push(angular.copy($scope.guessed.letters[id])); //add to global list of guessed letters
        $log.info("guessing letter # " + id + " of word " + $scope.theWord);
        var shouldBeCleared = false;
        angular.forEach($scope.letters, function(obj) {
          $log.info("Checking if " + $scope.guessed.letters[id] + " is equal to " + obj.name);
          if (obj.name.localeCompare($scope.guessed.letters[id]) === 0) {
            $scope.divList[obj.id] = true;
            $scope.guessed.letters[obj.id] = angular.copy($scope.guessed.letters[id]);
            $scope.letters[obj.id].display = angular.copy($scope.guessed.letters[id]);
            $scope.letters[obj.id].guessed = true;
            //$scope.letters[obj.id].guesses+=1;
            if (obj.id !== id) $scope.succesfulGuesses+=1;
          }
          if (obj.id === id) {
            obj.guesses+=1;
            $log.info("comparing " + $scope.guessed.letters[id] + " to " + obj.name);
            if ($scope.guessed.letters[id].localeCompare(obj.name) == 0) {
              $log.info("succesful guess of letter " + $scope.guessed.letters[id] + " in " + $scope.theWord);
              obj.display = obj.name;
              obj.guessed = true;
              $scope.succesfulGuesses+=1;
              $scope.divList[id] = true;
            }
            else {
              $scope.incorrectGuesses+=1;
              shouldBeCleared = true;
            }
          }
        });
        if (shouldBeCleared) {
          $scope.guessed.letters[id] = ''; //clear guessed only if incorrect; else leave it
        }
        $scope.totalGuesses+=1;
        if ($scope.incorrectGuesses === 7) {
          $scope.totalRounds+=1;
          $scope.notifications = [];
          $scope.notifications.push({msg: 'You lose! The word was ' + $scope.theWord, type: 'danger'});
          $scope.reset();
          $scope.globalScore = 0;
          //$scope.startNewRound();
        }
        else if ($scope.succesfulGuesses === $scope.theWord.length) {
          $scope.roundsWon+=1;
          $scope.totalRounds+=1;
          $scope.notifications = [];
          var winningPercentage = $scope.roundsWon / $scope.totalRounds;

          $scope.notifications.push({msg: 'Congratulations, you guessed the word! Winning Percentage: ' + winningPercentage + '\nTotal Rounds Played: ' + $scope.totalRounds + '\nTotal Rounds Won: ' + $scope.roundsWon, type: 'success'});
          $scope.globalScore+=(10*(8-$scope.incorrectGuesses)); //UPDATE SCORE!
          $scope.reset();
        }
      };

      $scope.closeNotification = function(index) {
          $scope.notifications.splice(index, 1);
      };  

      $scope.giveUp = function() {
        $scope.showWord = true;
        $scope.theWord = '';
        $scope.letters.length = 0; 
        $scope.guessed = angular.copy($scope.master);
        $scope.succesfulGuesses = 0;
        $scope.incorrectGuesses = 0;
        $scope.globalScore = 0;
        $scope.totalGuesses = 0; 
        $scope.guessedList = [];       
      };

      $scope.reset = function() {
        $scope.words.length = 0;
        $scope.guessedList = [];
        $scope.showWord = false;
        $scope.theWord = '';
        $scope.letters.length = 0; 
        $scope.guessed = angular.copy($scope.master);
        $scope.succesfulGuesses = 0;
        $scope.incorrectGuesses = 0;
        $scope.totalGuesses = 0;
        $scope.startNewRound();
        };        
    }])

    .controller('HomeController', ['$scope', function HomeController($scope) {
      $scope.pageClass='page-home';
      
      $scope.treeCategories = [
                                {id: 0, name: 'User', subCategories: [{name: 'Login', link: '#', dataTarget: "#", toggle: ""}, {name: 'Register', link: '#', dataTarget: '#userRegistrationModal', toggle: "modal"}], activeSubCategories: []}, 
                                {id: 1, name: 'Game', subCategories: [{name: 'Start New Game', link: '#hangman', dataTarget: "", toggle: ""}, {name: 'Resume Previous Game', link: '#', dataTarget: "#gameModal", toggle: "modal"}], activeSubCategories: []}, 
                                {id: 2, name: 'High Scores & Achievements', subCategories: [{name: 'High Scores', link: '#highscore', dataTarget: "#", toggle: ""}, {name: 'Other Stats', link: '#', dataTarget: "#", toggle: ""}], activeSubCategories: []},
                                {id: 3, name: 'About', subCategories: [{name: 'Technical', link: 'https://angularjs.org/', dataTarget: "#", toggle: ""}, {name: 'Legal', link: '#', dataTarget: "#", toggle: ""}], activeSubCategories: []}
                              ];

      //$scope.pageClass = 'index';
      $scope.subCategories = [];
      $scope.curNode = -1;
      $scope.expanded = false;

      $scope.onClick = function(nodeId) {

        if ($scope.expanded) {
          if ($scope.curNode === nodeId) {
            $scope.curNode = -1;
            $scope.expanded = false;

            while($scope.treeCategories[nodeId].activeSubCategories.length > 0) {
                $scope.treeCategories[nodeId].activeSubCategories.pop();
            }                
          }
          else { 
            //else a new menu is being clicked on. this is allowed. FIRST close current menu
            while($scope.treeCategories[$scope.curNode].activeSubCategories.length > 0) {
                $scope.treeCategories[$scope.curNode].activeSubCategories.pop();
            }

            //NOW open new menu:    
            $scope.curNode = nodeId;
            angular.forEach($scope.treeCategories[nodeId].subCategories, function(category) {
              var subCategory = angular.copy(category);
              $scope.treeCategories[nodeId].activeSubCategories.push(subCategory);
            });                             
          }
        }
        else {
          $scope.curNode = nodeId;
          $scope.expanded = true;

          angular.forEach($scope.treeCategories[nodeId].subCategories, function(category) {
            var subCategory = angular.copy(category);
            $scope.treeCategories[nodeId].activeSubCategories.push(subCategory);
          });     
        }
      };

    }])

  ;

  //controller for launching modal window
  var ModalController = function ($scope, $modal, $log) {

  $scope.items = ['item1', 'item2', 'item3'];

  $scope.open = function (size) {

    var modalInstance = $modal.open({
      templateUrl: 'modalContent.html',
      controller: ModalInstanceController,
      size: size,
      resolve: {
        items: function () {
          return $scope.items;
        }
      }
    });

    modalInstance.result.then(function (selectedItem) {
      $scope.selected = selectedItem;
    }, function () {
      $log.info('Modal dismissed at: ' + new Date());
    });
  };
};

//controller for modal window instance
var ModalInstanceController = function ($scope, $modalInstance, items, $rootScope) {

  $scope.items = items;
  $scope.selected = {
    item: $scope.items[0]
  };

  $scope.ok = function () {
    $rootScope.$emit('modalResult', true);
  };

  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };
};
