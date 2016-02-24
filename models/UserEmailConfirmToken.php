<?php

namespace budyaga\users\models;

use Yii;

/**
 * This is the model class for table "user_email_confirm_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $old_email
 * @property string $old_email_token
 * @property integer $old_email_confirm
 * @property string $new_email
 * @property string $new_email_token
 * @property integer $new_email_confirm
 *
 * @property User $user
 */
class UserEmailConfirmToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_email_confirm_token}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'new_email', 'new_email_token'], 'required'],
            [['user_id', 'old_email_confirm', 'new_email_confirm'], 'integer'],
            [['old_email', 'old_email_token', 'new_email', 'new_email_token'], 'string', 'max' => 255],
            [['old_email', 'old_email_token'], 'default', 'value' => ''],
            ['old_email_confirm', 'default', 'value' => 1]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function findToken($token)
    {
        return static::find()->where('old_email_token=:token OR new_email_token=:token', [':token' => $token])->one();
    }

    public function confirm($token)
    {
        if ($this->old_email_token == $token) {
            $this->old_email_confirm = 1;
            $message = Yii::t('users', 'TO_COMPLETE_THE_CHANGE_CHECK_EMAIL', ['email' => $this->new_email]);
        } else {
            $this->new_email_confirm = 1;
            $message = Yii::t('users', 'TO_COMPLETE_THE_CHANGE_CHECK_EMAIL', ['email' => $this->old_email]);
        }

        if ($this->old_email_confirm && $this->new_email_confirm) {
            $message = Yii::t('users', 'EMAIL_WAS_CONFIRMED');
            $user = $this->user;
            $user->email = $this->new_email;
            if ($user->status == User::STATUS_NEW) {
                $user->status = User::STATUS_ACTIVE;
            }
            if ($user->save()) {
                $this->delete();
            }
        } else {
            $this->save();
        }

        return $message;
    }
}
