<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Request;
use yii\rest\ActiveController;
use app\componets\HelperFunc;
use yii\filters\AccessControl;

use yii\filters\auth\HttpBearerAuth;

use yii\filters\auth\CompositeAuth;
//use yii\filters\auth\HttpBasicAuth;
//use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use app\models\MainHub;


class ApiController extends ActiveController
{
    public $modelClass = 'app\models\MainHub';

    public function init()
    {
        parent::init();
        // отключаем механизм сессий в api
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->loginUrl = null;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // $behaviors['authenticator'] = [
        //     'class' => CompositeAuth::className(),
        //     'authMethods' => [
        //         HttpBearerAuth::className(),
        //     ],
        // ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(), //включаем аутентификацию по токену
            'except' => ['options','login'],
        ];


        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];

        // add CORS filter
        // $behaviors['corsFilter'] = [
        //     'class' => \yii\filters\Cors::className(),
        //     'cors' => [
        //         'Origin' => ['*'],
        //         'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        //         'Access-Control-Request-Headers' => ['*'],
        //     ],
        // ];

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options', 'login', 'signup'];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => [
                'update',
                'delete',
                'view',
                'index',
            ],
            'rules' => [
                [
                    'actions' => [
                        'update',
                        'delete',
                        'view',
                        'index',
                        'ontv',
                    ],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'signup' => ['POST'],
                'login' => ['POST'],
                'update' => ['PUT'],
                'delete' => ['DELETE'],
                'view' => ['GET'],
                'index' => ['GET'],
                //'ontv' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        // disable the "delete" and "create" actions
        //unset($actions['delete'], $actions['create'], $actions['ontv']);

        //$actions['index']['create'] = [$this, 'create'];

        return $actions;
    }

    /**
     * Creates a new MainHub model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionOntv()
    {
        //\Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;
        // $mh = new MainHub();
        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     // return $this->redirect(['view', 
        //     //     'id' => $model->id,
        //     //     //'vi' => Yii::$app->request->post()
        //     //     ]);

        // } else {
        //     return $this->render('create', [
        //         'model' => $model,
        //     ]);
        // }
        //$mh->scenario = MainHub::SCENARIO_CREATE;
 
        // $mh->attributes = \yii::$app->request->post();
 
        //   if($mh->validate())
        //   {
        //    $mh->save();
        //    return array('status' => true, 'data'=> 'mh record is successfully updated');
        //   }else{
        //    return array('status'=>false,'data'=>$mh->getErrors());
        //   }
        //return Yii::$app->request->getMethod();
        //return $this->redirect(['view','id' => \yii::$app->request->post()->id]);
        //return Yii::$app->getRequest()->getBodyParams();//$_GET;//Yii::$app->getRequest()->getBodyParams();
        // $methods_to_check = array('POST', 'PUT');
        // if(in_array(strtoupper(Yii::$app->request->getMethod()), $methods_to_check)){
        //     return ['method is:' => Yii::$app->request->getMethod()];
        // }else{
        //     return $_REQUEST;//Yii::$app->request->getMethod();
        // }
        return [
                'method:' => Yii::$app->request->getMethod(), 
                'POSTER' =>$_POST, 
                'GETTER' => $_GET,
                'IP' => Yii::$app->request->userIP,
                'ts' => $request->bodyParams,
                ];
    }


}
