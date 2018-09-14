<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index" ng-controller="myCtrl">
    <!--div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?= \Yii::$app->basePath."\data\\"; ?>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <div class="text"><h1> Размещение бегущей строки на телевизионных каналах Кыргызстана</h1></div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="file" name="userfile" id="xlsx"/>
                <button ng-click="upFile()">Upload File</button>

            </div>
    </div-->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php
                $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id'=>'post-form','enableAjaxValidation' => FALSE]]);
            ?>
            <div>
                <?= $form->field($model,'userfile')->fileInput(['id'=>'xlsx']); ?>
            </div>
            <div>
                <a href="" onclick="return uploadImage();"><b class="photo">Upload Photo</b></a>

                <button id='post-submit-btn' onclick='javascript:send();' class='post_submit'></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>    
    </div>
</div>
<script>
// this script executes when click on upload images link
    function uploadImage() {
        $("#xlsx").click();
        return false;
}
</script>

<script type="text/javascript">
// this script for collecting the form data and pass to the controller action and doing the on success validations
function send(){
    var formData = new FormData($("#post-form")[0]);
    $.ajax({
        url: '/result',
        type: 'POST',
        data: formData,
        datatype:'json',
        // async: false,
        beforeSend: function() {
            // do some loading options
        },
        success: function (data) {
            // on success do some validation or refresh the content div to display the uploaded images 
            //jQuery("#list-of-post").load("<?php  ?>");
        },

        complete: function() {
            // success alerts
        },

        error: function (data) {
            alert("There may a error on uploading. Try again later");
        },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
}
</script>
