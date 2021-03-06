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
use app\models\ChangePassword;
use app\models\Filestore;
use app\models\UploadForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

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
        }elseif($action->id ==='onaction' || $action->id ==='exptexcel'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='exptexceladm' || $action->id === 'getuserlist'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='getdatestocallback' || $action->id === 'callbacker'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='getholidaydates' || $action->id === 'setsave'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='deletegetholidaydates' || $action->id === 'searchajax'){
            $this->enableCsrfValidation = false;
        }elseif($action->id ==='getdatareport' || $action->id === 'gettvinfo'){
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
        $tvlist = Yii::$app->HelperFunc->getTvlist();
        if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2){
            $count = MainHub::find()
                    ->filterWhere(['=', 'status', 0])
                    ->count();
            $mainhub = new MainHub();
            $model = new UploadForm();
            return $this->render('index',['model'=>$model,'upcount'=>$count,'mainhub'=>$mainhub,'tvlist'=>$tvlist]);
        }elseif(Yii::$app->user->identity->role == 1){
            return $this->redirect('/admin');
        }
    }
    
    public function actionAdd()
    {
        $tvlist = Yii::$app->HpFunc->getTvlist();
        if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2){
            $count = MainHub::find()
                    ->filterWhere(['=', 'status', 0])
                    ->count();
            $mainhub = new MainHub();
            $model = new UploadForm();
            return $this->render('add',
            ['model'=>$model,'upcount'=>$count,'mainhub'=>$mainhub,'tvlist'=>$tvlist]);
        }elseif(Yii::$app->user->identity->role == 1){
            return $this->redirect('/admin');
        }
    }

    public function actionAbout()
    {
        $messageLog = [
         'status' => '???????????? ???? ????????????.',
         'post' => ['test'=>'Uraaa!']
        ];
        print_r($messageLog);
        //Yii::error($messageLog,'writelog');
        //Yii::info($messageLog, 'sendlog'); //???????????? ?? ??????
        //$this->layout = 'client';
        //return $this->render('aboutRu');
    }
    
    public function actionLogin()
    {   
        $tvlist = Yii::$app->HelperFunc->getTvlist();
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
        $result = null;
        $model = new UploadForm();

        $userfile = UploadedFile::getInstance($model, 'userfile');

        if ($userfile) {
            $uid = uniqid(time(), true);
            $fileName = $uid . '.' . $userfile->extension;
            $filePath = Yii::getAlias(\Yii::$app->basePath.'/web/data/').$fileName;
            if ($userfile->saveAs($filePath)) {
                $result = Yii::$app->HelperFunc->savedb($fileName,$userfile->extension);
                if($result){
                    $result = 'OK';
                }else{
                    $result = $result;
                }
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
        $tvlist = Yii::$app->HelperFunc->getTvlist();
        if(Yii::$app->user->identity->role == 1){
            $count = MainHub::find()
                    ->filterWhere(['=', 'status', 0])
                    ->count();
            $mainhub = new MainHub();
            $model = new UploadForm();
            $this->layout = 'admin';
            return $this->render('report',['model'=>$model,'upcount'=>$count,'mainhub'=>$mainhub]);
        }elseif(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2){
            return $this->redirect('/');
        }
    }

    public function actionUseraccount()
    {
        if(Yii::$app->user->identity->role != 1){

            $model = new ChangePassword();
            if ($model->load(Yii::$app->getRequest()->post()) && $model->change()) {

                return $this->render('useraccount', [
                'model' => $model,
                'status' => '?????? ???????????? ?????????????? ?????????????? ?????? ???? ?????????????????? ???????? ?????? API',
                'accesstoken' => Yii::$app->user->identity->access_token,
                ]);
            }
            return $this->render('useraccount', [
                'model' => $model,
                'accesstoken' => Yii::$app->user->identity->access_token,
            ]);
            //return $this->render('useraccount');

        }elseif(Yii::$app->user->identity->role == 1){
            $this->layout = 'admin';
            $model = new ChangePassword();
            if ($model->load(Yii::$app->getRequest()->post()) && $model->change()) {

                return $this->render('useraccount', [
                'model' => $model,
                'status' => '?????? ???????????? ?????????????? ?????????????? ?????? ???? ?????????????????? ???????? ?????? API',
                'accesstoken' => Yii::$app->user->identity->access_token,
                ]);
            }
            return $this->render('useraccount', [
                'model' => $model,
                'accesstoken' => Yii::$app->user->identity->access_token,
            ]);
        }
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
            $retData = Yii::$app->HelperFunc->getTvlist();
            $this->layout = 'admin';
            return $this->render('export',['tvlist'=> $retData]);
        }else{
            return $this->redirect('/');
        }
    }

    public function actionGetdata()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        header('Content-Type: application/json');
        $data = Yii::$app->request->get();
        if($data['token'] == md5(Yii::$app->session->getId().'opn')){
          $retData = Yii::$app->HelperFunc->getData($data);
          
          return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count'],'total'=>$retData['totalsumm']],
                            'msg'=>'OK']
                          );
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
        }
    }
    public function actionGetdatareport(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = Yii::$app->request->get();
        if($data['token'] == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->user->identity->role == 1){
                try{
                $retData = Yii::$app->HelperFunc->getDataReport($data);
                return ['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],
                                     'count' => $retData['count'],
                                     'total'=>$retData['totalsumm'][0]['countSim']],
                            'msg'=>'OK'];
                }catch(\yii\base\Exception $ex){
                    Yii::error($ex,'writelog');
                    return $ex;
                }

            }else{
            return ['status'=>4,'message'=>'?????????????? ?????????????? ???? ??????????????????!'];
            }
        }else{
            return ['status'=>3,'message'=>'Error(Invalid token!)'];
        }
    }

    public function actionSearchajax()
    {
        header('Content-Type: application/json');
        $data = Yii::$app->request->post();
        if($data['token'] == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->user->identity->role == 1){
                $retData = Yii::$app->HelperFunc->getDataSearchAdm($data);
            }else{
                $retData = Yii::$app->HelperFunc->getDataSearchUsr($data);
            }
          return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count'],'total'=>[] ],
                            'msg'=>'OK']
                          );
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
        }
    }    
    public function actionGetuserdata()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        header('Content-Type: application/json');
        $request = Yii::$app->request;
        $data = $request->get();
        if($data['token'] == md5(Yii::$app->session->getId().'opn')){
          $retData = Yii::$app->HelperFunc->getUserData($data);
          
          return json_encode(['status'=>0,
                            'data'=>['mainlistview' => $retData['mlv'],'count' => $retData['count'],'total'=>$retData['totalsumm'] ],
                            'msg'=>'OK']
                          );
     }else{
      return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }

    public function actionGetdatas()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        header('Content-Type: application/json');
        $data = Yii::$app->request->get();
        //$data = [];
        $darr = [];
        //$data['chid'] = $req['chid'];
        if(!empty($data['dates'])){
            $dmas = explode(',', $data['dates']);
            for($i=0; $i<count($dmas); $i++){
                array_push($darr,$dmas[$i]);
            }
            $data['dates'] = $darr;
        }else{
            $data['dates'] = date('Y-m-d',strtotime('+1 days'));
        }

        if($data['token'] == md5(Yii::$app->session->getId().'opn')){
          $retData = Yii::$app->HelperFunc->getDatas($data);
          return json_encode(['status'=>0,
                            'data'=>[
                                'mainlistview' => $retData['mlv'],
                                'count'=>$retData['count']],
                            'msg'=>'OK']
                          );
      }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }

    public function actionGetdatestocallback()
    {
        header('Content-Type: application/json');
        $data = Yii::$app->request->post();
        
        if($data['token'] == md5(Yii::$app->session->getId().'opn')){
           $retData = (intval($data['cid']) == 0) ? Yii::$app->HelperFunc->dpickerblock() : Yii::$app->HelperFunc->getDatasToCallback($data);
           $holidays = Yii::$app->HelperFunc->holidays();

          return json_encode(['status'=>0,
                            'data'=>$retData,
                            'holidays' => $holidays,
                            'msg'=>'OK']
                          );
      }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
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
    public function actionGetuserlist()
    {
        header('Content-Type: application/json');
        $request = Yii::$app->request->get();
        if($request['token'] == md5(Yii::$app->session->getId().'opn') && Yii::$app->user->identity->role == 1){
          $retData = Yii::$app->HelperFunc->getUserlist();
          
          return json_encode(['status'=>0,
                              'data'=>['userlist' => $retData],'msg'=>'OK']);
     }else{
      return json_encode(array('status'=>3,'message'=>'Error(Invalid token or your access denied!)'));
      }
    }

    public function actionGetholidaydates()
    {
        header('Content-Type: application/json');
        $request = Yii::$app->request->get();
        if($request['token'] == md5(Yii::$app->session->getId().'opn') && Yii::$app->user->identity->role == 1){
          $retData = Yii::$app->HelperFunc->getHolidayDates($request);
          
          return json_encode(['status'=>0,
                              'data'=>[
                                        'hdlist' => $retData['hdlist'],
                                        'count' => $retData['count']],
                                'msg'=>'OK']);
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token or your access denied!)'));
        }
    }

    public function actionSetsave()
    {
        $data = Yii::$app->request->post();      
        header('Content-Type: application/json');
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            $saveresp = Yii::$app->HelperFunc->setsave($data);
            if($saveresp){                
                return json_encode(['status'=>0,'msg'=>'OK']);           
            }else{ return json_encode(['error' => $saveresp]); }
        }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
            return json_encode(array('status'=>2,'message'=>'???????????? ??????????! ???????????????????? ???????????????? ???????????????? ?????? ?????????????? ?? ?????????????? ????????????!'));
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
        }

    }

    public function actionDeletegetholidaydates()
    {
        header('Content-Type: application/json');
        $request = Yii::$app->request->get();
        if($request['token'] == md5(Yii::$app->session->getId().'opn') && Yii::$app->user->identity->role == 1){
          $retData = Yii::$app->HelperFunc->delHolidayDates($request);
          if($retData){
            return json_encode(['status'=>0,'msg'=>'OK']);
        }else{
            return json_encode(['status'=>0,'msg'=>$retData]);
        }
          
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token or your access denied!)'));
        }
    }

    public function actionSetdata()
    {
        $data = Yii::$app->request->post();      
        header('Content-Type: application/json');
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            $saved = Yii::$app->HelperFunc->save($data);
            if(intval($saved['id']) > 0){                
                return json_encode(['status'=>0,'message'=>$saved['message']]);           
            }else{
                return json_encode(['status'=>4,'message' => $saved['message']]); 
            }
     }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
        return json_encode(array('status'=>2,'message'=>'???????????? ??????????! ???????????????????? ???????????????? ???????????????? ?????? ?????????????? ?? ?????????????? ????????????!'));
     }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }

    public function actionCallbacker()
    {
        $data = Yii::$app->request->post();
        header('Content-Type: application/json');
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            $result = Yii::$app->HelperFunc->callback($data);
            if($result == null){
                return json_encode(['status'=>0,'msg'=>'OK']);
            }else{
                return json_encode(['status'=>1,'msg'=>$result]);
            }
         }elseif(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
            return json_encode(array('status'=>2,'message'=>'???????????? ??????????! ???????????????????? ???????????????? ???????????????? ?????? ?????????????? ?? ?????????????? ????????????!'));
         }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
         }
    }

    public function actionRemove()
    {
        $data = Yii::$app->request->post();     
        header('Content-Type: application/json');
        
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->HelperFunc->update($data)){
                return json_encode(['status'=>0,'msg'=>'OK']);
            }else{
                return json_encode(array('status'=>1,'message'=>'???????????? ?????? ???????????????? ????????????'));
            }
     }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
        return json_encode(array('status'=>2,'message'=>'???????????? ??????????! ???????????????????? ???????????????? ???????????????? ?????? ?????????????? ?? ?????????????? ????????????!'));
     }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }
    public function actionOnaction()
    {
        $data = Yii::$app->request->post();
        $retData = null;        
        
        header('Content-Type: application/json');
        
        if(isset($data['token']) == md5(Yii::$app->session->getId().'opn')){
            if(Yii::$app->HelperFunc->updateStatus($data)){
                //$retData = Yii::$app->HelperFunc->getData($data);
                return json_encode(['status'=>0,'msg'=>'OK']);
            }else{
                return json_encode(array('status'=>1,'message'=>'???????????? ?????? ???????????????? ????????????'));
            }
     }else if(isset($data['token']) != md5(Yii::$app->session->getId().'opn')){
        return json_encode(array('status'=>2,'message'=>'???????????? ??????????! ???????????????????? ???????????????? ???????????????? ?????? ?????????????? ?? ?????????????? ????????????!'));
     }else{
        return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
      }
    }    

    public function actionExptexceladm()
    {
        $req = Yii::$app->request;
        $data = $req->get();
        if($data['token'] === md5(Yii::$app->session->getId().'opn'))
        {
            // $styleArray = [
            //     'font' => ['bold' => true],
            //     'text-align'=> 'center',
            //     'borders' => [
            //         'allBorders' => [
            //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            //             'color' => ['argb' => '#000000'],
            //         ],
            //     ],    
            // ];
        $headers = ['???','????????','????????????????','??????????????????','??????????','???????? ??????????????','??????. ????????','??????. ??????.','??????????','????????????????','????????????'];
        //$abc = ['A','B','C','D','E','F','G','H','I','J','K'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getStyle('A1:K1')->applyFromArray($styleArray);
        //     for($i = 0; $i < count($abc); $i++){
        //       $sheet->getColumnDimension($abc[$i])->setAutoSize(true);
        //     }

        $rows = [];
        $retData = Yii::$app->HelperFunc->getDownData($data);
        foreach ($retData['mlv'] as $row) {
            foreach ($row as $key => $value) {
                if($key == 'chid'){
                    unset($row[$key]);
                }elseif($key == 'client_id'){
                    unset($row[$key]);
                }elseif($key == 'state'){
                    unset($row[$key]);
                }elseif($key == 'phone'){
                    unset($row[$key]);
                }else{
                  $row[$key] = $value;  
                }
            }
            $rows[] = $row;
        }

        $sheet->fromArray($headers,NULL,'A1');
        $sheet->fromArray($rows,NULL,'A2');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report-'.date('d.m.Y H:i:s').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // echo '<pre>';
        // print_r($rows);
        // echo '</pre>';
        }else{
            return json_encode(array('status'=>$req->get(),'message'=>'Error(Invalid token!)'));
        }
    }

    public function actionExptexcel()
    {
        $req = Yii::$app->request;
        $data = $req->get();
        if($data['token'] === md5(Yii::$app->session->getId().'opn'))
        {
        $headers = ['???','????????','??????????????????','??????????','???????? ??????????????','??????. ????????','??????. ??????.','??????????','????????????????','????????????'];
        
        $rows = [];
        $retData = Yii::$app->HelperFunc->getUserDownData($data);
        foreach ($retData['mlv'] as $row) {
            foreach ($row as $key => $value) {
                if($key == 'chid'){
                    unset($row[$key]);
                }elseif($key == 'client_id'){
                    unset($row[$key]);
                }elseif($key == 'phone'){
                    unset($row[$key]);
                }else{
                  $row[$key] = $value;  
                }
            }
            $rows[] = $row;
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers,NULL,'A1');
        $sheet->fromArray($rows,NULL,'A2');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report-'.date('d.m.Y H:i:s').'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // echo '<pre>';
        // print_r($rows);
        // echo '</pre>';
        }else{
            return json_encode(array('status'=>$req->get(),'message'=>'Error(Invalid token!)'));
        }

    }

    public function actionDownload()
    {
        $request = Yii::$app->request;
        $string = '';
        $darr = [];
        $data = [];
        $data['chid'] = $request->get('chid');
        $data['token'] = $request->get('token');
        header('Content-Type: text/plain');
        $fname = empty($request->get('dates')) ? date('Y-m-d',strtotime('+1 days')) : $request->get('dates');
        header('Content-Disposition: attachment;filename="'.$fname.'.txt"');
        header('Cache-Control: max-age=0');
        if($data['token'] === md5(Yii::$app->session->getId().'opn'))
        {
            if(!empty($request->get('dates'))){
                $dmas = explode(',', $request->get('dates'));
                for($i=0; $i<count($dmas); $i++){
                    array_push($darr,$dmas[$i]);
                }
                $data['dates'] = $darr;
            }else{
                $data['dates'] = date('Y-m-d',strtotime('+1 days'));
            }

            $retData = Yii::$app->HelperFunc->getDatas($data);
            $tarr = [];
            foreach ($retData['mlv'] as $item) {
                //$string .= $item['text']."\r\n";
                array_push($tarr, $item['text'].' '.$item['licdoc']);
            }
            shuffle($tarr);
            foreach ($tarr as $itm){
                $string .=$itm."\r\n";
            }
            return $string;
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
        }
    }

    public function actionMailer()
    {
        $request = Yii::$app->request;
        if($request->get('token') === md5(Yii::$app->session->getId().'opn'))
        {
            $msg = 0;
            $data = [];
            $darr = [];
            $string = '';
            $bucket = Yii::$app->fileStorage->getBucket('tempFiles');

            $data['chid'] = $request->get('chid');
            if(!empty($request->get('dates'))){
                $dmas = explode(',', $request->get('dates'));
                for($i=0; $i<count($dmas); $i++){
                    array_push($darr,$dmas[$i]);
                }
                $data['dates'] = $darr;
            }else{
                $data['dates'] = date('Y-m-d',strtotime('+1 days'));
            }
            $retData = Yii::$app->HelperFunc->getDatas($data);

            foreach ($retData['mlv'] as $item) {
                $string .= $item['text'].' '.$item['licdoc']."\r\n";
            }
            $bucket->saveFileContent(date('Y-m-d').'.txt', $string);
            $path = \Yii::$app->basePath."\web\\files\\tempFiles\\".date('Y-m-d').".txt";
              if(!empty($request->get('email'))){
                  $msg = Yii::$app->mailer->compose()
                  ->setFrom('sales@myservice.kg')
                  ->setTo($request->get('email'))
                  ->setSubject('???????? ??????????????????')
                  ->setTextBody('?????????? ??????????????????')
                  ->attach($path)
                  ->send();
              //->setHtmlBody('<b>?????????? ?????????????????? ?? ?????????????? HTML</b>')->send();
            }
              return $msg; //print_r($data);
        }else{
            return json_encode(array('status'=>3,'message'=>'Error(Invalid token!)'));
        }
    }

    public function actionGetjsontvdates()
    {
        $id = Yii::$app->request->get('id');
        $data = [
            'dates' => [
                    'begin' => strtotime( date('Y-m-d') ),
                    'end' => strtotime(date('Y-m-d',strtotime('+1 Year'))),
                    'begin_str' => date('Y-m-d'),
                    'end_str' => date('Y-m-d',strtotime('+1 Year')),
                    'discount' => ['3'=> 10, '7'=> 20, '10'=>30],
                    'list' => [],
                    'id' => $id,
                ]
        ];
        //print_r($data = Yii::$app->request->get());
        return json_encode($data);//$this->render('info',$data);
    }

    public function actionSaveticket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $response = ['error' => false,'msg' => '',];
    
        if (!$request->validateCsrfToken()) {
            $response = [
                'error' => true,
                'msg' => '????????????! ???? ???????????? ???????????????? CSFR'
            ];
            return $response;
        }
        if ($request->isAjax) {
            try {
                $data = $request->post();
                $inside = [];
                if(!isset($data['text']) || empty($data['text'])){
                    $response['error'] = true;
                    $response['msg'] = '?????????????? ?????????? ???????????? ????????????????????';
                    $response['data'] = '';
                }elseif(count(Yii::$app->HpFunc->dateValidation($data['dates'])) <= 0){
                    $response['error'] = true;
                    $response['msg'] = '???????????????? ????????????';
                    $response['data'] = '';
                }else{
                    //$data['countSim']=Yii::$app->HpFunc->getCountSim($data['text']);
                    $data['vdates'] = Yii::$app->HpFunc->dateValidation($data['dates']);
                    if(!empty($data['vdates'])){
                        foreach($data['vdates'] as $k => $v){
                            Yii::$app->HpFunc->save($k,$v,$data);
                        }
                    }
                    //$data['sum'] = Yii::$app->HpFunc->calculate($data);
                    //$sdata = Yii::$app->HpFunc->save($data);
                    $response['data'] = $data;
                }
                $response['data'] = $data;
            } catch (\yii\base\Exception $e) {
                $response['error'] = true;
                $response['msg'] = $e->getMessage();
            }
        } else {
            $response['error'] = true;
            $response['msg'] = 'OK';
        }
    
        return $response;
    }

}
