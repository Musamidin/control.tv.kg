<?php

//use dosamigos\fileupload\FileUploadUI;
// with UI

use dosamigos\fileupload\FileUpload;
// without UI

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>

<div class="row">
    <div class="col-md-2">
    <?= FileUpload::widget([
        'model' => $model,
        'attribute' => 'userfile',
        'url' => ['/result'], // your url, this is just for demo purposes,
        'options' => ['accept' => '*'],
        'clientOptions' => [
            'maxFileSize' => 2000000
        ],
        // Also, you can specify jQuery-File-Upload events
        // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
        'clientEvents' => [
            'fileuploaddone' => 'function(e, data) {
                                    console.log(e);
                                    console.log(data);
                                }',
            'fileuploadfail' => 'function(e, data) {
                                    console.log(e);
                                    console.log(data);
                                }',
        ],
    ]); 

    ?>
    </div>
    <div class="col-md-10">
        ghfghfghfg
    </div>
</div>