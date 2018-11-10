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
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(), //включаем аутентификацию по токену
            'except' => ['options','login'],
        ];


        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
                'application/html' => Response::FORMAT_HTML,
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

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => [
                'update',
                //'delete',
                'view',
                'index',
            ],
            'rules' => [
                [
                    'actions' => [
                        // 'update',
                        // 'delete',
                        // 'view',
                        //'index',
                        'ontvrawxml' => ['POST'],
                        'ontvxwwwformxml' => ['POST'],
                        'ontvjson' => ['POST'],
                        'getstatusjson' => ['POST'],
                    ],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                // 'signup' => ['POST'],
                // 'login' => ['POST'],
                // 'update' => ['PUT'],
                // 'delete' => ['DELETE'],
                // 'view' => ['GET'],
                // 'index' => ['GET'],
                'ontvrawxml' => ['POST'],
                'ontvxwwwformxml' => ['POST'],
                'ontvjson' => ['POST'],
                'getstatusjson' => ['POST'],
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
    public function actionOntvrawxml()
    {
        \Yii::$app->response->format = Response:: FORMAT_XML;

        $data = Yii::$app->Modules->xmlToArray(Yii::$app->request->getRawBody());
        $resp = Yii::$app->HelperFunc->save($data);
        return $resp;
        //$arr = Yii::$app->HelperFunc->validateDates($data['dates']);
        //$hd = Yii::$app->HelperFunc->upDatesStr($data['dates']);
        //print_r($hd);

        //Yii::$app->request->getRawBody();
/*
        [
                'method:' => Yii::$app->request->getMethod(), 
                'POSTER' =>$_POST, 
                'GETTER' => $_GET,
                'IP' => Yii::$app->request->userIP,
                'ts' => $request->bodyParams,
                ]; */
    }

    public function actionOntvxwwwformxml()
    {
        
        \Yii::$app->response->format = Response:: FORMAT_XML;

        return Yii::$app->request->getRawBody;
    }

    public function actionOntvjson()
    {
    	$resp = null;
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;
        $model = new MainHub();
        $model->attributes = Yii::$app->request->post();
        if($model->validate()){
        	//return Yii::$app->request->post('phone');
        	$resp = Yii::$app->HelperFunc->save(Yii::$app->request->post());
        	if($resp != false){
        		return ['status'=>0, 'message'=> 'OK','id'=>$resp];
        	}else{
        		return $resp;
        	}
        }else{
        	return $model->errors;
        }
        //return Yii::$app->request->post('phone');
        //Yii::$app->HelperFunc->save($data,true)
        //return Yii::$app->request->headers['authorization'];

        
    }
    public function actionGetstatusjson()
    {
        \Yii::$app->response->format = \yii\web\Response:: FORMAT_JSON;
        $response = MainHub::find()
        ->select(['status','description'])
        ->where(['id'=> Yii::$app->request->post('id')])
        ->andWhere(['client_id'=>Yii::$app->user->identity->getId()])
        ->one();
        if($response)
            return $response;
        else
            return ['status'=>-1, 'description'=>'record not found!'];
    }    
}