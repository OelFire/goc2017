angular.module('vez.controllers', [])

  .controller('DashCtrl', function($scope, $rootScope, $timeout, Uapi, $state) {

    var timer;

    $scope.counter = 0;

    var updateCounter = function() {
      if ($scope.counter + 1 == 8) {
        $scope.counter = 0;
      }
      $scope.counter++;
      timer = $timeout(updateCounter, 1000);
    };

    updateCounter();

    $rootScope.$watch('addrCheck', function() {
      $timeout(function() {
        $scope.departure = $rootScope.addrObj;
      }, 50);
    });

    $scope.departure = '';
    $scope.arrival = '';


    $scope.getLatitudeLongitudeDep = function(address) {
      var geocoder = new google.maps.Geocoder();
      if (typeof address == "object")
      {
        $rootScope.depObj = {};
        $rootScope.depObj.lat = address.geometry.location.lat();
        $rootScope.depObj.long = address.geometry.location.lng();
        return;
      }

      if (geocoder) {
        geocoder.geocode({
          'address': address
        }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            $rootScope.depObj = {};
            $rootScope.depObj.lat = results[0].geometry.location.lat();
            $rootScope.depObj.long = results[0].geometry.location.lng();
          } else {
            $scope.error = "No results";
          }
        });
      }
    }

    $scope.getLatitudeLongitudeAr = function(address) {
      geocoder = new google.maps.Geocoder();
      if (typeof address == "object")
      {
        $rootScope.arrivObj = {};
        $rootScope.arrivObj.lat = address.geometry.location.lat();
        $rootScope.arrivObj.long = address.geometry.location.lng();
        return;
      }

      if (geocoder) {
        geocoder.geocode({
          'address': address
        }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            $rootScope.arrivObj = {};
            $rootScope.arrivObj.lat = results[0].geometry.location.lat();
            $rootScope.arrivObj.long = results[0].geometry.location.lng();
          } else {
            $scope.error = "No results";
          }
        });
      }
    }


    $scope.clearInput = function(data) {
      if (data == 1)
        this.departure = '';
      else if (data == 2)
        this.arrival = '';
    }

    $scope.focused = function(data) {
      data.$focused = true;
    }

    $scope.blurred = function(data) {
      data.$focused = false;
    }

    $scope.switchItem = function() {
      if (!this.departure && !this.arrival)
        return;
      var tmp = this.departure;
      this.departure = this.arrival;
      this.arrival = tmp;
      delete tmp;
    }

    $scope.searchResult = function(arrival, departure) {
      delete $scope.error;
      $scope.getLatitudeLongitudeAr(arrival);
      $scope.getLatitudeLongitudeDep(departure);
      $timeout(function() {
        $scope.letsgo = true;
      }, 500);
    }

    var initializing = true

    $scope.$watch('letsgo', function() {
      if (initializing) {
        $timeout(function() {
          initializing = false;
        });
      } else {
        if (!$scope.error)
          $state.go("tab.result");
      }
    });
  })

  .controller('resultCtrl', function($scope, $stateParams, $state, $timeout, Uapi, $filter, $rootScope) {

    var timer;

    $scope.counter = 0;

    var updateCounter = function() {
      if ($scope.counter + 1 == 8) {
        $scope.counter = 0;
      }
      $scope.counter++;
      timer = $timeout(updateCounter, 2000);
    };

    updateCounter();

    $scope.backToDash = function() {
      $state.go("tab.dash");
    }

    $scope.getIcon = function(id) {
      if (id == "car")
        return "car";
      else if (id == "walk")
        return "walk"
    }

    var getMeteo = function() {
      Uapi.getMeteo().then(
        function(result) {
          $scope.meteo = new Object;
          $scope.date = $filter('date')(new Date(), 'HH', '+0100');
          $scope.meteo = result;
        });
    }

    getMeteo();

    if (!$rootScope.arrivObj || !$rootScope.arrivObj)
      $state.go("tab.dash");
    else {
      {
        $scope.loading = true;
        Uapi.findTrip($rootScope.depObj, $rootScope.arrivObj).then(
          function(result) {
            $scope.loading = false;
            $scope.travels = result;
          });
      }
    }

  })

  .controller('ChatsCtrl', function($scope, Chats) {
    // With the new view caching in Ionic, Controllers are only called
    // when they are recreated or on app start, instead of every page change.
    // To listen for when this page is active (for example, to refresh data),
    // listen for the $ionicView.enter event:
    //
    //$scope.$on('$ionicView.enter', function(e) {
    //});

    $scope.chats = Chats.all();
    $scope.remove = function(chat) {
      Chats.remove(chat);
    };
  })

  .controller('ChatDetailCtrl', function($scope, $stateParams, Chats) {
    $scope.chat = Chats.get($stateParams.chatId);
  })

  .controller('AccountCtrl', function($scope) {
    $scope.settings = {
      enableFriends: true
    };
  });
