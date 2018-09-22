<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "channels".
 *
 * @property int $id
 * @property string $channel_name
 * @property int $status
 * @property string $datetime
 * @property string $tk_price
 * @property string $owner_price
 * @property string $agency_price
 * @property string $display_quantity
 * @property string $coverage
 * @property string $email
 * @property MainHub[] $mainHubs
 */
class Channels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'channels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel_name', 'display_quantity', 'coverage','email'], 'string'],
            [['status'], 'integer'],
            [['datetime'], 'safe'],
            [['tk_price', 'owner_price', 'agency_price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel_name' => 'Channel Name',
            'status' => 'Status',
            'datetime' => 'Datetime',
            'tk_price' => 'Tk Price',
            'owner_price' => 'Owner Price',
            'agency_price' => 'Agency Price',
            'display_quantity' => 'Display Quantity',
            'coverage' => 'Coverage',
            'email' => 'E-mail',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainHubs()
    {
        return $this->hasMany(MainHub::className(), ['chid' => 'id']);
    }
}
