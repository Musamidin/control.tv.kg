<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Admin';

$tvlist = Yii::$app->HelperFunc->getTvlist();
$clients = Yii::$app->HelperFunc->getClients();
?>
<div class="site-index" ng-controller="AdminIndexAppCtrl">
  <br/>
  <div class="row">
      <div class="col-md-7 paddTop8">
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
          <select id="report-status" value="" name="reportstatus" class="form-control">
              <option value="-1">Все</option>
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
      <div class="col-md-5 sum-box text-center">
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
        <div class="col-md-12" ng-if="mainlistview.length > 0">
        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                <tr role="row">
                    <th class="sorting" aria-label="select-all-chbx"><input type="checkbox" class="select-all-chbx"/></th>
                    <th class="sorting" aria-label="ID">№</th>
                    <th class="sorting" aria-label="Дата">Дата</th>
                    <th class="sorting" aria-label="Заказчик">Заказчик</th>
                    <th class="sorting" aria-label="Канал">Канал</th>
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
                  <td>{{ml.order}}</td>
                  <td>{{ml.chname}}</td>
                  <td>{{ml.text}}</td>
                  <td>{{ml.dates}}</td>
                  <td>{{ml.cday}}</td>
                  <td>{{ml.simcount}}</td>
                  <td>{{ml.summ | fixedto}}</td>
                  <td>{{ml.description}}</td>
                  <td>
                    <div ng-switch="ml.status">
                        <span ng-switch-when="0" class="label label-info">В обработке</span>
                        <span ng-switch-when="1" class="label label-success">Принято</span>
                        <span ng-switch-when="2" class="label label-danger">Отвергнуто</span>
                    </div>
                  </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th rowspan="1" colspan="7">Итого:</th>
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

    <div class="lg-btn row">
      <div class="col-md-12 text-right">
          <div class="btn-group">
            <button class="btn btn-info btn-lg dropdown-toggle" type="button" data-toggle="dropdown">
              Действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="javascript:void(0)" ng-click="onAccept()"><span class="fa fa-check"></span>&nbsp;Принять</a></li>
              <li class="divider"></li>
              <li><a href="javascript:void(0)" ng-click="onReject()"><span class="fa fa-close"></span>&nbsp;Отвергнуть</a></li>
            </ul>
          </div>
      </div>
    </div>


<div class="modal modal-info fade in" id="modal-info">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Коментария к действию</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <textarea id="comment" class="form-control" rows="3" placeholder="Введите ..."></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-outline" ng-click="onAction($event)" data-id="0" id="actionBtn">Принять</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
</div>





</div>
