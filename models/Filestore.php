<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "filestore".
 *
 * @property int $id
 * @property string $fileref
 * @property string $datetime
 */
class Filestore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filestore';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fileref'], 'string'],
            [['datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fileref' => 'Fileref',
            'datetime' => 'Datetime',
        ];
    }
}
