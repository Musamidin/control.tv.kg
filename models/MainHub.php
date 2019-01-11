<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "main_hub".
 *
 * @property int $id
 * @property string $datetime
 * @property string $phone
 * @property int $chid
 * @property string $text
 * @property string $licdoc
 * @property string $dates
 * @property int $cday
 * @property int $client_id
 * @property int $status
 * @property int $state
 * @property string $description
 * @property string $last_up_date
 *
 * @property DatesHub[] $datesHubs
 * @property Channels $ch
 */
class MainHub extends \yii\db\ActiveRecord
{
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
            [['datetime', 'last_up_date'], 'safe'],
            [['phone'], 'number'],
            [['chid'], 'required'],
            [['chid', 'cday', 'client_id', 'status', 'state'], 'integer'],
            [['text', 'licdoc', 'dates', 'description'], 'string'],
            [['chid'], 'exist', 'skipOnError' => true, 'targetClass' => Channels::className(), 'targetAttribute' => ['chid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'phone' => 'Phone',
            'chid' => 'Chid',
            'text' => 'Text',
            'licdoc' => 'Licdoc',
            'dates' => 'Dates',
            'cday' => 'Cday',
            'client_id' => 'Client ID',
            'status' => 'Status',
            'state' => 'State',
            'description' => 'Description',
            'last_up_date' => 'Last Up Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDatesHubs()
    {
        return $this->hasMany(DatesHub::className(), ['mid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCh()
    {
        return $this->hasOne(Channels::className(), ['id' => 'chid']);
    }
}
