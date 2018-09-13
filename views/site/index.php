<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index">
    <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?= \Yii::$app->basePath."\data\\"; ?>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <div class="text"><h1> Размещение бегущей строки на телевизионных каналах Кыргызстана</h1></div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php $form = ActiveForm::begin(['action'=>['/result'],'options' => ['enctype' => 'multipart/form-data']]) ?>

                    <?= $form->field($model, 'fileref',['options'=>
                ['tag' => 'div','class'=> 'form-group field-mainform-fileref has-feedback required'],
                'template'=>'{input}</span>{error}{hint}'
                ])->fileInput(); ?>

                    <button type="submit" class="btn btn-primary">Сохранить прикрепленный файл</button>

                <?php ActiveForm::end() ?>
            </div>
            
            
</div>
</div>
