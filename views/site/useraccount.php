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
<? if(Yii::$app->user->identity->role == 1): ?>
    <div class="row sett-box" ng-controller="SettingsCtrl">
      <div class="col-lg-12 pswd-box">
        <div class="col-lg-12">
            <h1>Пользователи</h1>
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
    <br/>
    <div class="row sett-box">
      <div class="col-lg-12 pswd-box">
        <div class="col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>
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
    <br/>
    <div class="row">
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
    </div>
