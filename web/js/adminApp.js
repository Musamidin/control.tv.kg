var app = angular.module("AdminApp",['angularUtils.directives.dirPagination']);

app.controller("AdminIndexAppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };
$scope.tvlist = null;
$scope.data = {};

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


$scope.pageChanged = function(newPage) {
         $scope.getData(newPage,$scope.mainlistPerPage,$('#report-status').val());
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

$scope.getData(1,$scope.mainlistPerPage,$('#report-status').val());


$scope.onAccept = function(){
  $('#comment').val('');
  $('#actionBtn').html('Принять');
  $('#actionBtn').attr('data-id',1);
  $('#modal-info').modal({ keyboard: false });
};

$scope.onReject = function(){
  $('#comment').val('');
  $('#actionBtn').html('Отвергнуть');
  $('#actionBtn').attr('data-id',2);
  $('#modal-info').modal({ keyboard: false });
};


$(document).on('change', '#report-status', function(){
  if(this.value == 0 || this.value == -1){
    $('.select-all-chbx').show();
  }else{
    $('.select-all-chbx').hide();
  }
  $scope.getData(1,$scope.mainlistPerPage,this.value);
});

//select all checkboxes
$(document).on('change', '.select-all-chbx', function(){
  $(".checkbox").prop('checked', $(this).prop("checked"));
  if($('.checkbox:checked').length > 0){
      $('.lg-btn').show();
    }else if($('.checkbox:checked').length <= 0){
      $('.lg-btn').hide();
    }
});

$(document).on('change', '.checkbox', function(){
    if($(this).prop("checked") == false){
        $(".select-all-chbx").prop('checked', false);
        $('.lg-btn').hide();
    }
    if($(this).prop("checked") == true){
        $(".select-all-chbx").prop('checked', true);
        $('.lg-btn').hide();
    }
    if($('.checkbox:checked').length > 0){
      $('.lg-btn').show();
    }
    if($('.checkbox:checked').length == $('.checkbox').length ){
        $(".select-all-chbx").prop('checked', true);
        if($('.checkbox:checked').length > 0){
        $('.lg-btn').show();
      }
    }
});

$scope.onAction = function(item){
  var ids = [];
  var actionId = item.currentTarget.getAttribute("data-id");
    for(var i = 0; i < $('.checkbox:checked').length; i++){
      ids.push($('.checkbox:checked')[i].value);
      //console.log( $('.checkbox:checked')[i].value );
    }
  
  $scope.data['token'] = $('#token').val();
  $scope.data['description'] = $('#comment').val();
  $scope.data['sts'] = 0;
  $scope.data['page'] = 1;
  $scope.data['shpcount'] = 15;
  $scope.data['ids'] = ids;
  $scope.data['status'] = actionId;
  $http({
    method: 'POST',
    url: '/onaction',
    data: $scope.data
  }).then(function successCallback(response) {
      var state = eval(response.data);
      if(state.status == 0){
          $scope.mainlistview = state.data.mainlistview;
          $scope.totalmainlist = state.data.count;
          $('#modal-info').modal('hide');
          $(".select-all-chbx").prop('checked', false);
          $('.lg-btn').hide();

      }else{
        alert('Error!');
      }
    }, function errorCallback(response) {
          //console.log(response);
  });

};


}).controller("AdminExportAppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();
var dates = 0;
$scope.totacount = 0;
$scope.tvlist = null;

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

$scope.getTvList();

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

$scope.getDatas('5 канал'); //$('#report-status').val()


$scope.onAccept = function(){
  $('#actionBtn').html('Принять');
  $('#modal-info').modal({ keyboard: false });
};

$scope.onReject = function(id){
  $('#actionBtn').html('Отвергнуть');
  $('#actionBtn').attr('data-id',id);
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

$('.getbydatetime').datepicker({
  startDate: '+1d',
  multidate: true,
  format: "yyyy-mm-dd",
  startView: 0,
  language: "ru",
  //autoclose: true,
  orientation: "bottom right"
}).on('hide', function(e) {
  console.log(e.currentTarget.value);
  //console.log(moment(e.dates[1]).format('YYYY-MM-DD') );
});



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
});