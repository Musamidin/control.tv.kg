<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$tvlist = Yii::$app->HelperFunc->getTvlist();

$this->title = 'Изменить пароль';
?>
<div class="site-settings">
<input type="hidden" name="token" value="<?=md5(Yii::$app->session->getId().'opn'); ?>" id="token"/>

<div class="row sett-box" ng-controller="SettingsCtrl">

    <!-- Вкладки (навигация по панелям) -->
    <ul class="nav nav-tabs" id="myTabEvents">
        <li class="active"><a class="tabnav" data-toggle="tab" href="#evPanel1">Изменить пароль</a></li>
        
        <? if(Yii::$app->user->identity->role == 1): ?>
            <li class="tabnav"><a class="tabnav" data-toggle="tab" href="#evPanel2">Пользователи</a></li>
            <? endif; ?>

        <li><a class="tabnav" data-toggle="tab" href="#evPanel3">Документация API</a></li>
        <? if(Yii::$app->user->identity->role == 1): ?>
            <li><a class="tabnav" data-toggle="tab" href="#evPanel4">Нерабочие дни</a></li>
        <? endif; ?>
    </ul>
         
    <!-- Панели -->
    <div class="tab-content" id="myTabContent">
        <? if(Yii::$app->user->identity->role == 1): ?>
        <!-- Панель 2 -->
        <div id="evPanel2" class="tab-pane fade">
            <!-- Содержимое панели 2 -->
            <div class="col-lg-12 tab-box">
                <div class="col-lg-12">
                    <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th>Логин</th>
                                <th>Имя</th>
                                <th>Права</th>
                                <th>Статус</th>
                                <th>API Key</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="ul in userlist">
                                <td>{{ul.login}}</td>
                                <td>{{ul.name}}</td>
                                <td>
                                    <div ng-switch="ul.role">
                                        <span ng-switch-when="0" class="label label-info">Агент</span>
                                        <span ng-switch-when="1" class="label label-primary ">Администратор</span>
                                        <span ng-switch-when="2" class="label label-warning">Менеджер</span>
                                    </div>
                                </td>
                                <td>
                                    <div ng-switch="ul.status">
                                        <span ng-switch-when="0" class="label label-success bg-purple">Работает</span>
                                        <span ng-switch-when="1" class="label label-danger">Отключен</span>
                                    </div>
                                </td>
                                <td>{{ul.access_token}}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                          Действие <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                          <li><a href="javascript:void(0)" ng-click="onAction(ul,1)"><span class="fa fa-edit"></span>&nbsp;Редактировать</a></li>
                                          <li class="divider"></li>
                                          <li><a href="javascript:void(0)" ng-click="onAction(ul,2)"><span class="fa fa-close"></span>&nbsp;Отключить</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <? endif; ?>
        <!-- Панель 1 -->
        <div id="evPanel1" class="tab-pane fade in active">
              <!-- Содержимое панели 1 -->
            <div class="col-lg-12 tab-box">
                <div class="col-lg-6">
                    <?php $form = ActiveForm::begin(['id' => 'form-change']); ?>
                        <?= $form->field($model, 'oldPassword')->passwordInput()->label('Старый пароль') ?>
                        <?= $form->field($model, 'newPassword')->passwordInput()->label('Новый пароль') ?>
                        <?= $form->field($model, 'retypePassword')->passwordInput()->label('Повторить новый пароль') ?>
                        <div class="form-group">
                            <?= Html::submitButton('Изменить пароль', ['class' => 'btn btn-primary', 'name' => 'change-button']) ?>
                        </div>
                        <div><?=isset($status) ? $status : ''; ?></div>
                        <br/>
                        <div class="api-box"><b>Ваш API Key:</b>&nbsp;&nbsp;<?=isset($accesstoken) ? $accesstoken : ''; ?></div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <!-- Панель 3 -->
        <div id="evPanel3" class="tab-pane fade">
          <!-- Содержимое панели 3 -->
            <div class="col-lg-12 wiki-box">
                <h1>Документация API</h1>
                <div class="req-box">
                    <h4 class="header-tit">Запрос на добавления заявки</h4>
                    <h4> * Загаловок запроза (Header request)</h4>
                    Значение ключа <code>Authorization</code> после <code>Bear</code> вводите свой API ключ (API key)
                    <img class="imgsize" src="/img/header-req.png"/>
                    <h4> * Тело запроса (BODY request)</h4>
                    В теле запроса принимается 4 параметра:
                    <ul>
                        <li><code>phone</code> в 9 значном формате</li>
                        <li><code>text</code> текст объявления</li>
                        <li><code>dates</code> Даты выхода бегушки фомат даты <code>дд/мм/гггг</code>
                            <ul>
                                <li>Массив даты разделяются знаком <code>,</code> на пример: <code>дд/мм/гггг,дд/мм/гггг,дд/мм/гггг</code></li>
                                <li>Диапазон даты с по через знак тире <code>-</code>на пример: <code>дд/мм/гггг - дд/мм/гггг</code></li>
                            </ul>
                        </li>
                        <li><code>chid</code> - это номер телеканала <code>INT</code> значение, список ТК:
                            <ul>
                            <? foreach($tvlist['tvlist'] as $tl): ?>
                                <li><code><?=$tl['id'];?></code> - <?=$tl['channel_name'];?></li>
                            <? endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                    <img class="imgsize" src="/img/body-req.png"/>
                    <h4 class="header-tit">Ответ на запрос добавления заявки</h4>
                    Ответ запроса в формате JSON
                    <code>{
                    "status" : 0,
                    "message" : "OK",
                    "id" : 1
                    }</code>
                    <br/>
                    <code>status</code> - код ответа от сервра:
                    <ul>
                        <li>0 - Успешно добавлен!</li>
                        <li>1 - Ошибка на сотороне сервера!</li>
                    </ul>
                    <code>message</code> статус сообщения
                    <br/>
                    <code>id</code> записи (заявки)
                    <h4 class="header-tit">Запрос на получения статус заявки</h4>
                </div>
            </div>
        </div>
        <? if(Yii::$app->user->identity->role == 1): ?>
        <!-- Панель 1 -->
        <div id="evPanel4" class="tab-pane fade">
            <!-- Содержимое панели 1 -->
            <div class="col-lg-12 tab-box">
                <div class="row add-date-box">
                    <div class="col-md-6">
                        <a id="getbydatetime" href="javascript:void(0)">
                            <i class="glyphicon glyphicon-calendar getbydatetime"></i>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <div id="list-dates"></div>
                        <input type="hidden" name="disableddays" id="disableddays">
                        <button ng-click="setSave()" id="hd-btn" class="btn btn-primary">Добаить</button>
                    </div>
                </div>
                <br/>
                <div class="row list-disabled-box">
                    <div class="col-md-12" ng-if="hdlist.length > 0">
                        <table class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                <tr role="row">
                                    <th class="sorting" aria-label="№">№</th>
                                    <th class="sorting" aria-label="Дата">Дата</th>
                                    <th class="sorting" aria-label="Телеканал">Нерабочие дни</th>
                                    <th class="sorting" aria-label="Удалить">Удалить</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr role="row" class="odd" dir-paginate="hd in hdlist | itemsPerPage: mainlistPerPage" total-items="totalmainlist" current-page="pagination.current" pagination-id="cust">
                                  <td>{{hd.id}}</td>
                                  <td>{{hd.datetime | formatDatetime}}</td>
                                  <td>{{hd.days}}</td>
                                  <td>
                                    <a ng-click="deletebtn(hd)" class="rem-btn" href="javascript:void(0)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                  </td>
                                </tr>
                                </tbody>
                      </table>
                      <dir-pagination-controls pagination-id="cust" on-page-change="pageChanged(newPageNumber)">
                      </dir-pagination-controls>
                    </div>
                </div>
            </div>
        </div>
        <? endif; ?>
    </div>

</div>
