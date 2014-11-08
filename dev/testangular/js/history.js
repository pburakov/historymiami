var app = angular.module("HistoryMiami", ["firebase"]);

// Login Factory
app.factory("simpleLogin", ["$firebaseSimpleLogin", function ($firebaseSimpleLogin) {
    var ref = new Firebase("https://historymiami.firebaseio.com/");
    return $firebaseSimpleLogin(ref);
}]);

// Authorization Controller ('Facebook', 'Twitter' confirmed)
app.controller("authController", ["$scope", "simpleLogin", function ($scope, simpleLogin) {
    $scope.auth = simpleLogin;
}])

// Images
app.controller('imagesDB', ["$scope", "$firebase", function ($scope, $firebase) {
    var ref = new Firebase("https://historymiami.firebaseio.com/images/");
    var sync = $firebase(ref.limit(10));

    /*
     // Objects (editable)
     var syncObject = sync.$asObject();
     syncObject.$bindTo($scope, "images");
     */

    // Arrays
    var items_array = sync.$asArray();
    $scope.images = items_array;

}]);