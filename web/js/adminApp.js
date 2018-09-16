var app = angular.module("AdminApp",['angularUtils.directives.dirPagination']);

app.controller("AdminIndexAppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };


$scope.bystatus = 0;



$scope.pageChanged = function(newPage) {
         $scope.getData(newPage,$scope.mainlistPerPage,$scope.bystatus);
  };

$scope.getData = function(pageNum,showPageCount,sts){
  $http.get('/getdata?page=' + pageNum +'&shpcount='+ showPageCount+'&sts='+ sts) // +'&pagenum='+pnum
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

$scope.getData(1,$scope.mainlistPerPage,$scope.bystatus);


$scope.onAccept = function(){
  $('#actionBtn').html('Принять');
  $('#modal-info').modal({ keyboard: false });
};

$scope.onReject = function(){
  $('#actionBtn').html('Отвергнуть');
  $('#modal-info').modal({ keyboard: false });
};



}).controller("AdminExportAppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();
var dates = 0;
$scope.totacount = 0;

$scope.getDatas = function(channel){
  $http.get('/getdatas?dates=' + dates+'&channel='+channel) // +'&pagenum='+pnum
        .then(function(result) {
          var respdata = eval(result.data);
          if(respdata.status == 0){
                $scope.mainlistview = eval(respdata.data.mainlistview);
                $scope.totacount = eval(respdata.data.count);
          } else if(respdata.status > 0){
              alert(respdata.msg);
          }
        }, function errorCallback(response) {
            //console.log(response);
        });
  };

$scope.getDatas($('#report-status').val());


$scope.onAccept = function(){
  $('#actionBtn').html('Принять');
  $('#modal-info').modal({ keyboard: false });
};

$scope.onReject = function(){
  $('#actionBtn').html('Отвергнуть');
  $('#modal-info').modal({ keyboard: false });
};

$(document).on('change','#report-status',function(){
  $scope.getDatas(this.value);
  $('#downld').attr("href","/download?id="+this.value);
});

$(document).on('click','#sendmail',function(){

    $http.get('/mailer?channel='+$('#report-status').val()) // +'&pagenum='+pnum
        .then(function(result) {
          var respdata = eval(result.data);
          if(respdata.status == 0){
                // $scope.mainlistview = eval(respdata.data.mainlistview);
                // $scope.totacount = eval(respdata.data.count);
          } else if(respdata.status > 0){
              alert(respdata.msg);
          }
        }, function errorCallback(response) {
            //console.log(response);
        });
});



});