<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "userDataView".
 *
 * @property string $datetime
 * @property string $chname
 * @property string $text
 * @property string $dates
 * @property int $simcount
 * @property int $cday
 * @property string $summ
 * @property int $status
 * @property string $description
 * @property int $chid
 * @property int $client_id
 */
class UserDataView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userDataView';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime', 'status', 'chid'], 'required'],
            [['datetime'], 'safe'],
            [['chname', 'text', 'dates', 'description'], 'string'],
            [['simcount', 'cday', 'status', 'chid', 'client_id'], 'integer'],
            [['summ'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'datetime' => 'Datetime',
            'chname' => 'Chname',
            'text' => 'Text',
            'dates' => 'Dates',
            'simcount' => 'Simcount',
            'cday' => 'Cday',
            'summ' => 'Summ',
            'status' => 'Status',
            'description' => 'Description',
            'chid' => 'Chid',
            'client_id' => 'Client ID',
        ];
    }
}
