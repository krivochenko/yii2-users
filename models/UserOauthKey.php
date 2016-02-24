<?php

namespace budyaga\users\models;

use Yii;

/**
 * This is the model class for table "user_oauth_key".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $provider_id
 * @property integer $provider_user_id
 *
 * @property User $user
 */
class UserOauthKey extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_oauth_key}}';
    }

    /**
     * @inheritdoc
     */
    public static function getAvailableClients()
    {
        return [
            'vkontakte' => 1,
            'google' => 2,
            'facebook' => 3,
            'github' => 4,
            'linkedin' => 5,
            'live' => 6,
            'yandex' => 7,
            'twitter' => 8
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'provider_id'], 'integer'],
            [['user_id', 'provider_id', 'provider_user_id'], 'required']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
