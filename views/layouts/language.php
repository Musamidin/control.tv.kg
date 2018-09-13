<?php

use yii\bootstrap\Html;

if(Yii::$app->language == 'ru'){
	echo Html::a('КР', array_merge(Yii::$app->request->get(),[Yii::$app->controller->route, 'language'=>'kr']));
}else{
	echo Html::a('Ru', array_merge(Yii::$app->request->get(),[Yii::$app->controller->route, 'language'=>'ru']));
}