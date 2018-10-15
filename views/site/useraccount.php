<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$tvlist = Yii::$app->HelperFunc->getTvlist();

$this->title = 'Изменить пароль';
?>
<div class="site-signup">
<? if(Yii::$app->user->identity->role == 1): ?>
    <div class="row sett-box">
      <div class="col-lg-12 pswd-box">
        <div class="col-lg-6">
            <h1>Добавить пользователя</h1>
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
