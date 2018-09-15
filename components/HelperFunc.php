<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\data\Pagination;

use app\models\MainHub;
use app\models\DatesHub;
use app\models\ExportView;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
    public function savedb($fileName)
    {
        try{
                    
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
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
                $t .= $itm['channels'].'|'.$itm['text'].'|'.$itm['dates'];
                $mh = new MainHub();
                $mh->channels = $itm['channels'];
                $mh->text = $itm['text'];
                $mh->dates = $itm['dates'];
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
        $arr = explode(',', $data);
        for($i = 0; $i < count($arr); $i++){
                $dh = new DatesHub();
                $dh->daterent = date('Y-m-d',strtotime(str_replace('/', '-', $arr[$i])));
                $dh->astatus = 0;
                $dh->mid = $id;
                $dh->save();
                //echo $arr[$i].'<br/>';
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

   public function getDatas($param)
   {
        $data = [];
        try{

            $data['count'] = ExportView::find()->filterWhere(['status'=> 1])->count();
              $data['mlv'] = ExportView::find()
              ->filterWhere(['=','status',1])
              ->asArray()
              ->orderBy(['last_up_date'=>SORT_DESC])
              ->all();

          return $data;
        }catch(Exception $e){
            return $e->errorInfo;
          //echo json_encode(['status'=>1, 'msg'=>$e->errorInfo]);
        }
   }

}
