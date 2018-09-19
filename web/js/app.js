var app = angular.module("myApp",['angularUtils.directives.dirPagination']);

app.controller("AppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };

$scope.bystatus = 0;
$scope.tvlist = null;
$scope.data = {};
$scope.chdata ={};

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

$scope.getTvList = function(){
  $http.get('/gettvlist') // +'&pagenum='+pnum
        .then(function(result) {
          var respdata = eval(result.data);
          if(respdata.status == 0){
                $scope.tvlist = eval(respdata.data.tvlist);
          } else if(respdata.status > 0){
              alert(respdata.msg);
          }
        }, function errorCallback(response) {
            //console.log(response);
        });
  };

$scope.addformaction = function(){
	$scope.data['token'] = $('#token').val();
	$http({
	  method: 'POST',
	  url: '/setdata',
	  data: $scope.data
	}).then(function successCallback(response) {
	    var state = response.data;
	    if(state.status == 0){
	      $('#mainhub-dates').datepicker('update','');
	      //$('#statisticModal').modal({ keyboard: false });
	      //$scope.init1 = function(){ };
	      //$scope.datas = response.data.data;
	    }else{
	      alert(state.msg);
	    }
	  }, function errorCallback(response) {
	        //console.log(response);
	});
	console.log($scope.data);
	//
	$scope.data = [];
	console.log($scope.data);
};


/**********START DATE PICKER***************/
//$('#clients-date_of_issue').mask("99/99/9999", {placeholder: 'Дата выдачи (пасспорт) д/м/г'});
//$.fn.datepicker.defaults.format = "mm/dd/yyyy";
var forbidden=['2018-09-20','2018-09-21'];
var Nonbusinessday = ["2018-09-26", "2018-09-27"];
var Holiday = []; //["2018-09-20", "2018-09-21"];

$('#mainhub-dates').datepicker({
	beforeShowDay: function(date){
		var datestring = date.toJSON().substring(0,10);
		var dofw = new Date().getDay();
		if(dofw == 3){
				//Holiday = ["2018-09-20", "2018-09-21"];
			if (Nonbusinessday.indexOf(datestring) != -1) {
		        return false;
		    }
		        //  else if (Holiday.indexOf(datestring) != -1) {
		        //                 return false;
		        // }
		    else {
		        return true;
		    }
		}
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
	$scope.getTvList();
	//console.log($scope.tvlist);
	$('#modal-info-add-form').modal({ keyboard: false });
};

$scope.importbtn = function(){
	$("#status-response").html('');
	$('#modal-info-add-import').modal({ keyboard: false });
};


//select all checkboxes
$(document).on('change', '.select_all', function(){
	$(".checkbox").prop('checked', $(this).prop("checked"));
	if($('.checkbox:checked').length > 0){
    	$('#removebtn').show();
    }else if($('.checkbox:checked').length <= 0){
    	$('#removebtn').hide();
    }
});

$(document).on('change', '.checkbox', function(){
    if($(this).prop("checked") == false){
        $(".select_all").prop('checked', false);
        $('#removebtn').hide();
    }
    if($(this).prop("checked") == true){
        $(".select_all").prop('checked', true);
        $('#removebtn').show();
    }
    if($('.checkbox:checked').length > 0){
    	$('#removebtn').show();
    }
    if($('.checkbox:checked').length == $('.checkbox').length ){
        $(".select_all").prop('checked', true);
      	if($('.checkbox:checked').length > 0){
    		$('#removebtn').show();
    	}
    }
});

$scope.removedata = function(){
	for(var i = 0; i < $('.checkbox:checked').length; i++){
		//console.log( $('.checkbox:checked')[i].value );
	}
};

$("#mainhub-phone").mask("999999999",{placeholder:"XXX XX XX XX"});


});

