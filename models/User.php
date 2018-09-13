<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_agents".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $name
 * @property string $role
 * @property string $datetime
 * @property integer $status
 * @property integer $auth_key
 * @property integer $access_token
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password', 'datetime', 'name', 'role', 'status'], 'required'],
            [['login'], 'number'],
            [['password', 'name', 'auth_key', 'access_token'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'login' => 'Login',
            'password' => 'Password',
            'name' => 'User Name',
            'role' => 'Role',
            'datetime' => 'Datetime',
            'auth_key' => 'Access Token',
            'access_token' => 'Access Token',
        ];
    }

}
