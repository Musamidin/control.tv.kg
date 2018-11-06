var app = angular.module("AdminApp",['angularUtils.directives.dirPagination']);

app.controller("AdminIndexAppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();

$scope.totalmainlist = 0;
$scope.mainlistPerPage = 15; // this should match however many results your API puts on one page
$scope.pagination = { current: 1 };

$scope.bystatus = 0;
$scope.bytv = 0;
$scope.sortbycli = 0;

var dateft = moment(new Date()).format('YYYY-MM-DD');
$scope.dfdt = dateft +' / '+ dateft;

$scope.data = {};
$scope.userlist = {};

$scope.pageChanged = function(newPage) {
         //$scope.getData(newPage,$scope.mainlistPerPage,$('#report-status').val());
         $scope.getData(newPage,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv,$scope.sortbycli);
  };

$scope.getData = function(pageNum,showPageCount,sts,daterange,bytv,sortbycli){
  $http.get('/getdata?page=' 
    + pageNum +'&shpcount='
    + showPageCount+'&sts='
    + sts+'&token='
    + $('#token').val()+'&daterange='
    + daterange+'&bytv='
    + bytv+'&sortbycli='+ sortbycli)
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

$scope.getData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv,$scope.sortbycli);


/**********START DATE PICKER RANG***************/
  var now = new Date();
    $('.getbydatetime').daterangepicker({
      //"autoUpdateInput": false,
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
        "firstDay": 1
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
      $scope.getData($scope.pagination.current,$scope.mainlistPerPage,$scope.bystatus,dfdt,$scope.bytv,$scope.sortbycli );

      $('#exptexcel').attr("href","/exptexceladm?token="+$('#token').val()
    + "&daterange="+ dfdt +"&bytv="+ $scope.bytv+'&sts='+$scope.bystatus+'&sortbycli='+$scope.sortbycli);
      }
    );

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
  $scope.bystatus = this.value;
  $scope.getData(1,$scope.mainlistPerPage,this.value,$scope.dfdt,$scope.bytv,$scope.sortbycli);
  if(this.value > 0){ $('.select_all').hide(); }else{ $('.select_all').show(); }
  $('#exptexcel').attr("href","/exptexceladm?token="+$('#token').val()
    + "&daterange="+ $scope.dfdt +"&bytv="+ $scope.bytv+'&sts='+this.value+'&sortbycli='+$scope.sortbycli);
});

$(document).on('change', '#sortbytv', function(){
  $scope.bytv = this.value;
  $scope.getData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,this.value,$scope.sortbycli);
  if($('#report-status').val() > 0){ $('.select_all').hide(); }else{ $('.select_all').show(); }
  $('#exptexcel').attr("href","/exptexceladm?token="+$('#token').val()
    + "&daterange="+ $scope.dfdt +"&bytv="+ this.value+'&sts='+$scope.bystatus+'&sortbycli='+$scope.sortbycli);
});
$(document).on('change', '#sortbycli', function(){
  $scope.sortbycli = this.value;
  $scope.getData(1,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv,this.value);
  if($('#report-status').val() > 0){ $('.select_all').hide(); }else{ $('.select_all').show(); }
  $('#exptexcel').attr("href","/exptexceladm?token="+$('#token').val()
    + "&daterange="+ $scope.dfdt +"&bytv="+ $scope.bytv+'&sts='+$scope.bystatus+'&sortbycli='+this.value);
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
  $scope.data['ids'] = ids;
  $scope.data['status'] = actionId;
  $http({
    method: 'POST',
    url: '/onaction',
    data: $scope.data
  }).then(function successCallback(response) {
      var state = eval(response.data);
      if(state.status == 0){
          $scope.getData($scope.pagination.current,$scope.mainlistPerPage,$scope.bystatus,$scope.dfdt,$scope.bytv,$scope.sortbycli );
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
}).controller("SettingsCtrl", function($scope,$http){

$('#getbydatetime').datepicker({
  startDate: '+1d',
  multidate: true,
  format: "yyyy-mm-dd",
  startView: 0,
  language: "ru",
  //autoclose: true,
  orientation: "bottom right"
}).on('changeDate', function(e) {
  //list-dates
    var dates = [];
    for(var i = 0; i < e.dates.length; i++){
      if(e.dates.length > 0){
        dates.push(moment(e.dates[i]).format('DD/MM/YYYY'));
      }
    }
  
  console.log(dates);
});

  $scope.getUserList = function(){
    $http.get('/getuserlist?token='+$('#token').val()) // +'&pagenum='+pnum
          .then(function(result) {
            var respdata = eval(result.data);
            if(respdata.status == 0){
                  $scope.userlist = eval(respdata.data.userlist);
            } else if(respdata.status > 0){
                alert(respdata.msg);
            }
          }, function errorCallback(response) {
              //console.log(response);
          });
    };

  $scope.getUserList();

  $scope.onAction = function(data,id){
    alert(data.id);
    alert(id);
  };

}).controller("AdminExportAppCtrl", function($scope,$http){
/**NO CONFLICT**/
$.fn.bootstrapBtn = $.fn.button.noConflict();
//var dates = 0;
$scope.totacount = 0;

$scope.getDatas = function(chid,dates,token){
  $http.get('/getdatas?dates=' + dates+'&chid='+chid+'&token='+token) // +'&pagenum='+pnum
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

$scope.getDatas($('#report-status').val(),$('.getbydatetime').val(),$('#token').val());


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
  var option = $(this).find('option:selected').attr('data-eml');
  console.log(option);
  $scope.getDatas(this.value,$('.getbydatetime').val(),$('#token').val());
  $('#downld').attr("href","/download?chid="+this.value
    + "&dates="+ $('.getbydatetime').val()
    +"&token="+ $('#token').val());
});

$(document).on('click','#sendmail',function(){

    $http.get('/mailer?chid='+$('#report-status').val()+ '&token='+$('#token').val()+'&dates='+$('.getbydatetime').val()+'&email='+ $('#report-status').find('option:selected').attr('data-eml')) // +'&pagenum='+pnum
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
  $scope.getDatas($('#report-status').val(),e.currentTarget.value,$('#token').val());
  $('#downld').attr("href","/download?chid="+$('#report-status').val()
    +"&dates="+$('.getbydatetime').val()
    +"&token="+ $('#token').val());
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