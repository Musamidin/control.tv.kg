<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Изменить пароль';
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
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
