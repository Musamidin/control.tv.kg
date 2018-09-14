<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Url;

use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;

use app\models\Filestore;
use app\models\UploadForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\MyReadFilter;
use app\componets\HelperFunc;


class SiteController extends Controller
{

    public function beforeAction($action)
    {
        if (\Yii::$app->getUser()->isGuest && $action->id !== 'login' && $action->id !=='/'){
            Yii::$app->response->redirect(Url::to(['login']), 301); //Url::to(['login'])
            Yii::$app->end();
        }elseif($action->id === 'result'){
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);    
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'result' => ['POST','FILES'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //$this->layout = 'client';
        $model = new UploadForm();
        return $this->render('index',['model'=>$model]);
    }
    public function actionAbout()
    {
        //$this->layout = 'client';
        return $this->render('aboutRu');
    }
    
    public function actionLogin()
    {
        $model = new LoginForm();
        if ( $model->load(Yii::$app->request->post()) && $model->login() ) {
            return $this->redirect('/');
        }else{
           $this->layout = 'login';
           return $this->render('login', ['model' => $model]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('login');
    }

    // public function actionResult()
    // {
    //     header('Content-Type: application/json');

    //     return json_encode(['test'=>Yii::$app->request->headers->get('token')]);
    // }

    public function actionResult()
    {
        //header('Content-Type: application/json');
        $response = null;

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {

            $model->userfile = UploadedFile::getInstance($model, 'userfile');

            // $fname = explode('.', $_FILES['userfile']['name']);
            // $fnames = md5($fname[0].date('Y-m-d H:i:s'));
            // $flink = \Yii::$app->basePath."\web\data\\" . $fnames.'.'.$fname[1];

            $model->upload();

            // if ($model->upload($fname)) {
            //     // file is uploaded successfully
            //     try{
                    
                    
            //         $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            //         $reader->setReadDataOnly(true);
            //         //$reader->setLoadSheetsOnly(["sheet1"]);
            //         //$reader->setReadFilter( new MyReadFilter() );
            //         $spreadsheet = $reader->load($flink);
            //         $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            //         unset($data[1]);
            //         $rows = [];
            //         foreach ($data as $row) {
            //             foreach ($row as $key => $value) {
            //                 unset($row[$key]);
            //                 if($key == 'A'){
            //                     $row['channels'] = $value;
            //                 }elseif($key == 'B'){
            //                     unset($row[$key]);
            //                     $row['text'] = $value;
            //                 }elseif($key == 'C'){
            //                     unset($row[$key]);
            //                     $row['dates'] = $value;
            //                 }                
            //             }
            //             $rows[] = $row;
            //         }

            //         $response = Yii::$app->HelperFunc->save($rows);
            //         //unlink($flink);
            //     }catch(Exception $e){
            //         $response = $e;
            //     }
            // }else{
            //    $response = 'not successfully uploaded!';
            // }
        }
        print_r($_FILES);
        //return json_encode(['response'=>$ts ]);
        //$this->render('result', ['model' => $response]);
        //Yii::$app->request->headers->get('token')

    } 

}
