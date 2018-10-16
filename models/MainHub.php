<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "main_hub".
 *
 * @property int $id
 * @property string $datetime
 * @property string $phone
 * @property string $chid
 * @property string $text
 * @property string $dates
 * @property int $cday
 * @property int $client_id
 * @property int $status
 * @property int $state 
 * @property string $description
 * @property string $last_up_date
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
            [['datetime','last_up_date'], 'safe'],
            [['text', 'dates','description'], 'string'],
            [['state','phone','chid','client_id', 'status','cday'], 'integer'],
            //[[ 'phone'], 'match', 'pattern' => '/^[0-9]{9}$/'],
            [['text', 'dates','chid'],'required'],
        ];
    }

    // public function scenarios()
    // {
    //     $scenarios = parent::scenarios();
    //     $scenarios['create'] = ['phone','chid','text','dates']; 
    //     return $scenarios; 
    // }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            //'phone' => 'Мобильный номер',
            'chid' => 'chid',
            'text' => 'Text',
            'dates' => 'Dates',
            'cday' => 'count days',
            'client_id' => 'Client ID',
            'status' => 'Status',
            'state' => 'State',
            'description' => 'Description',
            'last_up_date' => 'Last modify',
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
