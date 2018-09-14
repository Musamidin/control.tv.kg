// var myApp = angular.module('myApp', []);

//      myApp.directive('fileModel', ['$parse', function ($parse) {
//         return {
//            restrict: 'A',
//            link: function(scope, element, attrs) {
//               var model = $parse(attrs.fileModel);
//               var modelSetter = model.assign;
//               element.bind('change', function(){
//                  scope.$apply(function(){
//                     modelSetter(scope, element[0].files[0]);
//                  });
//               });
//            }
//         };
//      }]);
//      myApp.service('fileUpload', ['$http', function ($http) {
//         this.uploadFileToUrl = function(file, uploadUrl){
//            var fd = new FormData();
//            fd.append('file', file);
//            $http.post(uploadUrl, fd, {
//               transformRequest: angular.identity,
//               headers: {'Content-Type': undefined}
//            });
//            // .success(function(){
           
//            // }).error(function(){
//            //  console.log('error');
//            // });
//         }
//      }]);
//      myApp.controller('myCtrl', ['$scope', 'fileUpload', function($scope, fileUpload){
//         $scope.uploadFile = function(){
//            var file = $scope.myFile;
//            var uploadUrl = "/result";
//            fileUpload.uploadFileToUrl(file, uploadUrl);
//            console.log(file);
//         };
//      }]);



var app = angular.module("myApp", ['angularFileUpload']);

app.controller('myCtrl', ['$scope', 'FileUploader', function($scope, FileUploader) {


  $scope.upFile = function(){
      var formData = new FormData();
      formData.append('Content-type','multipart/form-data');
      formData.append('userfile', $('#xlsx').prop("files")[0]);

        $.ajax('/result', {
            method: 'POST',
            processData: false,
            contentType: false,
            data: formData
        }).done(function(data){
          var  obj = JSON.parse(data);
            if (obj.status == 0){
                console.log(obj);
            }
            if (obj.status == 1){
              alert(obj.message);
            }

        }).fail(function(data){
            //console.log(data);
            //alert("Error while uploading the files");
        });
  };




}]);