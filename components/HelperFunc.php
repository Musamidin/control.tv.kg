<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\data\Pagination;
use DateTime;

use app\models\MainHub;
use app\models\AdminModerView;
use app\models\DailyCountSimView;
use app\models\DatesHub;
use app\models\ExportView;
use app\models\ClientView;
use app\models\UserDataView;
use app\models\Channels;
use app\models\Holidays;
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
                        $row['chid'] = $value;
                    }elseif($key == 'B'){
                        unset($row[$key]);
                        $row['text'] = $value;
                    }elseif($key == 'C'){
                        unset($row[$key]);
                        $row['dates'] = $value;
                    }
                    $this->save($row);
                }
                $rows[] = $row;
            }

        //return $this->save($rows);
                    
      }catch(Exception $e){
            return $e;
      }
  }

  public function save($data)
  {
    try{
          $mh = new MainHub();
          $mh->attributes = $data;
          if($mh->validate())
          {
            $vdate = $this->validateDates($data['dates']);
            if(!empty($vdate) && count($vdate) > 0)
            {
              //$mh->phone = $data['phone'];
              $mh->chid = $data['chid'];
              $mh->text = $data['text'];
              $mh->licdoc = isset($data['licdoc']) ? $data['licdoc'] : '';
              $mh->dates = $this->upDatesStr($vdate);
              $mh->state = 0;
              $mh->client_id = Yii::$app->user->identity->getId();
              $mh->cday = count($vdate);
              if($mh->save())
              {
                foreach($vdate as $itm)
                {
                  $dh = new DatesHub();
                  $dh->daterent = $itm;
                  $dh->astatus = 0;
                  $dh->mid = $mh->id;
                  $dh->save();
                }
                return [
                        'id'=> $mh->id,
                        'coutDays' => $mh->cday,
                        'status' => 0,
                        'message'=> '???????????? ?????????????? ????????????????'
                        ];
              }else{
                return [
                        'id'=> 0,
                        'coutDays' => 0,
                        'status' => 1,
                        'message'=>'?????????????????? ???????????? ?????? ???????????????????? ?? ????. ???????????????????? ?? ????????????????????????????!'
                        ];
              }
            }else{
              return [
                        'id'=> 0,
                        'coutDays' => 0,
                        'status' => 2,
                        'message'=>'???????? ?????????????? ????????????????????????'
                        ];
            }
          }else{
            return $mh->errors;
          }
        }catch(\yii\base\Exception $ex){
          return $ex;
        }
  }

  public function checkDates($dates)
  {
    $hdays = [];
    $date = date('Y-m-d',strtotime(str_replace('/', '-', $dates)));
    if($date >= $this->dpickerblock())
    {
      if(count($this->holidaysf()) > 0)
      {
        if( in_array( $date, $this->holidaysf() ) ){
            return false;
        }else{
            return true;
        }
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

/* ?????????????? validateDates() ?????????????????? ?????????????????? ???????? 
?????????????? (dd/mm/yyyy,dd/mm/yyyy) ?????? (dd/mm/yyyy-dd/mm/yyyy) 
???????????????????? ???????????? ???????? ?????????????? YYYY-mm-dd ?????? ???????????? ???????????? [] 
*/
  public function validateDates($data)
  {
    $dates = [];
    if(preg_match("/[\-]+/",$data)){
        try{
            $arr = preg_split("/[\-]+/",$data);
            $stime1 = strtotime(str_replace('/', '-', $arr[0]));
            $stime2 = strtotime(str_replace('/', '-', $arr[1]));
            if($stime1 != false && $stime2 != false)
            {
              $dts = date('Y-m-d',$stime1);
              $dte = date('Y-m-d',$stime2);
              if($this->checkDates($dts) && $this->checkDates($dte))
              {
                $start = new DateTime($dts);
                $interval = new \DateInterval('P1D');
                $end = new DateTime($dte);
                $end->add(new \DateInterval('P1D'));
                $period = new \DatePeriod($start, $interval, $end);
                foreach ($period as $date)
                {
                  array_push($dates, $date->format('Y-m-d'));
                }
                return $dates;
              }

            }
          return $dates;
        }catch(\yii\base\Exception $e){
          Yii::error($e->getMessage(),'writelog');
          //Yii::info($e->getMessage(), 'sendlog');
        }

    }elseif(preg_match("/[\,]+/",$data)){
        try{
            $arr = explode(',', $data);
            foreach($arr as $itm)
            {
              if($this->checkDates(trim($itm)))
              {
                $strtime = strtotime(str_replace('/', '-', $itm));
                if($strtime != false){
                  array_push($dates, date('Y-m-d',$strtime));
                }
              }
            }
            return $dates;
        }catch(Exception $e){
          return $e;
        }
    
    }else{
        if(!empty($data)){
          $strtime = strtotime(str_replace('/', '-', $data));
          if($strtime != false){
            array_push($dates,date('Y-m-d',$strtime));
          }
          return $dates;
        }
        return $dates;
    }
  }

  public function upDatesStr($data)
  {
    $str = '';
    if(count($data) > 0){
        foreach ($data as $itm) {
          $str .= implode('/', array_reverse(explode('-', $itm))).',';
        }
    }
    return substr($str, 0,-1);
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

  public function getDataReport($param)
  {
        $data = [];
        try{
            $da = explode('/', $param['daterange']);
            $df = trim($da[0]);
            $dt = trim($da[1]);
            $bytv = (intval($param['bytv']) === 0) ? [] : ['chid' => $param['bytv']];
            $sortbycli = (intval($param['sortbycli']) === 0) ? [] : ['client_id' => $param['sortbycli']];

            $data['count'] = DailyCountSimView::find()
            ->andWhere($bytv)
            ->andWhere($sortbycli)
            ->andWhere(['BETWEEN','daterent',$df,$dt])
            ->andWhere(['IN','status',[0,1]])
            ->count();
            $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
            $data['mlv'] = DailyCountSimView::find()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->andWhere($bytv)
            ->andWhere($sortbycli)
            ->andWhere(['BETWEEN','daterent',$df,$dt])
            ->andWhere(['IN','status',[0,1]])
            ->asArray()
            ->orderBy(['id'=>SORT_DESC])
            ->all();
            
            $str_bytv = (intval($param['bytv']) === 0) ? '' : 'AND chid = '.$param['bytv'];
            $str_sortbycli = (intval($param['sortbycli']) === 0) ? '' : 'AND client_id = '.$param['sortbycli'];

           $command=Yii::$app->db->createCommand("SELECT SUM(countSim) AS countSim FROM [dbo].[dailyCountSimView] WHERE status IN(0,1) AND [daterent] BETWEEN '".$df."' AND '".$dt."' {$str_bytv} {$str_sortbycli}");
            $data['totalsumm'] = $command->queryAll();

          return $data;
        }catch(\yii\base\Exception $ex){
            return $ex->errorInfo;
        }
  }

  public function getDataSearchAdm($param)
  {
        $data = [];
        try{
            $key = ($param['field'] === 'id') ? '=' : 'LIKE';
            $data['count'] = AdminModerView::find()
            ->filterWhere([$key, $param['field'], $param['key']])
            ->andWhere(['!=','status',88])
            ->count();

            $data['mlv'] = AdminModerView::find()
            ->filterWhere([$key, $param['field'], $param['key']])
            ->andWhere(['!=','status',88])
            ->asArray()
            ->orderBy(['id'=>SORT_DESC])
            ->all();
            
          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
  }

  public function getDataSearchUsr($param)
  {
        $data = [];
        try{
            $key = ($param['field'] === 'id') ? '=' : 'LIKE';
            $data['count'] = UserDataView::find()
            ->filterWhere([$key, $param['field'], $param['key']])
            ->andWhere(['!=','status',88])
            ->andWhere(['client_id'=> Yii::$app->user->identity->getId()])
            ->count();

            $data['mlv'] = UserDataView::find()
            ->filterWhere([$key, $param['field'], $param['key']])
            ->andWhere(['!=','status',88])
            ->andWhere(['client_id'=> Yii::$app->user->identity->getId()])
            ->asArray()
            ->orderBy(['datetime'=>SORT_DESC])
            ->all();
            
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

  public function getUserlist()
  {
        try{
              return User::find()
              ->where(['status'=> 0])
              //->andWhere(['<>','role',1])
              ->asArray()
              ->orderBy(['id'=>SORT_ASC])
              ->all();

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
    }catch(\yii\base\Exception $e){
      Yii::error($e->getMessage(),'writelog');
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

  public function getDatasToCallback($data)
  {
      try{
          return DatesHub::find()
              ->where(['astatus'=> 0,'mid'=> $data['id']])
              ->andWhere(['>=','daterent',$this->dpickerblock()])
              ->asArray()
              ->orderBy(['daterent'=>SORT_ASC])
              ->all();

      }catch(Exception $e){
          return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
      }
  }

  public function dpickerblock()
  {
    $retVal=null;
    $time = '17:30';
      if(date("w") == 5 && date("H:i") > $time){ //???????? ?????????????? ?? ?????????? 17-30
        $retVal = date("Y-m-d", strtotime("+ 4 day"));
      }elseif(date("w") == 5 && date("H:i") < $time){
        $retVal = date("Y-m-d", strtotime("+ 1 day"));
      }
      if(date("w") == 6){ // ???????? ??????????????
        $retVal = date("Y-m-d", strtotime("+ 3 day"));
      }
      if(date("w") == 0){ // ???????? ??????????????????????
        $retVal = date("Y-m-d", strtotime("+ 2 day"));
      }

      if((date("w") == 1 || date("w") == 2 || date("w") == 3 || date("w") == 4) && (date("H:i") > $time)){
        $retVal = date("Y-m-d", strtotime("+ 2 day"));
      }elseif((date("w") == 1 || date("w") == 2 || date("w") == 3 || date("w") == 4) && (date("H:i") < $time)){
          $retVal = date("Y-m-d", strtotime("+ 1 day"));
      }
      
    return $retVal;
  }

  public function holidays()
  {
    $hd = Holidays::find()
    ->select('days')
    ->where(['status'=> 0])
    ->asArray()
    ->orderBy(['id'=>SORT_DESC])
    ->one();
    if(!empty($hd['days'])){
        return explode(',', $hd['days']);
    }else{
        return [];
    }
    
  }

  public function holidaysf()
  {

      $dates = [];
      $hd = Holidays::find()
      ->select('days')
      ->where(['status'=> 0])
      ->asArray()
      ->orderBy(['id'=>SORT_DESC])
      ->one();
      if(!empty($hd['days'])){
          $data = explode(',', $hd['days']);
          if(!empty($data)){
            foreach ($data as $itm) {
              array_push($dates,date('Y-m-d',strtotime(str_replace('/', '-', $itm))));
          }
          return $dates;
          }else{ 
            return $dates;
          }

      }else{
          return $dates;
      }
  }

  public function getHolidayDates($param)
  {
    $data = [];
    try{
      $data['count'] = Holidays::find()->where(['status'=> 0])->count();
      
      $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);

      $data['hdlist'] = Holidays::find()
      ->where(['status'=> 0])
      ->offset($pagination->offset)
      ->limit($pagination->limit)
      ->asArray()
      ->orderBy(['id'=>SORT_DESC])
      ->all();

      return $data;
    }catch(Exception $e){
        return $e->errorInfo;
        //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
    }
  }

  public function setsave($data)
  {
    try{
      unset($data['token']);
      $hd = new Holidays();
      $hd->attributes = $data;
      if($hd->validate()){
        if($hd->save(false)){
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }

    }catch(Exception $e){
      return $e;
    }
  }

  public function delHolidayDates($data)
  {
    try{
      $hd = Holidays::findOne($data['id']);
      $hd->status = 1;
      if($hd->save()){ return true; }else{ return false; }
    }catch(Exception $e){
      return $e;
    }
  }

  public function textupdater($dates)
  {
    $str = '';
    if(!empty($dates)){
        foreach ($dates as $item) {
            $str .= date('d/m/Y',strtotime($item['daterent'])).',';
        }
    $str = substr($str,0,-1);  
    }
    return $str;
  }

  public function callback($data)
  {
    $daterent = '';
    try{
      
      $in = explode(',', $data['daterent']);
      if(!empty($in)){
        for($i = 0; $i < count($in); $i++){
            $daterent .= "'".$in[$i]."',";
        }
        $daterent = substr($daterent, 0,-1);
      }
      
      $cmd1 = Yii::$app->db->createCommand("DELETE dates_hub WHERE mid = ".$data['id']." AND daterent IN(".$daterent.")");
      $cmd1->execute();
      
      $count = DatesHub::find()->where(['mid'=>$data['id']])->count();
      $dates = DatesHub::find()->select(['daterent'])->where(['mid'=>$data['id']])->all();
      $strdaterent = $this->textupdater($dates);


      $cmd2 = Yii::$app->db->createCommand("UPDATE main_hub SET cday = {$count},dates = '{$strdaterent}' WHERE id = ".$data['id']."");
      $cmd2->execute(); 
      
    }catch(Exception $ex){
      return $ex;
    }

  }

}
