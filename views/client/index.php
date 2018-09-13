<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index">
    <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="lng"><?= \Yii::$app->view->renderFile('@app/views/layouts/language.php') ?></div>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                <a href="/">
                    <img src="/img/logo.png" alt="" title="" class="logo">
                </a>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
                <div class="text"><h1> Размещение бегущей строки на телевизионных каналах Кыргызстана</h1></div>
                <div> <?php 
               echo Html::beginForm(['/client/logout'], 'post')
                . Html::submitButton(
                    'Выход (' . Yii::$app->user->identity->login . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm();

                ?></div><br/>
                <?=Url::to(['client/login']); ?>
            </div>
            
</div>
</div>
