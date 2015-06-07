<?php
namespace budyaga\users\models\forms;

use budyaga\users\models;
use yii\base\Model;
use Yii;

class ChangeEmailForm extends Model
{
    public $new_email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['new_email', 'filter', 'filter' => 'trim'],
            ['new_email', 'required'],
            ['new_email', 'email'],
            ['new_email', 'noCompare']
        ];
    }

    public function attributeLabels()
    {
        return [
            'new_email' => Yii::t('users', 'NEW_EMAIL'),
        ];
    }

    public function noCompare($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if ($user->email == $this->new_email) {
            $this->addError($attribute, Yii::t('users', 'THIS_EMAIL_ALREADY_CONFIRM'));
        }
    }

}
