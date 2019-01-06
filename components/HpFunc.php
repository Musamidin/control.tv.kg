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
class HpFunc extends Component
{
    public function save($k,$v,$data)
    {
      try{
            $mh = new MainHub();
            //$mh->attributes = $data;
            // if($mh->validate())
            // {
              if(!empty($v) && count($v) > 0)
              {
                $mh->chid = $k;
                $mh->text = $data['text'];
                $mh->dates = $this->upDatesStr($v);
                $mh->state = 0;
                $mh->client_id = Yii::$app->user->identity->getId();
                $mh->cday = count($v);
                if($mh->save())
                {
                  foreach($v as $itm)
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
                          'message'=> 'Запись успешно добавлен'
                          ];
                }else{
                  return [
                          'id'=> 0,
                          'coutDays' => 0,
                          'status' => 1,
                          'message'=>'Произашло ошибка при сохранения в БД. Обратитесь к администратору!'
                          ];
                }
              }else{
                return [
                          'id'=> 0,
                          'coutDays' => 0,
                          'status' => 2,
                          'message'=>'Даты проката неправильные'
                          ];
              }
            // }else{
            //   Yii::error($mh->errors,'writelog');
            //   return $mh->errors;
            // }
          }catch(\yii\base\Exception $ex){
            Yii::error($ex->getMessage(),'writelog');
            return $ex;
          }
    }
    
    public function calculate($data)
    {
        $price = [];
        $tv = $this->getTvList();
        foreach($tv as $item){
            $price[$item['id']] = $item['price'];
        }
        $retSum = []; 
        foreach($data['valid']['countDays'] as $k => $v)
        {
            $retSum[$k] = (Intval($data['countSim']) * Intval($data['valid']['countDays'][$k]) * floatval($price[$k]));
            $retSum['totalSum'] += (Intval($data['countSim']) * Intval($data['valid']['countDays'][$k]) * floatval($price[$k]));
        }
        return $retSum;
    }
    public function getCountSim($str)
    {
        $patt = [" ","\t","\n","\r","\0","\x0B"];
        return mb_strlen(str_replace($patt,'',$str));
    }

    public function dateValidation($dates)
    {
        $ds = [];
        if(!empty($dates)){
            foreach($dates as $k => $v){
                if(!empty($v)){
                    if(!empty($this->validateDates($v))){
                        $ds[$k] = $this->validateDates($v);
                        //$ds['countDays'][$k] = count($this->validateDates($v));
                    }
                }
            }
        }
        return $ds;
    }

    public function checkDates($dates)
    {
      $hdays = [];
      $date = date('Y-m-d',strtotime( $dates));
      if($date >= $this->dpickerblock())
      {
        return true;
      }else{
        return false;
      }
    }

    /* Функция validateDates() принимает строкувую дату 
    формате (YYYY-mm-dd) Возвращает массив даты формате YYYY-mm-dd или пустой массив [] 
    */
  public function validateDates($data)
  {
    $dates = [];
    if(!empty($data)){
        try{
            $arr = explode(',', $data);
            foreach($arr as $itm)
            {
              if($this->checkDates(trim($itm)))
              {
                $strtime = strtotime($itm);
                if($strtime != false){
                  array_push($dates, date('Y-m-d',$strtime));
                }
              }
            }
            return $dates;
        }catch(\yii\base\Exception $e){
            Yii::error($e->getMessage(),'writelog');
            $e->getMessage();
        }
    }
    return $dates;
  }

  public function upDatesStr($data)
  {
    $str = '';
        if(count($data) > 0){
            foreach ($data as $itm) {
                    $str .= date('d/m/Y',strtotime($itm)).',';
            }
        }
        return substr($str, 0,-1);
  }

  public function daterent($data,$bid,$chid)
  {
    if(count($data) > 0){
        foreach ($data as $itm) {
            try{
                $dtr = new Daterent();
                $dtr->basketId = $bid;
                $dtr->chid = $chid;
                $dtr->daterent = date('Y-m-d',strtotime($itm));
                $dtr->save();
            }catch(\yii\base\Exception $e){
                Yii::error($e->getMessage(),'writelog');
            }
        }
    }      
  }

  public function dpickerblock()
  {
    $retVal=null;
    $time = '17:30';
      if(date("w") == 5 && date("H:i") > $time){ //Если Пятница и время 17-30
        $retVal = date("Y-m-d", strtotime("+ 4 day"));
      }elseif(date("w") == 5 && date("H:i") < $time){
        $retVal = date("Y-m-d", strtotime("+ 1 day"));
      }
      if(date("w") == 6){ // Если Суббота
        $retVal = date("Y-m-d", strtotime("+ 3 day"));
      }
      if(date("w") == 0){ // Если Воскресения
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


}