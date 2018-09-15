<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;
use app\models\MainHub;

use app\models\Filestore;
use app\models\UploadForm;

use app\componets\HelperFunc;


class SiteController extends Controller
{

    public function beforeAction($action)
    {
        if (\Yii::$app->getUser()->isGuest && $action->id !== 'login' && $action->id !=='/'){
            Yii::$app->response->redirect(Url::to(['/login']), 301); //Url::to(['login'])
            Yii::$app->end();
        }elseif($action->id === 'result'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='getdata' || $action->id ==='getdatas'){
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
                    'logout' => ['POST'],
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
        $count = MainHub::find()
                ->filterWhere(['=', 'status', 0])
                ->count();
        $model = new UploadForm();
        return $this->render('index',['model'=>$model,'upcount'=>$count]);
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
            if(Yii::$app->user->identity->role == 0){
                return $this->redirect('/');
            }elseif(Yii::$app->user->identity->role == 1){
                return $this->redirect('/admin');
            }
        }else{
           $this->layout = 'login';
           return $this->render('login', ['model' => $model]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Url::to(['/login']));
    }

    public function actionResult()
    {
        $model = new UploadForm();

        $userfile = UploadedFile::getInstance($model, 'userfile');

        if ($userfile) {
            $uid = uniqid(time(), true);
            $fileName = $uid . '.' . $userfile->extension;
            $filePath = Yii::getAlias(\Yii::$app->basePath.'/web/data/').$fileName;
            if ($userfile->saveAs($filePath)) {
                
                $result = Yii::$app->HelperFunc->savedb($fileName);
                unlink($filePath);
                $count = MainHub::find()
                ->filterWhere(['=', 'status', 0])
                ->count();

                return Json::encode([
                    'files' => [
                        [
                            'name' => $fileName,
                            'size' => $userfile->size,
                            'status' => 0,
                            'count' => $count,
                            'result' => $result,
                            //'deleteUrl' => 'filedelete?name=' . $fileName,
                            //'deleteType' => 'POST',
                        ],
                    ],
                ]);
            }
        }

        return '';
    }

    public function actionReport()
    {
        return $this->render('report');
    }
    public function actionAdmin()
    {
        if(Yii::$app->user->identity->role == 1){
            $this->layout = 'admin';
            return $this->render('admin');
        }else{
            return $this->redirect('/');
        }
    }
    public function actionExport()
    {
        if(Yii::$app->user->identity->role == 1){
            $this->layout = 'admin';
            return $this->render('export');
        }else{
            return $this->redirect('/');
        }
    }

    public function actionGetdata()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        header('Content-Type: application/json');
        $request = Yii::$app->request;
        $token = $request->get('token');
        $data = [];
        $data['sts'] = $request->get('sts');
        $data['page'] = $request->get('page');
        $data['shpcount'] = 15;
         
        //if($token == md5(Yii::$app->session->getId().'opn')){
          $retData = Yii::$app->HelperFunc->getData($data);
          
          return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count']],
                            'msg'=>'OK']
                          );
     // }else{
     //  return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      //}
    }

    public function actionGetdatas()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        header('Content-Type: application/json');
        $request = Yii::$app->request;
        //$token = $request->get('token');
        $data = [];
        $data['dates'] = $request->get('dates');
         
        //if($token == md5(Yii::$app->session->getId().'opn')){
          $retData = Yii::$app->HelperFunc->getDatas($data);
          
          return json_encode(['status'=>0,
                            'data'=>[
                                'mainlistview' => $retData['mlv'],
                                'count'=>$retData['count']],
                            'msg'=>'OK']
                          );
     // }else{
     //  return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      //}
    }

    public function actionDownload()
    {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment;filename="'.date('d.m.Y H:i:s').'.txt"');
        header('Cache-Control: max-age=0');

        $retData = Yii::$app->HelperFunc->getDatas($data);

        foreach ($retData['mlv'] as $item) {
            $string .= $item['text']."\r\n";
        }
        return $string;
        
    }
}
