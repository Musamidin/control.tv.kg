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
        }elseif($action->id === 'result' || $action->id === 'mailer'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='getdata' || $action->id ==='getdatas'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='getuserdata' || $action->id ==='gettvlist'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='setdata' || $action->id ==='remove'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='onaction'){
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
                    'setdata' => ['POST'],
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
        $mainhub = new MainHub();        
        $model = new UploadForm();
        return $this->render('index',['model'=>$model,'upcount'=>$count,'mainhub'=>$mainhub]);
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
            if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2){
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
                
                $result = Yii::$app->HelperFunc->savedb($fileName,$userfile->extension);
                unlink($filePath);
                $count = MainHub::find()
                ->filterWhere(['=', 'status', 0])
                ->count();
                //sleep(2);
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
    public function actionGetuserdata()
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
          $retData = Yii::$app->HelperFunc->getUserData($data);
          
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
        $data = [];
        $data['channel'] = $request->get('channel');
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
    public function actionGettvlist()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        header('Content-Type: application/json');
        $request = Yii::$app->request;
        //if($token == md5(Yii::$app->session->getId().'opn')){
          $retData = Yii::$app->HelperFunc->getTvlist();
          
          return json_encode(['status'=>0,
                              'data'=>['tvlist' => $retData['tvlist']],'msg'=>'OK']);
     // }else{
     //  return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      //}
    }
    public function actionSetdata()
    {
        $data = Yii::$app->request->post();
        $data['state'] = 0;
        $retData = null;        
        
        header('Content-Type: application/json');
        
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->HelperFunc->save($data,true)){
                $retData = Yii::$app->HelperFunc->getUserData($data);
                
                return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count']],
                            'msg'=>'OK']
                        );                
            }
     }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
        return json_encode(array('status'=>2,'message'=>'Сессия истек! Пожалуйста обновите страницу или зайдите в систему заново!'));
     }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }

    public function actionRemove()
    {
        $data = Yii::$app->request->post();
        $data['state'] = 0;
        $retData = null;        
        
        header('Content-Type: application/json');
        
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->HelperFunc->update($data)){
                $retData = Yii::$app->HelperFunc->getUserData($data);
                return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count']],
                            'msg'=>'OK']
                        );
            }else{
                return json_encode(array('status'=>1,'message'=>'Ошибка при удаления записи'));
            }
     }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
        return json_encode(array('status'=>2,'message'=>'Сессия истек! Пожалуйста обновите страницу или зайдите в систему заново!'));
     }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }
    public function actionOnaction()
    {
        $data = Yii::$app->request->post();
        $data['state'] = 0;
        $retData = null;        
        
        header('Content-Type: application/json');
        
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->HelperFunc->updateStatus($data)){
                $retData = Yii::$app->HelperFunc->getData($data);
                return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count']],
                            'msg'=>'OK']
                        );
            }else{
                return json_encode(array('status'=>1,'message'=>'Ошибка при удаления записи'));
            }
     }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
        return json_encode(array('status'=>2,'message'=>'Сессия истек! Пожалуйста обновите страницу или зайдите в систему заново!'));
     }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }    

    public function actionDownload()
    {
        $string = '';
        $data['channel'] = Yii::$app->request->get('id');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment;filename="'.date('d.m.Y H:i:s').'.txt"');
        header('Cache-Control: max-age=0');

        $retData = Yii::$app->HelperFunc->getDatas($data);

        foreach ($retData['mlv'] as $item) {
            $string .= $item['text']."\r\n";
        }
        return $string;
        
    }

    public function actionMailer()
    {
        $string = '';
        $bucket = Yii::$app->fileStorage->getBucket('tempFiles');

        $data['channel'] = Yii::$app->request->get('channel');
        $retData = Yii::$app->HelperFunc->getDatas($data);

        foreach ($retData['mlv'] as $item) {
            $string .= $item['text']."\r\n";
        }
        $bucket->saveFileContent(date('Y-m-d').'.txt', $string);
        $path = \Yii::$app->basePath."\web\\files\\tempFiles\\".date('Y-m-d').".txt";
          $msg = Yii::$app->mailer->compose()
          ->setFrom('sales@myservice.kg')
          ->setTo('avtin@yandex.ru')
          ->setSubject('Тема сообщения')
          ->setTextBody('Текст сообщения')
          ->attach($path)
          ->send();
          //->setHtmlBody('<b>текст сообщения в формате HTML</b>')->send();
          
          
          return $msg; //print_r($data);
    }
}
