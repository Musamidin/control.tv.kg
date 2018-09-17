var app = angular.module("myApp",['angularUtils.directives.dirPagination']);

app.controller("AppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };


$scope.bystatus = 0;


$scope.importbtn = function(){
	$('#modal-info-add-form').modal({ keyboard: false });
};


});

