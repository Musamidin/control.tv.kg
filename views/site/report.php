<?php

//use dosamigos\fileupload\FileUploadUI;
// with UI
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use dosamigos\fileupload\FileUpload;
// without UI

$tvlist = Yii::$app->HelperFunc->getTvlist();
$clients = Yii::$app->HelperFunc->getClients();

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-report" ng-controller="AppReportCtrl">
<div class="row">
    <div class="col-md-12"></div>
</div>
<br/>
  <div class="row">
      <div class="col-md-9 paddTop8">
          <input type="hidden" name="token" value="<?=md5(Yii::$app->session->getId().'opn'); ?>" id="token"/>
          <div class="input-group">
          <select id="sortbycli" name="sortbycli" class="form-control">
              <option value="0">Агенты</option>
              <? foreach($clients as $cli): ?>
                  <option value="<?=$cli['id']; ?>"><?=$cli['name']; ?></option>
              <? endforeach; ?>
          </select>
          <span class="input-group-addon input-sm"></span>
          <select id="sortbytv" name="sortbytv" class="form-control">
              <option value="0">Телеканалы...</option>
              <? foreach($tvlist['tvlist'] as $tl): ?>
                  <option value="<?=$tl['id']; ?>"><?=$tl['channel_name']; ?></option>
              <? endforeach; ?>
          </select>
              <span class="input-group-addon input-sm"></span>
              <input type="text" class="form-control getbydatetime">
              <span class="input-group-addon rep-dpicker">
              <i class="glyphicon glyphicon-calendar"></i>
              </span>
          </div>
      </div>
      <div class="col-md-3 sum-box text-center">
          <div class="row">
          <div class="col-md-12">Количество: <span class="summ">{{totalmainlist}}</span></div>
          </div>
          <div class="row">
          <div class="col-md-12">Кол. сим.: <span class="summ">{{total}}</span></div>
          </div>
      </div>
  </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <!--a id="expt-excel" class="export-excel" href="/exptexcel?token=<?=md5(Yii::$app->session->getId().'opn'); ?>&daterange=<?=date('Y-m-d')?> / <?=date('Y-m-d')?>&bytv=0&sts=0">
                <i class="fa fa-file-excel-o"></i>
            </a-->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-box" ng-if="mainlistview.length > 0">
        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th class="sorting" aria-label="№">№</th>
                    <th class="sorting" aria-label="Агент">Агент</th>
                    <th class="sorting" aria-label="Дата">Дата</th>
                    <th class="sorting" aria-label="Дата проката">Дата проката</th>
                    <th class="sorting" aria-label="Телеканал">Телеканал</th>
                    <th class="sorting" aria-label="Текст">Текст</th>
                    <th class="sorting" aria-label="Текст">Кол.Сим.</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row" class="odd" dir-paginate="ml in mainlistview | itemsPerPage: mainlistPerPage" total-items="totalmainlist" current-page="pagination.current" pagination-id="cust">
                  <td>{{ml.id}}</td>
                  <td>{{ml.client}}</td>
                  <td>{{ml.datetime | formatDatetime}}</td>
                  <td>{{ml.daterent}}</td>
                  <td>{{ml.tvname}}</td>
                  <td class="daterent">{{ml.txt}}</td>
                  <td>{{ml.countSim}}</td>
                </tr>
                </tbody>
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
                ss
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


  </div>

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