<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_agents".
 *
 * @property int $id
 * @property string $phoneAsLogin
 * @property string $password
 * @property string $userName
 * @property string $email
 * @property string $datetime
 * @property int $account
 * @property int $smsBalance
 * @property string $Balance
 * @property int $Code
 * @property int $Active
 * @property string $topup_datetime
 * @property int $notificationStatusSMS
 * @property int $userType
 * @property string $publicKey
 * @property int $smsNotify
 * @property int $smsNotifyFlag
 * @property int $last_dispatch_id
 * @property int $tariff_id
 * @property string $auth_key
 * @property string $access_token
 *
 * @property DISPATCH[] $dISPATCHes
 * @property MainDump[] $mainDumps
 * @property NUMBERLISTS[] $nUMBERLISTSs
 * @property SenderNames[] $senderNames
 * @property TARIFFS $tariff
 * @property DISPATCH $lastDispatch
 */
class UserAgents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_agents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phoneAsLogin', 'password', 'datetime', 'account', 'Code', 'Active', 'userType'], 'required'],
            [['phoneAsLogin', 'Balance'], 'number'],
            [['password', 'userName', 'email', 'publicKey', 'auth_key', 'access_token'], 'string'],
            [['datetime', 'topup_datetime'], 'safe'],
            [['account', 'smsBalance', 'Code', 'Active', 'notificationStatusSMS', 'userType', 'smsNotify', 'smsNotifyFlag', 'last_dispatch_id', 'tariff_id'], 'integer'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => TARIFFS::className(), 'targetAttribute' => ['tariff_id' => 'TARIFF_ID']],
            [['last_dispatch_id'], 'exist', 'skipOnError' => true, 'targetClass' => DISPATCH::className(), 'targetAttribute' => ['last_dispatch_id' => 'DISPATCH_ID']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phoneAsLogin' => 'Phone As Login',
            'password' => 'Password',
            'userName' => 'User Name',
            'email' => 'Email',
            'datetime' => 'Datetime',
            'account' => 'Account',
            'smsBalance' => 'Sms Balance',
            'Balance' => 'Balance',
            'Code' => 'Code',
            'Active' => 'Active',
            'topup_datetime' => 'Topup Datetime',
            'notificationStatusSMS' => 'Notification Status Sms',
            'userType' => 'User Type',
            'publicKey' => 'Public Key',
            'smsNotify' => 'Sms Notify',
            'smsNotifyFlag' => 'Sms Notify Flag',
            'last_dispatch_id' => 'Last Dispatch ID',
            'tariff_id' => 'Tariff ID',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDISPATCHes()
    {
        return $this->hasMany(DISPATCH::className(), ['USER_AGENT_ID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainDumps()
    {
        return $this->hasMany(MainDump::className(), ['agentsId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNUMBERLISTSs()
    {
        return $this->hasMany(NUMBERLISTS::className(), ['USER_AGENT_ID' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSenderNames()
    {
        return $this->hasMany(SenderNames::className(), ['agentsId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(TARIFFS::className(), ['TARIFF_ID' => 'tariff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastDispatch()
    {
        return $this->hasOne(DISPATCH::className(), ['DISPATCH_ID' => 'last_dispatch_id']);
    }
}
