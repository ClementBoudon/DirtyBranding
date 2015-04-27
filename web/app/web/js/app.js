var DirtyBranding = angular.module('DirtyBranding',['ngRoute','ngResource']);

DirtyBranding.config(['$routeProvider', function($routeProvider){
    $routeProvider
        .when('/',{
            templateUrl: 'partials/home.html',
            controller:'HomeController'
        })
        .when('/results/:idea',{
            templateUrl: 'partials/results.html',
            controller:'ResultsController'
        })
        .otherwise({
            redirectTo: '/'
        });


}]);