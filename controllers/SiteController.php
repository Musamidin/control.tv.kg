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

    public function actionResult()
    {
        $response = null;

        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->fileref = UploadedFile::getInstance($model, 'fileref');

            $fname = explode('.', $_FILES['UploadForm']['name']['fileref']);
            $fnames = md5($fname[0].date('Y-m-d H:i:s'));
            

            if ($model->upload($fnames)) {
                // file is uploaded successfully
                try{
                    
                    $flink = \Yii::$app->basePath."\web\data\\" . $fnames.'.'.$fname[1];
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $reader->setReadDataOnly(true);
                    //$reader->setLoadSheetsOnly(["sheet1"]);
                    //$reader->setReadFilter( new MyReadFilter() );
                    $spreadsheet = $reader->load($flink);
                    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                    //print_r($data);
                    unset($data[1]);
                    $rows = [];
                    foreach ($data as $row) {
                        foreach ($row as $key => $value) {
                            unset($row[$key]);
                            if($key == 'A'){
                                $row['channels'] = $value;
                            }elseif($key == 'B'){
                                unset($row[$key]);
                                $row['text'] = $value;
                            }elseif($key == 'C'){
                                unset($row[$key]);
                                $row['dates'] = $value;
                            }                
                        }
                        $rows[] = $row;
                    }

                    $response = Yii::$app->HelperFunc->save($rows);
                    //unlink($flink);
                }catch(Exception $e){
                    $response = $e;
                }
            }else{
               $response = null;
            }
        }

        return $this->render('result', ['model' => $response]);

    }    

}
