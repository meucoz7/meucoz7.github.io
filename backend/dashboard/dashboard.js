'use strict';

angular.module('myApp.contacts', ['ngRoute'])

.controller('DashCtrl', ['$scope','$firebaseArray','loginService', function($scope,$firebaseArray,loginService) {
   
   var ref = new Firebase('https://imperiya1-1a8dc.firebaseio.com/orders')
    $scope.orders = $firebaseArray(ref);
    var uref = new Firebase('https://imperiya1-1a8dc.firebaseio.com/users')
     
    $scope.users = $firebaseArray(uref);
}]);