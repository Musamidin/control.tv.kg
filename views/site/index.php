<?php

//use dosamigos\fileupload\FileUploadUI;
// with UI

use dosamigos\fileupload\FileUpload;
// without UI

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index" ng-controller="AppCtrl">
<div class="row">
    <div class="col-md-2">
    <?= FileUpload::widget([
        'model' => $model,
        'attribute' => 'userfile',
        'url' => ['/result'], // your url, this is just for demo purposes,
        'options' => ['accept' => '*'],

        'clientOptions' => [
            'maxFileSize' => 2000000
        ],
        // Also, you can specify jQuery-File-Upload events
        // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
        'clientEvents' => [
            'fileuploaddone' => 'function(e, data) {
                                    //console.log(e);
                                    //console.log(data);
                            var obj = JSON.parse(data.result);
                        if(data.textStatus == "success"){
                            $("#upcount").html(obj.files[0].count);
                            alert("Файл успешно загружен!");
                        }else{
                            alert("Файл не загружен, обратитесь к администратору софта!");
                        }    
                            
                                }',
            'fileuploadfail' => 'function(e, data) {
                                    console.log(e);
                                    console.log(data);
                                }',
        ],
    ]); 

    ?>
    </div>
    <div class="col-md-10">
    <span id="status">Количество не обработанных записей: <span id="upcount"><?=$upcount ?></span> <a href="javascript:void(0)" id="ts">посмотреть загруженные записи</a></span>
    </div>
</div>
<div class="row">
    <div class="col-md-12"><a href="" class="btn btn-app"><i class="fa fa-plus"></i>Добавить</a><a href="" class="btn btn-app"><i class="fa fa-file-excel-o"></i>Импорт Excel</a></div>    
</div>

</div>