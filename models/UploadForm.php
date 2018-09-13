<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $fileref;

    public function rules()
    {
        return [
            [['fileref'], 'file', 'skipOnEmpty' => false, 
                          'extensions' => 'xlsx, xls',
                          'checkExtensionByMimeType' => false, 
                          ],
            // [['fileref'], 'file', 'mimeTypes' => 'application/x-ms-excel'],
            // [['fileref'], 'file', 'mimeTypes' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ];
    }
    
    public function upload($fname)
    {
        if ($this->validate()) {
            $this->fileref->saveAs(\Yii::$app->basePath."\web\data\\" . $fname . '.' . $this->fileref->extension);
            return true;
        } else {
            return false;
        }
    }
}