<?php

namespace budyaga\users\models;

use Yii;

/**
 * This is the model class for table "user_password_reset_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 *
 * @property User $user
 */
class UserPasswordResetToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_password_reset_token}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['token'], 'string', 'max' => 255]
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
