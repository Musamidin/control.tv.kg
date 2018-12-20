<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AdminAsset;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-cloak ng-app="AdminApp">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $search = '<form class="navbar-form navbar-nav text-center nav-srch">
            <div class="srch form-group has-success has-feedback">
              <input type="text" placeholder="Поиск заявки по тексту, номеру" id="searcher" class="min-w input-sm form-control">
              <span class="glyphicon glyphicon-search form-control-feedback"></span>
            </div>
      </form>';

    NavBar::begin([
        'brandLabel' => 'Online TV Маркет', //Yii::$app->name
        'brandUrl' => '/',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Главная', 'url' => ['/admin']],
            ['label' => 'Настройки', 'url' => ['/useraccount']],
            ['label' => 'Отчёт', 'url' => ['/report']],
            ['label' => 'Выгрузка', 'url' => ['/export']],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', Url::to(['/login'])]
            ) : (
                '<li>'
                . Html::beginForm(['/logout'], 'post')
                . Html::submitButton(
                    'Выход (' . Yii::$app->user->identity->login . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    echo $search;

    NavBar::end();
    ?>
    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; ОсОО «Медиа маркет групп» <?= date('Y') ?><!--?= $this->render('language')?--></p>

        <!--p class="pull-right"><?= Yii::powered() ?></p-->
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
