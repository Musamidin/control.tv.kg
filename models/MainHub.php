<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "main_hub".
 *
 * @property int $id
 * @property string $datetime
 * @property string $channels
 * @property string $text
 * @property string $dates
 * @property int $client_id
 * @property int $status
 *
 * @property DatesHub[] $datesHubs
 */
class MainHub extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'main_hub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['channels', 'text', 'dates'], 'string'],
            [['client_id', 'status'], 'integer'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['channels','test','dates']; 
        return $scenarios; 
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'channels' => 'Channels',
            'text' => 'Text',
            'dates' => 'Dates',
            'client_id' => 'Client ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDatesHubs()
    {
        return $this->hasMany(DatesHub::className(), ['mid' => 'id']);
    }
}
