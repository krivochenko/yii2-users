<?php
namespace budyaga\users\models\forms;

use yii\base\Model;
use budyaga\users\models\User;
use Yii;

class RetryConfirmEmailForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\budyaga\users\models\UserEmailConfirmToken',
                'targetAttribute' => 'new_email',
                'filter' => ['old_email' => ''],
                'message' => Yii::t('users', 'USER_WITH_SUCH_EMAIL_DO_NOT_EXISTS')
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('users', 'EMAIL'),
        ];
    }

    public function getUser()
    {
        return User::findOne(['email' => $this->email]);
    }
}