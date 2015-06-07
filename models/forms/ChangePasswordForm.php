<?php
namespace budyaga\users\models\forms;

use budyaga\users\models;
use yii\base\Model;
use Yii;

class ChangePasswordForm extends Model
{
    public $old_password;
    public $new_password;
    public $new_password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password', 'new_password_repeat'], 'filter', 'filter' => 'trim'],
            ['old_password', 'required', 'on' => 'requiredOldPassword'],
            [['new_password', 'new_password_repeat'], 'required'],
            [['old_password', 'new_password', 'new_password_repeat'], 'string', 'min' => 6],
            ['new_password_repeat', 'compare', 'compareAttribute' => 'new_password'],
            ['old_password', 'validatePassword', 'on' => 'requiredOldPassword']
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('users', 'OLD_PASSWORD'),
            'new_password' => Yii::t('users', 'NEW_PASSWORD'),
            'new_password_repeat' => Yii::t('users', 'NEW_PASSWORD_REPEAT'),
        ];
    }

    public function validatePassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if (!$this->hasErrors() && $user->password_hash != '') {
            if (!$user->validatePassword($this->old_password)) {
                $this->addError($attribute, Yii::t('users', 'OLD_PASSWORD_IS_WRONG'));
            }
        }
    }

}
