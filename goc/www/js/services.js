angular.module('vez.services', [])

  .factory('Uapi', function($http) {
    return {
      getAddr: function(lat, long) {
        return $http.get("http://goc2017.synoria.com/api/getHumanLoc.php?lat=" + lat + "&long=" + long).then(function(response) {
          return response.data;
        });
      },
      getMeteo: function() {
        return $http.get("http://goc2017.synoria.com/api/weather.php").then(function(response) {
          return response.data;
        });
      },
      findTrip: function(dep, arr) {
        return $http.get("http://goc2017.synoria.com/api/findTrip.php?longStart=" + dep.long + "&latStart=" + dep.lat + "&longEnd=" + arr.long + "&latEnd=" + arr.lat).then(function(response) {
          return response.data;
        });
      }
    }
  })
  /*findTrip: function(dep, arr) {
    return $http.get("http://goc2017.synoria.com/api/findTrip.php?longStart=" + dep.long + "?latStart=" dep.lat + "?longEnd=" + arr.long + "?latEnd=" arr.lat).then(function(response) {
      console.log(response.data);
      return response.data;
    });
  }*/

  .factory('Chats', function() {
    // Might use a resource here that returns a JSON array

    // Some fake testing data
    var chats = [{
      id: 0,
      name: 'Ben Sparrow',
      lastText: 'You on your way?',
      face: 'img/ben.png'
    }, {
      id: 1,
      name: 'Max Lynx',
      lastText: 'Hey, it\'s me',
      face: 'img/max.png'
    }, {
      id: 2,
      name: 'Adam Bradleyson',
      lastText: 'I should buy a boat',
      face: 'img/adam.jpg'
    }, {
      id: 3,
      name: 'Perry Governor',
      lastText: 'Look at my mukluks!',
      face: 'img/perry.png'
    }, {
      id: 4,
      name: 'Mike Harrington',
      lastText: 'This is wicked good ice cream.',
      face: 'img/mike.png'
    }];

    return {
      all: function() {
        return chats;
      },
      remove: function(chat) {
        chats.splice(chats.indexOf(chat), 1);
      },
      get: function(chatId) {
        for (var i = 0; i < chats.length; i++) {
          if (chats[i].id === parseInt(chatId)) {
            return chats[i];
          }
        }
        return null;
      }
    };
  });
