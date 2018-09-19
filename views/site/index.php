<?php

//use dosamigos\fileupload\FileUploadUI;
// with UI
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use dosamigos\fileupload\FileUpload;
// without UI

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index" ng-controller="AppCtrl">
<div class="row">
    <div class="col-md-2">sss
    </div>
    <div class="col-md-10">
    <span id="status">Количество не обработанных записей: <span id="upcount">{{totalmainlist}}</span> <a href="javascript:void(0)" id="ts">посмотреть загруженные записи</a></span>
    </div>
</div>
<div class="row">
    <div class="col-md-12"><a ng-click="addform()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-plus"></i>Добавить</a>
    <a ng-click="importbtn()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-file-excel-o"></i>Импорт Excel</a>
     <a ng-click="importbtn()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-plug"></i>API Keys</a>
    </div>    
</div>


    <br/>
    <div class="row">
        <div class="col-md-12" ng-if="mainlistview.length > 0">
        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th class="sorting" aria-label="checkbox"><input type="checkbox"/></th>
                    <th class="sorting" aria-label="ID">ID</th>
                    <th class="sorting" aria-label="Дата">Дата</th>
                    <th class="sorting" aria-label="Моб. номер">Моб. номер</th>
                    <th class="sorting" aria-label="Телеканал">Телеканал</th>
                    <th class="sorting" aria-label="Текст">Текст</th>
                    <th class="sorting" aria-label="Дата проката">Дата проката</th>
                    <th class="sorting" aria-label="Статус">Статус</th>
                    <th class="sorting" aria-label="Описание">Описание</th>
                    <th class="sorting" aria-label="Действие">Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row" class="odd" dir-paginate="ml in mainlistview | itemsPerPage: mainlistPerPage" total-items="totalmainlist" current-page="pagination.current" pagination-id="cust">
                  <td><input type="checkbox"/></td>
                  <td>{{ml.mhid}}</td>
                  <td>{{ml.datetime}}</td>
                  <td>{{ml.phone}}</td>
                  <td>{{ml.chname}}</td>
                  <td>{{ml.text}}</td>
                  <td>{{ml.daterent}}</td>
                  <td>{{ml.status}}</td>
                  <td>{{ml.description}}</td>
                  <td>
                  <div class="btn-group">
                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                      Действие <span class="caret"></span>
                   </button>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:void(0)" ng-click="onAccept(ml.id)"><span class="fa fa-check"></span>&nbsp;Принять</a></li>
                      <li class="divider"></li>
                      <li><a href="javascript:void(0)" ng-click="onReject(ml.id)"><span class="fa fa-close"></span>&nbsp;Отвергнуть</a></li>
                      
                    </ul>
               </div>
               </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                <th rowspan="1" colspan="1"><input type="checkbox"/></th>
                <th rowspan="1" colspan="1">ID</th>
                <th rowspan="1" colspan="1">Дата</th>
                <th rowspan="1" colspan="1">Моб. номер</th>
                <th rowspan="1" colspan="1">Телеканал</th>
                <th rowspan="1" colspan="1">Текст</th>
                <th rowspan="1" colspan="1">Дата проката</th>
                <th rowspan="1" colspan="1">Статус</th>
                <th rowspan="1" colspan="1">Описание</th>
                <th rowspan="1" colspan="1">Действие</th>
                </tfoot>
              </table>
              <dir-pagination-controls pagination-id="cust" on-page-change="pageChanged(newPageNumber)">
    </dir-pagination-controls>
        </div>
    </div>




<div class="modal modal-info fade in" id="modal-info-add-import">
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
                                                $("#load-ing").hide();
                                                $("#upcount").html(obj.files[0].count);
                                                $("#status-response").html("Файл успешно загружен!");
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
                    <div class="col-md-6">
                        <span id="status-response"></span>
                        <span id="load-ing">
                            <img id="loader" src="/img/ezgif.com-crop.gif"/>
                        </span>
                    </div>
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

<!--Modal window Add data by form-->
<div class="modal modal-info fade in" id="modal-info-add-form">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Добавить запись</h4>
              </div>
              <div class="modal-body">
                <?php $form = ActiveForm::begin([
                                      'id' => 'addForm',
                                      'options' => ['name' => 'addForm']
                                    ]); ?>
                  <input type="hidden" name="token" value="<?=md5(Yii::$app->session->getId().'opn'); ?>" id="token"/>                  
                  <div class="row">
                      <div class="col-md-4">
                          <?= $form->field($mainhub, 'phone',['options'=>
                            ['tag' => 'div','class'=> 'form-group field-mainhub-phone has-feedback required'],
                            'template'=>'{input}<span class="fa fa-phone form-control-feedback"></span>{error}{hint}'
                  ])->textInput(['autofocus' => false,'placeholder'=>'XXXXXXXXX','ng-model'=>'data.phone'])->label(false); ?>
                      </div>
                      <div class="col-md-4">
                          <?= $form->field($mainhub, 'chid',['options'=>
                            ['tag' => 'div','class'=> 'form-group field-mainhub-chid has-feedback required'],
                            'template'=>'{input}<span class="form-control-feedback"></span>{error}{hint}'
                            ])->dropDownList([],
                            ['prompt' => 'Телеканалы...',
                            'ng-model' => 'data.chid',
                            'ng-options'=> 'tvls.id as tvls.channel_name for tvls in tvlist track by tvls.id'
                            ])->label(false); ?>
                      </div>
                      <div class="col-md-4">
                        <?=$form->field($mainhub, 'dates',['options'=>
                            ['tag' => 'div','class'=> 'form-group field-mainhub-dates has-feedback required'],
                            'template'=>'{input}<span class="glyphicon glyphicon-calendar form-control-feedback"></span>{error}{hint}'
                            ])->textInput(['autofocus' => false,'placeholder'=>'дд/мм/гггг','title'=>'дд/мм/гггг','ng-model'=>'data.dates'])->label(false);
                        ?>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          <?= $form->field($mainhub, 'text')->textarea(['autofocus' => false,'placeholder'=>'Текст объявления','ng-model'=>'data.text'])->label('Текст объявления'); ?>
                      </div>
                  </div>
                <?php ActiveForm::end(); ?>  
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Отменить</button>
                <button type="button" class="btn btn-outline" ng-click="addformaction()">Добавить</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->

</div>


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