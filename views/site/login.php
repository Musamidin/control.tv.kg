<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="login-box">
        <div class="login-logo">
            <a href="../../"></a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Авторизация</p>
           <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'login',['options'=>
                                                    ['tag' => 'div','class'=> 'form-group field-loginform-username has-feedback required'],
                                                    'template'=>'{input}<span class="glyphicon glyphicon-user form-control-feedback"></span>{error}{hint}'
                                                    ])->textInput(['autofocus' => true,'placeholder'=>'Номер телефона'])->label('Номер'); ?>
            <?= $form->field($model, 'password',['options'=>
                                                    ['tag' => 'div','class'=> 'form-group field-loginform-username has-feedback required'],
                                                    'template'=>'{input}<span class="glyphicon glyphicon-lock form-control-feedback"></span>{error}{hint}'
                                                    ])->passwordInput(['placeholder'=>'Пароль'])->label('Пароль'); ?>
            <br/>
            <div class="row">
                <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe',['options'=>
                                                    ['tag' => 'div','class'=> 'checkbox icheck']
                                                    ])->checkbox()->label('Запомнить пароль'); ?>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <?= Html::submitButton('Вход', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <br/>
            <div class="row">
                <div class="col-xs-12"><?=Html::a('Забыл пароль',['/grid-options'], ['class'=>'your_class']); ?></div>
                <!--div class="col-xs-6"></div-->
            </div>    
        </div>
    </div>
    
</div>
<!--script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script-->