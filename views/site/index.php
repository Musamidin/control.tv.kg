<?php

//use dosamigos\fileupload\FileUploadUI;
// with UI

use dosamigos\fileupload\FileUpload;
// without UI

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index" ng-controller="AppCtrl">
<div class="row">
    <div class="col-md-2">sss
    </div>
    <div class="col-md-10">
    <span id="status">Количество не обработанных записей: <span id="upcount"><?=$upcount ?></span> <a href="javascript:void(0)" id="ts">посмотреть загруженные записи</a></span>
    </div>
</div>
<div class="row">
    <div class="col-md-12"><a ng-click="addform()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-plus"></i>Добавить</a>
    <a ng-click="importbtn()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-file-excel-o"></i>Импорт Excel</a></div>    
</div>

<div class="modal modal-info fade in" id="modal-info-add-form">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Импорт с EXCEL файла</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                <?= FileUpload::widget([
                    'model' => $model,
                    'attribute' => 'userfile',
                    'url' => ['/result'], // your url, this is just for demo purposes,
                    'options' => ['accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel'],

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
                    <div class="col-md-6"><img id="loader" src="/img/ezgif.com-crop.gif"/></div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <span class="">Шаблон для загрузки файла</span>
                        <ul id="mlist">
                            <li><span class="required">Все поля обязательны для заполнения</span></li>
                            <li>Поле phone заполняется в формате: 772030317</li>
                            <li>Поле chid - это ID телеканала
                                <ul>
                                    <li>1 - 5 канал</li>
                                    <li>2 - Пирамида</li>
                                    <li>3 - ЭЛТР</li>
                                    <li>4 - Нарын ТВ</li>
                                    <li>5 - СТВ</li>
                                </ul>
                            </li>
                            <li>Поле text - текс объявления</li>
                            <li>dates - это поле даты проката в формате <span class="codex">dd/mm/YYYY</span> 
                                <ul>
                                    <li>Даты разделяются с запятыми(<span class="sim"> , </span>) к примеру: <span class="codex">dd/mm/YYYY<span class="sim">,</span>dd/mm/YYYY<span class="sim">,</span>dd/mm/YYYY</span></li>
                                    <li>Диапазон даты разделяется со знаком тире (<span class="sim"> - </span>) к примеру: <span class="codex">dd/mm/YYYY <span class="sim">-</span> dd/mm/YYYY</span></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <img id="exml" src="/img/Inport-examples.png"/>
                    </div>
                </div>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>

<style type="text/css">
.modal-info .modal-header, .modal-info .modal-footer {
    background-color: #00a7d085 !important;
}
.bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body{
    background-color: #00a7d0 !important;
}
</style>

</div>