<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\MyReadFilter;
use app\componets\HelperFunc;

class SiteController extends Controller
{
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
        return $this->render('index');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionBannernayareklama()
    {
        return $this->render('bannernayareklama');
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionTest()
    {
        try{
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            //$reader->setLoadSheetsOnly(["sheet1"]);
            //$reader->setReadFilter( new MyReadFilter() );
            $spreadsheet = $reader->load('D:\testData.xlsx');
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

            Yii::$app->HelperFunc->save($rows);
            
        }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        if(Yii::$app->language == 'ru'){
            return $this->render('aboutRu');
        }else{
            return $this->render('aboutEn');
        }
        
    }
}
