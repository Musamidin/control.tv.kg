<?php

namespace app\components;

use Yii;
use yii\httpclient\XmlParser;
use yii\db\BaseActiveRecord;
use yii\db\Exception;

class Modules extends XmlParser
{

  public function xmlToArray($xml)
  {
      return $this->convertXmlToArray($xml);
  }
  
}