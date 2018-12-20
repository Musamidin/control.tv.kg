<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dailyCountSimView".
 *
 * @property string $txt
 * @property int $id
 * @property int $mid
 * @property string $datetime
 * @property string $daterent
 * @property int $chid
 * @property int $client_id
 * @property int $status
 */
class DailyCountSimView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dailyCountSimView';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['txt'], 'string'],
            [['id', 'mid', 'datetime', 'chid', 'status'], 'required'],
            [['id', 'mid', 'chid', 'client_id', 'status'], 'integer'],
            [['datetime', 'daterent'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'txt' => 'Txt',
            'id' => 'ID',
            'mid' => 'Mid',
            'datetime' => 'Datetime',
            'daterent' => 'Daterent',
            'chid' => 'Chid',
            'client_id' => 'Client ID',
            'status' => 'Status',
        ];
    }
}
