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
    <div class="col-md-12"></div>
</div>
<br/>
<div class="row">
    <div class="col-md-3 btn-box">
        <input type="hidden" name="token" value="<?=md5(Yii::$app->session->getId().'opn'); ?>" id="token"/>
        <a ng-click="addform()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-plus"></i>Добавить</a>
        <a ng-click="importbtn()" href="javascript:void(0)" class="btn btn-app"><i class="fa fa-file-excel-o"></i>Импорт Excel</a>       
    </div>
    <div class="col-md-5 filt-box paddTop8">
        <div class="input-group">
        <select id="sortbytv" name="sortbytv" class="form-control">
            <option value="0">Телеканалы...</option>
            <? foreach($tvlist['tvlist'] as $tl): ?>
                <option value="<?=$tl['id']; ?>"><?=$tl['channel_name']; ?></option>
            <? endforeach; ?>
        </select>
        <span class="input-group-addon input-sm"></span>
        <select id="report-status" value="" name="reportstatus" class="form-control">
            <option value="0">Не принятые</option>
            <option value="1">Принятые</option>
            <option value="2">Отвергнутые</option>
        </select>
            <span class="input-group-addon input-sm"></span>
            <input type="text" class="form-control getbydatetime">
            <span class="input-group-addon rep-dpicker">
            <i class="glyphicon glyphicon-calendar"></i>
            </span>
        </div>
    </div>
    <div class="col-md-4 sum-box text-center">
        <div class="row">
        <div class="col-md-6">Количество: <span class="summ">{{totalmainlist}}</span></div>
        <div class="col-md-6">Кол. дней: <span class="summ">{{total[0].allcd}}</span></div>
        </div>
        <div class="row">
        <div class="col-md-6">Кол. сим.: <span class="summ">{{total[0].allcs}}</span></div>
        <div class="col-md-6">Сумма: <span class="summ">{{total[0].allsumm | fixedto}}</span></div>
        </div>
    </div>
</div>
<br/>
    <div class="row">
        <div ng-if="mainlistview.length > 0" class="col-md-12">
            <a id="expt-excel" class="export-excel" href="/exptexcel?token=<?=md5(Yii::$app->session->getId().'opn'); ?>&daterange=<?=date('Y-m-d')?> / <?=date('Y-m-d')?>&bytv=0&sts=0">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-box" ng-if="mainlistview.length > 0">
        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th class="sorting" aria-label="checkbox">
                    <input type="checkbox" class="select_all"/>
                    </th>
                    <th class="sorting" aria-label="№">№</th>
                    <th class="sorting" aria-label="Дата">Дата</th>
                    <th class="sorting" aria-label="Телеканал">Телеканал</th>
                    <th class="sorting" aria-label="Текст">Текст</th>
                    <th class="sorting" aria-label="Дата проката">Дата проката</th>
                    <th class="sorting" aria-label="Кол. день">Кол. день</th>
                    <th class="sorting" aria-label="Кол. сим.">Кол. сим.</th>
                    <th class="sorting" aria-label="Сумма">Сумма</th>
                    <th class="sorting" aria-label="Описание">Описание</th>
                    <th class="sorting" aria-label="Статус">Статус</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row" class="odd" dir-paginate="ml in mainlistview | itemsPerPage: mainlistPerPage" total-items="totalmainlist" current-page="pagination.current" pagination-id="cust">
                  <td>
                  <input ng-if="ml.status == '0'" class="checkbox" type="checkbox" name="remove[]" ng-model="chdata" value="{{ml.id}}" />
                  </td>
                  <td>{{ml.id}}</td>
                  <td>{{ml.datetime | formatDatetime}}</td>
                  <td>{{ml.chname}}</td>
                  <td>{{ml.text}}</td>
                  <td class="daterent">{{ml.dates}}</td>
                  <td>{{ml.cday}}</td>
                  <td>{{ml.simcount}}</td>
                  <td>{{ml.summ | fixedto}}</td>
                  <td>{{ml.description}}</td>
                  <td>
                    <div ng-switch="ml.status">
                        <span ng-switch-when="0" class="label label-info">В обработке</span>
                        <div ng-switch-when="1" class="btn-group">
                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Принято <span class="caret"></span>
                        </button>
                          <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)" ng-click="onCallback(ml)"><span class="fa fa-calendar-times-o"></span>&nbsp;Отозвать</a></li>
                          </ul>
                        </div>
                        <span ng-switch-when="2" class="label label-danger">Отвергнуто</span>
                    </div>
                  </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th rowspan="1" colspan="6">Итого:</th>
                    <th rowspan="1" colspan="1">{{ mainlistview | tSumm: 'cday' }}</th>
                    <th rowspan="1" colspan="1">{{ mainlistview | tSumm: 'simcount' }}</th>
                    <th rowspan="1" colspan="1">{{ mainlistview | tSumms: 'summ' }}</th>
                    <th rowspan="1" colspan="2"></th>
                </tr>
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
                                                window.location.reload();
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
                            <!--li>Поле phone заполняется в формате: 772030317</li-->
                            <li>Поле chid - это ID телеканала
                                <ul ng-repeat="tvl in tvlist">
                                    <li>{{tvl.id}} - {{tvl.channel_name}}</li>
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
                <h4 class="modal-title">Добавить запись <span id="addstate"></span></h4>
              </div>
              <div class="modal-body">
                <?php $form = ActiveForm::begin([
                                      'id' => 'addForm',
                                      'options' => ['name' => 'addForm']
                                    ]); ?>
                  <div class="row">
                          <? /*= $form->field($mainhub, 'phone',['options'=>
                            ['tag' => 'div','class'=> 'form-group field-mainhub-phone has-feedback required'],
                            'template'=>'{input}<span class="fa fa-phone form-control-feedback"></span>{error}{hint}'
                  ])->textInput(['autofocus' => false,'placeholder'=>'XXXXXXXXX','ng-model'=>'data.phone'])->label(false); */ ?>
                      <div class="col-md-6">
                          <?= $form->field($mainhub, 'chid',['options'=>
                            ['tag' => 'div','class'=> 'form-group field-mainhub-chid has-feedback required'],
                            'template'=>'{input}<span class="form-control-feedback"></span>{error}{hint}'
                            ])->dropDownList([],
                            ['prompt' => 'Телеканалы...',
                            'ng-model' => 'data.chid',
                            'ng-options'=> 'tvls.id as tvls.channel_name for tvls in tvlist track by tvls.id'
                            ])->label(false); ?>
                      </div>
                      <div class="col-md-6">
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
<!--Modal window callBack-->
<div class="modal modal-info fade in" id="modal-callback">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Отмена заявки</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div id="mydatepicker"></div>
                    <input type="hidden" name="callbackdates" id="cbdates">
                  </div>
                  <div class="col-md-6">
                    <div class="col-md-12">
                      <div class="form-group">
                      <textarea id="comment" class="form-control" rows="3" placeholder="Введите комментарию ..."></textarea>
                    </div>
                    </div>
                    <div class="col-md-12">
                      <div id="view-dates"></div>
                    </div>
                    
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline" ng-click="onAction($event)" data-id="0" id="actionBtn">Принять</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>
<!--End Modal window callBack-->

<a style="display:none;" id="removebtn" ng-click="removedata()" class="btn btn-block btn-social btn-bitbucket">
    <i class="fa fa-bitbucket"></i>Удалить</a>
</div>
<style type="text/css">
#mydatepicker > .datepicker.datepicker-inline {
    border: 1px solid white;
}
.modal-info .modal-header, .modal-info .modal-footer {
    background-color: #00a7d085 !important;
}
.bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body{
    background-color: #1a95d0 !important;
}
</style>

</div>