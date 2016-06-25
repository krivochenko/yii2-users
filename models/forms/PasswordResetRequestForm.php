<?php
namespace budyaga\users\models\forms;

use budyaga\users\models\User;
use budyaga\users\models\UserPasswordResetToken;
use yii\base\Model;
use Yii;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\budyaga\users\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t('users', 'USER_WITH_SUCH_EMAIL_DO_NOT_EXISTS')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $user = $this->user;
        if (!$user) {
            return false;
        }

        return \Yii::$app->mailer->compose(['html' => '@budyaga/users/mail/passwordResetToken-html', 'text' => '@budyaga/users/mail/passwordResetToken-text'], ['user' => $user, 'token' => $this->token])
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . \Yii::$app->name)
            ->send();
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne([
                'status' => User::STATUS_ACTIVE,
                'email' => $this->email,
            ]);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('users', 'EMAIL'),
        ];
    }

    public function getToken()
    {
        $user = $this->user;

        $token = UserPasswordResetToken::findOne([
            'user_id' => $user->id
        ]);

        if ($token == null) {
            $token = new UserPasswordResetToken;
            $token->user_id = $user->id;
            $token->token = $user->generatePasswordResetToken();
            $token->save();
        }

        return $token;
    }
}
