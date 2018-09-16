<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Admin';
?>
<div class="site-index" ng-controller="AdminExportAppCtrl">
        <div class="row">
          <div class="col-md-2">
            <input type="hidden" id="token" name="token" value="<?=md5(Yii::$app->session->getId().'opn');?>"/>
          </div>
          <div class="col-md-5">
          <div class="input-group">
            <select id="report-status" value="" name="reportstatus" class="form-control">
                <option value="5 канал">5 канал</option>
                <option value="Пирамида">Пирамида</option>
                <option value="Нарын ТВ">Нарын ТВ</option>
                <option value="ЭЛТР">ЭЛТР</option>
              </select>
              <span class="input-group-addon input-sm"></span>
              <input type="text" class="form-control getbydatetime">
              <span class="input-group-addon rep-dpicker">
                <i class="glyphicon glyphicon-calendar"></i>
              </span>
        </div>
        </div>
        <div class="col-md-1 text-center">
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-6">
                Количество: <span>{{totacount}}</span>
            </div>
            <div class="col-md-6">
              <a id="downld" href="/download?id=5 канал" title="Скачать" class="icon-style">
                <span class="glyphicon glyphicon-save"></span>
              </a>
              <a id="sendmail" href="javascript:void(0)" title="Отправить на ТК" class="icon-style">
                <span class="glyphicon glyphicon-send"></span>
              </a>
            </div>    
          </div>
        </div>
        </div>
    <br/>
    <div class="row">
        <div class="col-md-12" ng-if="mainlistview.length > 0">
        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th class="sorting" aria-label="Текст">Текст</th>
                    <th class="sorting" aria-label="Дата проката">Дата проката</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row" class="odd" ng-repeat="ml in mainlistview">
                  <td>{{ml.text}}</td>
                  <td>{{ml.daterent}}</td>
                </tr>
                </tbody>
              </table>
        </div>
    </div>



<div class="modal modal-info fade in" id="modal-info">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Коментария к дествию</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <textarea class="form-control" rows="3" placeholder="Введите ..."></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-outline" id="actionBtn">Принять</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>





</div>
