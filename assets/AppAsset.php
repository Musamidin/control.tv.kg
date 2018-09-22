<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/bootstrap.min.css',
        'css/AdminLTE.min.css',
        'css/plugins/iCheck/flat/blue.css',
        '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css',
        'css/bootstrap-datepicker.min.css',
        'css/select2.min.css',
    ];
    public $js = [
        'js/angular.min1.6.4.js',
        'js/dirPagination.js',
        'js/app.js',
        'js/angular-file-upload.js',
        'js/bootstrap-datepicker.min.js',
        'js/bootstrap-datepicker.ru.min.js',
        'js/select2.min.js',
        'js/jquery.mask.min.js',
        'js/jquery/jquery-ui.min.js',
        'js/jquery.table2excel.min.js',
        'js/moment.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
