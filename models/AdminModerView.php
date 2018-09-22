<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "adminModerView".
 *
 * @property int $id
 * @property string $datetime
 * @property string $chname
 * @property string $text
 * @property string $dates
 * @property string $order
 * @property int $status
 * @property int $state
 * @property string $description
 */
class AdminModerView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adminModerView';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime', 'status', 'state'], 'required'],
            [['datetime'], 'safe'],
            [['chname', 'text', 'dates', 'order', 'description'], 'string'],
            [['status', 'state'], 'integer'],
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
            'chname' => 'Chname',
            'text' => 'Text',
            'dates' => 'Dates',
            'order' => 'Order',
            'status' => 'Status',
            'state' => 'State',
            'description' => 'Description',
        ];
    }
}
