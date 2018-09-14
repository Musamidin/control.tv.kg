<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dates_hub".
 *
 * @property int $id
 * @property int $mid
 * @property string $daterent
 * @property int $astatus
 * @property string $comment
 *
 * @property MainHub $m
 */
class DatesHub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dates_hub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mid'], 'required'],
            [['mid','astatus'], 'integer'],
            [['comment'], 'string'],
            [['daterent'], 'safe'],
            [['mid'], 'exist', 'skipOnError' => true, 'targetClass' => MainHub::className(), 'targetAttribute' => ['mid' => 'id']],
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
            'daterent' => 'Dates',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getM()
    {
        return $this->hasOne(MainHub::className(), ['id' => 'mid']);
    }
}
