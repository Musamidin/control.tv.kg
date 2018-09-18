<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientView".
 *
 * @property int $mhid
 * @property string $phone
 * @property string $chname
 * @property string $text
 * @property string $daterent
 * @property string $description
 * @property int $status
 * @property int $state
 * @property string $datetime
 * @property int $chid
 */
class ClientView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientView';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mhid', 'status', 'state', 'datetime', 'chid'], 'required'],
            [['mhid', 'status', 'state', 'chid'], 'integer'],
            [['phone'], 'number'],
            [['chname', 'text', 'description'], 'string'],
            [['daterent', 'datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mhid' => 'Mhid',
            'phone' => 'Phone',
            'chname' => 'Chname',
            'text' => 'Text',
            'daterent' => 'Daterent',
            'description' => 'Description',
            'status' => 'Status',
            'state' => 'State',
            'datetime' => 'Datetime',
            'chid' => 'Chid',
        ];
    }
}
