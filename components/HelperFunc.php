<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\data\Pagination;
use DateTime;

use app\models\MainHub;
use app\models\AdminModerView;
use app\models\DatesHub;
use app\models\ExportView;
use app\models\ClientView;
use app\models\UserDataView;
use app\models\Channels;
use app\models\ClientsDataView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use app\models\MyReadFilter;
use app\models\User;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelperFunc extends Component
{
    public function savedb($fileName,$fileType)
    {
        try{
             if($fileType == 'xlsx'){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
             }elseif($fileType == 'xls'){
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
             }
            $reader->setReadDataOnly(true);
            //$reader->setLoadSheetsOnly(["sheet1"]);
            //$reader->setReadFilter( new MyReadFilter() );
            $spreadsheet = $reader->load(\Yii::$app->basePath.'\web\data\\'.$fileName);
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            unset($data[1]);
            $rows = [];
            foreach ($data as $row) {
                foreach ($row as $key => $value) {
                    //unset($row[$key]);
                    if($key == 'A'){
                        unset($row[$key]);
                        $row['phone'] = $value;
                    }elseif($key == 'B'){
                        unset($row[$key]);
                        $row['chid'] = $value;
                    }elseif($key == 'C'){
                        unset($row[$key]);
                        $row['text'] = $value;
                    }elseif($key == 'D'){
                        unset($row[$key]);
                        $row['dates'] = $value;
                    }
                    // elseif($key == 'E'){
                    //     unset($row[$key]);
                    //     $row['state'] = $value;
                    // }
                }
                $rows[] = $row;
            }

            return $this->save($rows,false);
                    
        }catch(Exception $e){
            return $e;
        }
    }
    public function save($data,$single = false)
    {
            if($single == true){
                try{
                    $mh = new MainHub();
                    $mh->attributes = $data;
                    if($mh->validate()){
                        $mh->phone = $data['phone'];
                        $mh->chid = $data['chid'];
                        $mh->text = $data['text'];
                        $mh->dates = $data['dates'];
                        $mh->state = 0;
                        $mh->client_id = Yii::$app->user->identity->getId();
                        $mh->cday = $this->getCoutDays($data['dates']);
                        if($mh->save()){
                            $this->arr_map($data['dates'],$mh->id);
                            //return true;
                        }else{
                            return false; //['error'=> 'save false'];
                        }
                    }else{ return false; } //['error'=> 'validate false']; } 
                }catch(Exception $ex){
                    return $ex;
                }

            }else{
                try{
                    foreach($data as $itm) {
                        $mh = new MainHub();
                        $mh->attributes = $itm;
                        if($mh->validate()){
                            $mh->phone = $itm['phone'];
                            $mh->chid = $itm['chid'];
                            $mh->text = $itm['text'];
                            $mh->dates = $itm['dates'];
                            $mh->state = 0;//$itm['state'];
                            $mh->client_id = Yii::$app->user->identity->getId();
                            $mh->cday = $this->getCoutDays($itm['dates']);
                            if($mh->save()){
                                $this->arr_map($itm['dates'],$mh->id);
                                //return true;
                            }else{
                                return false;
                            }
                        }else{ return false; }
                    }  
                }catch(Exception $exc){
                    return $exc;
                }

            }   
    }

    public function arr_map($data,$id)
    {
      $response = null;
      if(preg_match("/[\-]+/",$data)){
        try{
          $arr = preg_split("/[\-]+/",$data);
          $dts = date('Y-m-d',strtotime(str_replace('/', '-', $arr[0])));
          $dte = date('Y-m-d',strtotime(str_replace('/', '-', $arr[1])));
          $start = new DateTime($dts);
          $interval = new \DateInterval('P1D');
          $end = new DateTime($dte);
          $end->add(new \DateInterval('P1D'));
          $period = new \DatePeriod($start, $interval, $end);

            foreach ($period as $date) {
              $dh = new DatesHub();
              $dh->daterent = $date->format('Y-m-d');
              //date('Y-m-d',strtotime(str_replace('/', '-', $arr[$i])));
              $dh->astatus = 0;
              $dh->mid = $id;
              $dh->save();
            }
        }catch(Exception $e){
          return $e;
        }

      }elseif(preg_match("/[\,]+/",$data)){
        try{
          $arr = preg_split("/[\,]+/",$data);
          for($i = 0; $i < count($arr); $i++){
                  $dh = new DatesHub();
                  $dh->daterent = date('Y-m-d',strtotime(str_replace('/', '-', $arr[$i])));
                  $dh->astatus = 0;
                  $dh->mid = $id;
                  $dh->save();
          }
        }catch(Exception $e){
          return $e;
        }
      }else{
        if(!empty($data)){
            $dh = new DatesHub();
            $dh->daterent = date('Y-m-d',strtotime(str_replace('/', '-', $data)));
            $dh->astatus = 0;
            $dh->mid = $id;
            $dh->save();

        }else{ return false; }
      }
    }

   public function getData($param)
   {
        $data = [];
        try{
            $da = explode('/', $param['daterange']);
            $df = trim($da[0]).'T00:00:00';
            $dt = trim($da[1]).'T23:59:59';
            $sts = (intval($param['sts']) === -1) ? [] : ['status' => $param['sts']];
            $bytv = (intval($param['bytv']) === 0) ? [] : ['chid' => $param['bytv']];
            $sortbycli = (intval($param['sortbycli']) === 0) ? [] : ['client_id' => $param['sortbycli']];

            $data['count'] = AdminModerView::find()
            ->where(['!=','status',88])
            ->andWhere($bytv)
            ->andWhere($sortbycli)
            ->andWhere(['BETWEEN','datetime',$df,$dt])
            ->andWhere($sts)
            ->count();
            $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
            $data['mlv'] = AdminModerView::find()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->where(['!=','status',88])
            ->andWhere($bytv)
            ->andWhere($sortbycli)
            ->andWhere(['BETWEEN','datetime',$df,$dt])
            ->andWhere($sts)
            ->asArray()
            ->orderBy(['id'=>SORT_DESC])
            ->all();
            
            $str_bytv = (intval($param['bytv']) === 0) ? '' : 'AND chid = '.$param['bytv'];
            $str_sts = (intval($param['sts']) === -1) ? 'AND status <> 88' : 'AND status = '.$param['sts'];
            $str_sortbycli = (intval($param['sortbycli']) === 0) ? '' : 'AND client_id = '.$param['sortbycli'];

              $command=Yii::$app->db->createCommand("SELECT SUM(simcount) as allcs, SUM(cday) as allcd, SUM(summ) as allsumm FROM adminModerView WHERE [datetime] BETWEEN '".$df."' AND '".$dt."' {$str_bytv} {$str_sts} {$str_sortbycli}");
            $data['totalsumm'] = $command->queryAll();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }

   public function getDownData($param)
   {
        $data = [];
        try{
            $da = explode('/', $param['daterange']);
            $df = trim($da[0]).'T00:00:00';
            $dt = trim($da[1]).'T23:59:59';
            $sts = (intval($param['sts']) === -1) ? [] : ['status' => $param['sts']];
            $bytv = (intval($param['bytv']) === 0) ? [] : ['chid' => $param['bytv']];
            $sortbycli = (intval($param['sortbycli']) === 0) ? [] : ['client_id' => $param['sortbycli']];

            // $data['count'] = AdminModerView::find()
            // ->where(['!=','status',88])
            // ->andWhere($bytv)
            // ->andWhere($sortbycli)
            // ->andWhere(['BETWEEN','datetime',$df,$dt])
            // ->andWhere($sts)
            // ->count();

            $data['mlv'] = AdminModerView::find()
            ->where(['!=','status',88])
            ->andWhere($bytv)
            ->andWhere($sortbycli)
            ->andWhere(['BETWEEN','datetime',$df,$dt])
            ->andWhere($sts)
            ->asArray()
            ->orderBy(['id'=>SORT_DESC])
            ->all();
            
            // $str_bytv = (intval($param['bytv']) === 0) ? '' : 'AND chid = '.$param['bytv'];
            // $str_sts = (intval($param['sts']) === -1) ? 'AND status <> 88' : 'AND status = '.$param['sts'];
            // $str_sortbycli = (intval($param['sortbycli']) === 0) ? '' : 'AND client_id = '.$param['sortbycli'];

            //   $command=Yii::$app->db->createCommand("SELECT SUM(simcount) as allcs, SUM(cday) as allcd, SUM(summ) as allsumm FROM adminModerView WHERE [datetime] BETWEEN '".$df."' AND '".$dt."' {$str_bytv} {$str_sts} {$str_sortbycli}");
            // $data['totalsumm'] = $command->queryAll();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }

   public function getUserDownData($pdata)
   {
        $data = [];
        try{
            $da = explode('/', $pdata['daterange']);
            $df = trim($da[0]).'T00:00:00';
            $dt = trim($da[1]).'T23:59:59';
            $bytv = (intval($pdata['bytv']) === 0) ? [] : ['chid'=>$pdata['bytv']];
            // $data['count'] = UserDataView::find()
            // ->where(['status'=>$pdata['sts'],'client_id'=> Yii::$app->user->identity->getId()])
            // ->andWhere(['BETWEEN','datetime',$df,$dt])
            // ->andWhere($bytv)
            // ->count();

              $data['mlv'] = UserDataView::find()
              ->where(['status'=> $pdata['sts'],'client_id'=> Yii::$app->user->identity->getId()])
              ->andWhere(['BETWEEN','datetime',$df,$dt])
              ->andWhere($bytv)
              ->asArray()
              ->orderBy(['datetime'=>SORT_DESC])
              ->all();
              // $bytvs = (intval($pdata['bytv']) === 0) ? '' : 'AND chid = '.$pdata['bytv'];

              // $command=Yii::$app->db->createCommand("SELECT SUM(simcount) as allcs, SUM(cday) as allcd, SUM(summ) as allsumm FROM userDataView WHERE client_id =".Yii::$app->user->identity->getId()." AND status = ".$pdata['sts']." AND datetime BETWEEN '".$df."' AND '".$dt."' {$bytvs}");

              // $data['totalsumm'] = $command->queryAll();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }
   public function getUserData($param)
   {
        $data = [];
        try{
            $da = explode('/', $param['daterange']);
            $df = trim($da[0]).'T00:00:00';
            $dt = trim($da[1]).'T23:59:59';
            $bytv = (intval($param['bytv']) === 0) ? [] : ['chid'=>$param['bytv']];
            $data['count'] = UserDataView::find()
            ->where(['status'=>$param['sts'],'client_id'=> Yii::$app->user->identity->getId()])
            ->andWhere(['BETWEEN','datetime',$df,$dt])
            ->andWhere($bytv)
            ->count();
              $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
              $data['mlv'] = UserDataView::find()
              ->where(['status'=> $param['sts'],'client_id'=> Yii::$app->user->identity->getId()])
              ->andWhere(['BETWEEN','datetime',$df,$dt])
              ->andWhere($bytv)
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->asArray()
              ->orderBy(['datetime'=>SORT_DESC])
              ->all();
              $bytvs = (intval($param['bytv']) === 0) ? '' : 'AND chid = '.$param['bytv'];

              $command=Yii::$app->db->createCommand("SELECT SUM(simcount) as allcs, SUM(cday) as allcd, SUM(summ) as allsumm FROM userDataView WHERE client_id =".Yii::$app->user->identity->getId()." AND status = ".$param['sts']." AND datetime BETWEEN '".$df."' AND '".$dt."' {$bytvs}");

              $data['totalsumm'] = $command->queryAll();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }
   public function getDatas($par)
   {
        $data = [];
      try{
              $data['count'] = ExportView::find()
              ->where(['IN','daterent',$par['dates']])
              ->andWhere(['chid'=> $par['chid']])
              ->count();
              $data['mlv'] = ExportView::find()
              ->where(['IN','daterent',$par['dates']])
              ->andWhere(['chid'=> $par['chid']])
              ->asArray()
              ->orderBy(['daterent'=>SORT_DESC])
              ->all();

        return $data;

      }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }
   public function getTvlist()
   {
        $data = [];
        try{
              $data['tvlist'] = Channels::find()
              ->select('id, channel_name,email')
              ->where(['status'=> 0])
              ->asArray()
              ->orderBy(['id'=>SORT_ASC])
              ->all();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }
   public function getClients()
   {
        try{
              return User::find()
              ->select('id, name')
              ->where(['status'=> 0])
              ->andWhere(['<>','role',1])
              ->asArray()
              ->orderBy(['id'=>SORT_ASC])
              ->all();

        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }

   public function update($data)
   {
        try{
            if(MainHub::updateAll(['status'=> 88],['IN', 'id',$data['ids']])){
                return true;
            }
             
        }catch(Exception $ex){
            return $ex->errorInfo;
        }

   }

   public function updateStatus($data)
   {
        try{
            if(MainHub::updateAll(['status'=> $data['status'],'description'=> $data['description']],['IN', 'id',$data['ids']])){
                return true;
            }
             
        }catch(Exception $ex){
            return $ex->errorInfo;
        }

   }

  public function find_dates_between( $start_date, $end_date) 
  {
      $start = new DateTime($start_date);
      $interval = new DateInterval('P1D');
      $end = new DateTime($end_date);
      $end->add(new DateInterval('P1D'));
      $period = new DatePeriod($start, $interval, $end);

      foreach ($period as $date) {
      echo $date->format('d/m/Y') . "<br />";
      }
  }

  public function getCoutDays($dates)
  {

        if(preg_match("/[\-]+/",$dates)){
            try{
                $arr = preg_split("/[\-]+/",$dates);
                $datetime1 = new DateTime( date('Y-m-d',strtotime(str_replace('/', '-', $arr[0]))) );
                $datetime2 = new DateTime( date('Y-m-d',strtotime(str_replace('/', '-', $arr[1]))) );
                $interval = $datetime1->diff($datetime2);
                return IntVal($interval->d)+1;
            }catch(Exception $ex){
                return $ex;
            }
        }elseif(preg_match("/[\,]+/",$dates)){
            try{
                $arr = preg_split("/[\,]+/",$dates);
                return count($arr);
            }catch(Exception $e){
                return $e;
            }
        }else{
            return !empty($dates);
        }
    }


}
