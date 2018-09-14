<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index" ng-controller="myCtrl">

	<!--input type = "file" file-model = "myFile"/-->
    <button id="clcl">upload me</button>
    <br/>
    <form action="/result" method="POST">
    <input type="text" name="test" value="musa">
    	<button>upload me</button>
    </form>

</div>
