<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\data\Pagination;

use app\models\MainHub;
use app\models\DatesHub;
use app\models\ExportView;
use app\models\ClientView;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use app\models\MyReadFilter;

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
                    unset($row[$key]);
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
                    }elseif($key == 'E'){
                        unset($row[$key]);
                        $row['state'] = $value;
                    }
                }
                $rows[] = $row;
            }

            return $this->save($rows);
                    
        }catch(Exception $e){
            return $e;
        }
    }
    public function save($data)
    {
        try{
            $t = '';
            foreach($data as $itm) {

                // echo '<pre>';
                // echo $itm['channels'].'|'.$itm['text'].'|'.$itm['dates'].'<br/>';
                // echo '</pre>';
                $t .= $itm['chid'].'|'.$itm['text'].'|'.$itm['dates'];
                $mh = new MainHub();
                $mh->phone = $itm['phone'];
                $mh->chid = $itm['chid'];
                $mh->text = $itm['text'];
                $mh->dates = $itm['dates'];
                $mh->state = $itm['state'];
                $mh->client_id = Yii::$app->user->identity->getId();
                $mh->save();
                $this->arr_map($itm['dates'],$mh->id);
            }
            return $t;
         }catch(Exception $e){
            return $e;
         }   
    }

    public function arr_map($data,$id)
    {
      if(preg_match("/[\-]+/",$data)){

          $arr = preg_split("/[\-]+/",$data);
          $start = new DateTime($arr[0]);
          $interval = new DateInterval('P1D');
          $end = new DateTime($arr[1]);
          $end->add(new DateInterval('P1D'));
          $period = new DatePeriod($start, $interval, $end);

          foreach ($period as $date) {
              $dh = new DatesHub();
              $dh->daterent = $date->format('Y-m-d');
              //date('Y-m-d',strtotime(str_replace('/', '-', $arr[$i])));
              $dh->astatus = 0;
              $dh->mid = $id;
              $dh->save();
          }

      }elseif(preg_match("/[\,]+/",$data)){

          $arr = preg_split("/[\,]+/",$data);
          for($i = 0; $i < count($arr); $i++){
                  $dh = new DatesHub();
                  $dh->daterent = date('Y-m-d',strtotime(str_replace('/', '-', $arr[$i])));
                  $dh->astatus = 0;
                  $dh->mid = $id;
                  $dh->save();
                  //echo $arr[$i].'<br/>';
          }
      }

      




    }

   public function getData($param)
   {
        $data = [];
        try{
          if($param['sts'] == 0){
              $data['count'] = MainHub::find()->filterWhere(['=','status',$param['sts']])->count();
              $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
              $data['mlv'] = MainHub::find()
              ->filterWhere(['=','status',0])
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->asArray()
              ->orderBy(['last_up_date'=>SORT_DESC])
              ->all();
          }else{
            $data['count'] = MainHub::find()->filterWhere(['status'=> $param['sts']])->count();
            $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
            $data['mlv'] = MainHub::find()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->filterWhere(['status'=> $param['sts']])
            ->asArray()
            ->orderBy(['last_up_date'=>SORT_DESC])
            ->all();
          }
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
          if($param['sts'] == 0){
              $data['count'] = clientView::find()->filterWhere(['=','status',$param['sts']])->count();
              $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
              $data['mlv'] = clientView::find()
              ->filterWhere(['=','status',0])
              ->offset($pagination->offset)
              ->limit($pagination->limit)
              ->asArray()
              ->orderBy(['datetime'=>SORT_DESC])
              ->all();
          }else{
            $data['count'] = clientView::find()->filterWhere(['status'=> $param['sts']])->count();
            $pagination = new Pagination(['defaultPageSize'=>$param['shpcount'],'totalCount'=> $data['count']]);
            $data['mlv'] = clientView::find()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->filterWhere(['status'=> $param['sts']])
            ->asArray()
            ->orderBy(['datetime'=>SORT_DESC])
            ->all();
          }
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

            $data['count'] = ExportView::find()->where(['status'=> 1,'channels'=> $par['channel']])->count();
              $data['mlv'] = ExportView::find()
              ->where(['status'=> 1,'channels'=> $par['channel']])
              ->asArray()
              ->orderBy(['last_up_date'=>SORT_DESC])
              ->all();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
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


}
