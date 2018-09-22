<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientsDataView".
 *
 * @property int $id
 * @property string $datetime
 * @property string $phone
 * @property string $chname
 * @property string $text
 * @property string $dates
 * @property int $client_id
 * @property int $status
 * @property int $state
 * @property string $description
 */
class ClientsDataView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientsDataView';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime', 'status', 'state'], 'required'],
            [['datetime'], 'safe'],
            [['phone'], 'number'],
            [['chname', 'text', 'dates', 'description'], 'string'],
            [['client_id', 'status', 'state'], 'integer'],
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
            'chname' => 'Chname',
            'text' => 'Text',
            'dates' => 'Dates',
            'client_id' => 'Client ID',
            'status' => 'Status',
            'state' => 'State',
            'description' => 'Description',
        ];
    }
}
