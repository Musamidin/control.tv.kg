<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exportView".
 *
 * @property int $id
 * @property int $mid
 * @property string $daterent
 * @property int $astatus
 * @property string $comment
 * @property int $Expr1
 * @property string $datetime
 * @property string $channels
 * @property string $text
 * @property string $dates
 * @property int $client_id
 * @property int $status
 * @property string $description
 * @property string $last_up_date
 */
class ExportView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exportView';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'mid', 'Expr1', 'datetime', 'status'], 'required'],
            [['id', 'mid', 'astatus', 'Expr1', 'client_id', 'status'], 'integer'],
            [['daterent', 'datetime', 'last_up_date'], 'safe'],
            [['comment', 'channels', 'text', 'dates', 'description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mid' => 'Mid',
            'daterent' => 'Daterent',
            'astatus' => 'Astatus',
            'comment' => 'Comment',
            'Expr1' => 'Expr1',
            'datetime' => 'Datetime',
            'channels' => 'Channels',
            'text' => 'Text',
            'dates' => 'Dates',
            'client_id' => 'Client ID',
            'status' => 'Status',
            'description' => 'Description',
            'last_up_date' => 'Last Up Date',
        ];
    }
}
