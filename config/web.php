<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'language' => 'ru',
    'sourceLanguage'=> 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'fileStorage' => [
            'class' => 'yii2tech\filestorage\local\Storage',
            'basePath' => '@webroot/files',
            'baseUrl' => '@web/files',
            'dirPermission' => 0775,
            'filePermission' => 0755,
            'buckets' => [
                'tempFiles' => [
                    //'baseSubPath' => 'temp',
                    //'fileSubDirTemplate' => '{^name}/{^^name}',
                ],
            ]
        ],
        'HelperFunc' => ['class'=>'app\components\HelperFunc'],
        'Modules' => ['class'=>'app\components\Modules'],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Sf7LapkC_wdc9me7F4TH74Hyf1XfTQV_',
            'baseUrl' => '/',
            'parsers' => ['application/json' => 'yii\web\JsonParser'],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'loginUrl'=>['/login'],
            'identityClass' => 'app\models\UserIdentity',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',
                'username' => 'sales@myservice.kg',
                'password' => 'sales@open',
                'port' => '465',
                'encryption' => 'ssl',
            ],    
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget', //в файл
                    'categories' => ['writelog'], //категория логов
                    'logFile' => '@runtime/logs/warning.log', //куда сохранять
                    'logVars' => [] //не добавлять в лог глобальные переменные ($_SERVER, $_SESSION...)
                ],
                [
                    'class' => 'yii\log\EmailTarget', //шлет на e-mail
                    'categories' => ['sendlog'],
                    'mailer' => 'mailer',
                    //'logVars' => [],
                    'message' => [
                            'from' => ['sales@myservice.kg' => 'НА ТВ'], //от кого
                            'to' => ['musa@cs.kg'], //кому
                            'subject' => 'Критическая ошибка!', //тема
                        ],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/',
            //'class' => 'codemix\localeurls\UrlManager',
            //'languages' => ['ru','kr'],
            ///'enableLanguageDetection' => true,
            //'enableDefaultLanguageUrlCode' => true,
            //'enableLanguagePersistence' => false,
            'rules' => [
                'index' => 'site/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'result' => 'site/result',
                'about' => 'site/about',
                'admin' => 'site/admin',
                'export' => 'site/export',
                'getdata' => 'site/getdata',
                'getjsontvdates' => 'site/getjsontvdates',
                'getdatareport' => 'site/getdatareport',
                'searchajax' => 'site/searchajax',
                'getdatas' => 'site/getdatas',
                'getuserlist' => 'site/getuserlist',
                'getdatestocallback' => 'site/getdatestocallback',
                'callbacker' => 'site/callbacker',
                'report' => 'site/report',
                'add' => 'site/add',
                'useraccount' => 'site/useraccount',
                'download' => 'site/download',
                'exptexcel' => 'site/exptexcel',
                'exptexceladm' => 'site/exptexceladm',
                'mailer' => 'site/mailer',
                'getuserdata' => 'site/getuserdata',
                'gettvlist' => 'site/gettvlist',
                'getholidaydates' => 'site/getholidaydates',
                'deletegetholidaydates' => 'site/deletegetholidaydates',
                'setsave' => 'site/setsave',
                'setdata' => 'site/setdata',
                'remove' => 'site/remove',
                'onaction' => 'site/onaction',
                'ontvrawxml' => 'api/ontvrawxml',
                'ontvxwwwformxml' => 'api/ontvxwwwformxml',
                'ontvjson' => 'api/ontvjson',
                'getstatusjson' => 'api/getstatusjson',
                ['class' => 'yii\rest\UrlRule','controller' => 'api','pluralize'=>false],
            ],
        ],
        // 'i18n' => [
        //     'translations' => [
        //         'common*' => [
        //             'class' => 'yii\i18n\PhpMessageSource',
        //             'sourceLanguage' => 'ru-RU',
        //             'basePath' => '@app/messages'
        //         ],
        //     ],
        // ],
        
    ],
    'params' => $params,
    //'defaultRoute' => 'client/index',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1','10.240.101.23', '::1'],
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1','10.240.101.23', '::1'],
        'allowedIPs' => ['*'],
    ];
}

return $config;
