<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-cloak ng-app="myApp">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--title>SMS - Рассылка</title-->
    <meta name="Authors" content="Sergey Fedotov, Musa Ahmedov">
    <meta name="description" content="SMS - Рассылка">
    <meta name="keywords" content="sms, смс рассылка, смс, рассылка, отправить смс, массовая смс рассылка, смс уведомления, смс шлюз">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style id="core_style">@media (min-height:483px) and (max-height:484px) { html body { min-height: 483px; } }</style>
</head>
<body>
<?php $this->beginBody() ?>
<header>
    <div class="container">
        
    </div>
</header>
<div class="wrap">
    <div class="container">
        <?= Alert::widget() ?>
        <div class="navBar">
            <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name, //
                'brandUrl' => '/',
                'options' => [
                    'class' => 'navbar navbar-inverse',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Главная', 'url' => ['/']],
                    //['label' => 'Отчёт', 'url' => ['/report']],
                    ['label' => 'Настройки', 'url' => ['/useraccount']],
                    Yii::$app->user->isGuest ? (
                        ['label' => 'Login', Url::to(['/login'])]
                    ) : (
                        '<li>'
                        . Html::beginForm(['/logout'], 'post')
                        . Html::submitButton(
                            'Выход (' . Yii::$app->user->identity->phoneAsLogin . ')',
                            ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>'
                    )
                ],
            ]);
            NavBar::end();
            ?>
        </div>
        <div class="col-md-1 sidebar">
            <a href="/phonebook">
                <div class="mbox">
                    <span class="glyphicon glyphicon-book"></span>
                    <span>Телефонная книга</span>
                </div>
            </a>
            <a href="/adddispatch">
                <div class="mbox">
                    <span class="glyphicon glyphicon-fullscreen"></span>
                    <span>Новая рассылка</span>
                </div>
            </a>
            <a href="/dispatchlist">
                <div class="mbox">
                    <span class="glyphicon glyphicon-list"></span>
                    <span>Список рассылок</span>
                </div>
            </a>
            <a href="/statistic">
                <div class="mbox">
                    <span class="glyphicon glyphicon-stats"></span>
                    <span>Статистика отправленных сообщений</span>
                </div>
            </a>
            <a href="/sendername">
                <div class="mbox">
                    <span class="glyphicon glyphicon-tag"></span>
                    <span>Запросить имя отправителя</span>
                </div>
            </a>
            <a href="/settings">
                <div class="mbox">
                    <span class="glyphicon glyphicon-cog"></span>
                    <span>Настройки</span>
                </div>
            </a>           
        </div>
        <div class="col-md-11 main" style="min-height:500px;">
            <?= $content ?>
        </div>
        
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; ОсОО «Перспективные решения» <?= date('Y') ?><!--?= $this->render('language')?--></p>

        <!--p class="pull-right"><?= Yii::powered() ?></p-->
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
