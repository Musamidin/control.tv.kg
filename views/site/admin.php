<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Admin';
?>
<div class="site-index" ng-controller="AdminIndexAppCtrl">
        <div class="row">
          <div class="col-md-2">
            <input type="hidden" id="token" name="token" value="<?=md5(Yii::$app->session->getId().'opn');?>"/>
          </div>
          <div class="col-md-5">
          <div class="input-group">
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
        <div class="col-md-1 text-center">
        </div>
        <div class="col-md-4">
                Количество: <span>{{totalmainlist}}</span>
        </div>
        </div>
    <br/>
    <div class="row">
        <div class="col-md-12" ng-if="mainlistview.length > 0">
        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th class="sorting" aria-label="ID"><input type="checkbox"/></th>
                    <th class="sorting" aria-label="ID">ID</th>
                    <th class="sorting" aria-label="Заказчик">Заказчик</th>
                    <th class="sorting" aria-label="Текст">Текст</th>
                    <th class="sorting" aria-label="Дата проката">Дата проката</th>
                    <th class="sorting" aria-label="Действие">Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr role="row" class="odd" dir-paginate="ml in mainlistview | itemsPerPage: mainlistPerPage" total-items="totalmainlist" current-page="pagination.current" pagination-id="cust">
                  <td><input type="checkbox"/></td>
                  <td>{{ml.id}}</td>
                  <td>{{ml.client_id}}</td>
                  <td>{{ml.text}}</td>
                  <td>{{ml.dates}}</td>
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
                <th rowspan="1" colspan="1">Заказчик</th>
                <th rowspan="1" colspan="1">Текст</th>
                <th rowspan="1" colspan="1">Дата проката</th>
                <th rowspan="1" colspan="1">Действие</th>
                </tfoot>
              </table>
              <dir-pagination-controls pagination-id="cust" on-page-change="pageChanged(newPageNumber)">
    </dir-pagination-controls>
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
