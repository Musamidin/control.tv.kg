<?php

namespace app\components;

use Yii;
use yii\base\Component;

use app\models\MainHub;
use app\models\DatesHub;

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
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
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

}
