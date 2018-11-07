<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "holidays".
 *
 * @property int $id
 * @property string $days
 * @property string $datetime
 * @property int $status
 */
class Holidays extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'holidays';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['days'], 'string'],
            [['datetime'], 'safe'],
            [['status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'days' => 'Days',
            'datetime' => 'Datetime',
            'status' => 'Status',
        ];
    }
}
