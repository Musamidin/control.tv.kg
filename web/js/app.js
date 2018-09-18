var app = angular.module("myApp",['angularUtils.directives.dirPagination']);

app.controller("AppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };

$scope.bystatus = 0;

$scope.pageChanged = function(newPage) {
         $scope.getUserData(newPage,$scope.mainlistPerPage,$scope.bystatus);
  };

$scope.getUserData = function(pageNum,showPageCount,sts){
  $http.get('/getuserdata?page=' + pageNum +'&shpcount='+ showPageCount+'&sts='+ sts) // +'&pagenum='+pnum
        .then(function(result) {
          var respdata = eval(result.data);
          if(respdata.status == 0){
                $scope.mainlistview = eval(respdata.data.mainlistview);
                $scope.totalmainlist = eval(respdata.data.count);
          } else if(respdata.status > 0){
              alert(respdata.msg);
          }
        }, function errorCallback(response) {
            //console.log(response);
        });
  };

$scope.getUserData(1,$scope.mainlistPerPage,$scope.bystatus);





/**********START DATE PICKER***************/
//$('#clients-date_of_issue').mask("99/99/9999", {placeholder: 'Дата выдачи (пасспорт) д/м/г'});
//$.fn.datepicker.defaults.format = "mm/dd/yyyy";
var forbidden=['2018-09-20','2018-09-21'];
$('#mainhub-dates').datepicker({
	beforeShowDay:function(Date){
        var curr_date = Date.toJSON().substring(0,10);
        //console.log(curr_date);
        if (forbidden.indexOf(curr_date) != -1) return false;        
    },
	startDate: new Date(),
	//minDate: new Date().getDate()+1,
	multidate: true,
	format: "dd/mm/yyyy",
	startView: 0,
	language: "ru",
	//autoclose: true,
	orientation: "bottom right"
}).on('hide', function() { });

/********END DATE PICKER****************/

$scope.addform = function(){
	$("#status-response").html('');
	$('#modal-info-add-form').modal({ keyboard: false });
};

$scope.importbtn = function(){
	$("#status-response").html('');
	$('#modal-info-add-import').modal({ keyboard: false });
};



$("#mainhub-phone").mask("999999999",{placeholder:"XXX XX XX XX"});


});

