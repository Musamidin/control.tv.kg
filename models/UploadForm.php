<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $userfile;

    public function rules()
    {
        return [
            [['userfile'], 'file', 'skipOnEmpty' => false, 
                          'extensions' => 'xlsx, xls',
                          'checkExtensionByMimeType' => false, 
                          ],
            // [['userfile'], 'file', 'mimeTypes' => 'application/x-ms-excel'],
            // [['userfile'], 'file', 'mimeTypes' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->userfile->saveAs(\Yii::$app->basePath."\web\data\\" .$this->userfile->baseName . '.' . $this->userfile->extension);
            return true;
        } else {
            return false;
        }
    }
}