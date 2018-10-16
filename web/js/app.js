var app = angular.module("myApp",['angularUtils.directives.dirPagination']);

app.controller("AppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };

$scope.total = null;
$scope.bystatus = 0;
$scope.bytv = 0;
$scope.tvlist = null;
$scope.data = {};
$scope.chdata ={};
var dateft = moment(new Date()).format('YYYY-MM-DD');
$scope.dfdt = dateft +' / '+ dateft;

$scope.pageChanged = function(newPage) {
         $scope.getUserData(newPage,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv);
  };

$scope.getUserData = function(pageNum,showPageCount,sts,daterange,bytv){
  $http.get('/getuserdata?page=' 
  			+ pageNum +'&shpcount='
  			+ showPageCount+'&sts='+ sts+'&daterange='
  			+daterange+'&token='
  			+ $('#token').val()+'&bytv='
  			+bytv)
        .then(function(result) {
          var respdata = eval(result.data);
          if(respdata.status == 0){
                $scope.mainlistview = eval(respdata.data.mainlistview);
                $scope.totalmainlist = eval(respdata.data.count);
                $scope.total = eval(respdata.data.total);
          } else if(respdata.status > 0){
              alert(respdata.msg);
          }
        }, function errorCallback(response) {
            //console.log(response);
        });
  };
    


$scope.getUserData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv);

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


$(document).on('change', '#report-status', function(){
	$scope.bystatus = this.value;
	$scope.getUserData(1,$scope.mainlistPerPage,this.value,$scope.dfdt,$scope.bytv);
	if(this.value > 0){ $('.select_all').hide(); }else{ $('.select_all').show(); }
	$('#expt-excel').attr("href","/exptexcel?token="+$('#token').val()
    + "&daterange="+ $scope.dfdt+"&bytv="+ $scope.bytv+'&sts='+this.value);
});

$(document).on('change', '#sortbytv', function(){
	$scope.bytv = this.value;
	$scope.getUserData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,this.value);
	if($('#report-status').val() > 0){ $('.select_all').hide(); }else{ $('.select_all').show(); }
	$('#expt-excel').attr("href","/exptexcel?token="+$('#token').val()
    + "&daterange="+ $scope.dfdt+"&bytv="+ this.value +'&sts='+$scope.bystatus);
});

$scope.addformaction = function(){
	$scope.data['token'] = $('#token').val();
	$http({
	  method: 'POST',
	  url: '/setdata',
	  data: $scope.data
	}).then(function successCallback(response) {
	    var state = eval(response.data);
	    if(state.status == 0){
	      $('#mainhub-dates').datepicker('update','');
         	$scope.getUserData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv);
            $scope.pagination.current = 1;
            document.getElementById("addForm").reset();
            $('#addstate').html('(Запись успешно добавлен!)').css('color','#8fff00');
	    }else{
	    	$('#addstate').html(state.message).css('color','red');
	    }
	  }, function errorCallback(response) {
	        //console.log(response);
	});
};

/**********START DATE PICKER RANG***************/
	var now = new Date();
    $('.getbydatetime').daterangepicker({
    	//"autoUpdateInput": false,
    	//"singleDatePicker": false,
    	"locale": {
        "format": "YYYY-MM-DD", //MM/DD/YYYY
        "separator": " / ",
        "applyLabel": "Принять",
        "cancelLabel": "Отмена",
        "fromLabel": "С",
        "toLabel": "По",
        "customRangeLabel": "Custom",
        "weekLabel": "W",
        "daysOfWeek": ["Вс","Пн","Вт","Ср","Чт","Пт","Сб"],
        "monthNames": ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
        "firstDay": 1,

    },
    	"startDate": now,
    	alwaysShowCalendars: false,
    	//"dateLimit": { "days": 31 } //only 31 day can select 
    }, function(start, end, label) {
    	var df = moment(start).format('YYYY-MM-DD');
    	var dt = moment(end).format('YYYY-MM-DD');
    	var dfdt = df+'/'+dt;
    	//console.log( $('.getbydatetime').val() );
    	$scope.dfdt = dfdt;
    	$scope.getUserData( 1,$scope.mainlistPerPage,$scope.bystatus,dfdt,$scope.bytv );
    	$('#expt-excel').attr("href","/exptexcel?token="+$('#token').val()
    + "&daterange="+ dfdt +"&bytv="+ $scope.bytv+'&sts='+$scope.bystatus);
		//console.log(  +' / '+ );
    	}
    );

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
	$("#status-response, #addstate").html('');
	//$('#addstate').html('');
	$scope.getTvList();
	//console.log($scope.tvlist);
	$('#modal-info-add-form').modal({ keyboard: false });
};

$scope.importbtn = function(){
	$scope.getTvList();
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
	var ids = [];
	for(var i = 0; i < $('.checkbox:checked').length; i++){
		ids.push($('.checkbox:checked')[i].value);
		//console.log( $('.checkbox:checked')[i].value );
	}

	$scope.data['token'] = $('#token').val();
	$scope.data['ids'] = ids;

	$http({
	  method: 'POST',
	  url: '/remove',
	  data: $scope.data
	}).then(function successCallback(response) {
	    var state = eval(response.data);
	    if(state.status == 0){
         	$scope.getUserData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv);
            $scope.pagination.current = 1;
            $(".select_all").prop('checked', false);
			$('#removebtn').hide();

	    }else{
	    	alert('Error!');
	    }
	  }, function errorCallback(response) {
	        //console.log(response);
	});




};

/*$("#mainhub-phone").mask("999999999",{placeholder:"XXX XX XX XX"}); */


}).filter("status", function()
{	
	var retval = '';
			return function(input){
				switch(Number(input)){
					case 0 : { retval = 'В обработке'; } break;
					case 1 : { retval = 'Принято'; } break;
					case 2 : { retval = 'Отвергнуто'; } break;
					default : { retval = ''; } break;
				}
				return retval;
			}
}).filter("formatDatetime", function ()
{
    return function (input) {
      if(jQuery.isEmptyObject(input) == false){
				var dt = input.slice(0, -4).split('-');
				var td = dt[2].split(' ');
        return td[0]+'.'+dt[1]+'.'+dt[0]+' '+td[1];
      }else{
        return '';
      }
    }
}).filter("fixedto", function()
{
	return function(input){
    if(jQuery.isEmptyObject(input) == false){
		    return parseFloat(input).toFixed(2);
    }else{
      return parseFloat(0).toFixed(2);
    }
	}
}).filter('tSumm', function() {
        return function(data, key) {
            if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
                return 0;
            }
			var sum = 0;
            for (var i = data.length - 1; i >= 0; i--) {
                sum += parseFloat(data[i][key]);
            }
            return sum;
        };
}).filter('tSumms', function() {
        return function(data, key) {
            if (typeof(data) === 'undefined' || typeof(key) === 'undefined') {
                return 0;
            }
			var sum = 0;
            for (var i = data.length - 1; i >= 0; i--) {
                sum += parseFloat(data[i][key]);
            }
            return sum.toFixed(2);
        };
});

