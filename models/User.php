<?php

namespace budyaga\users\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $photo
 * @property string $auth_key
 * @property integer $status
 * @property integer $sex
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
*/
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NEW = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_BLOCKED = 3;

    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    public function getDefaultPhoto()
    {
        return ($this->sex == self::SEX_MALE) ? 'male' : 'female';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function getSexArray()
    {
        return [
            self::SEX_MALE => Yii::t('users', 'SEX_MALE'),
            self::SEX_FEMALE => Yii::t('users', 'SEX_FEMALE'),
        ];
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_NEW => Yii::t('users', 'STATUS_NEW'),
            self::STATUS_ACTIVE => Yii::t('users', 'STATUS_ACTIVE'),
            self::STATUS_BLOCKED => Yii::t('users', 'STATUS_BLOCKED'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required', 'except' => ['oauth']],
            [['username'], 'required'],
            [['username', 'photo', 'email', 'auth_key', 'password_hash'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'status', 'sex'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_ACTIVE, self::STATUS_BLOCKED]],
            ['sex', 'in', 'range' => [self::SEX_MALE, self::SEX_FEMALE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('users', 'USERNAME'),
            'photo' => Yii::t('users', 'PHOTO'),
            'email' => Yii::t('users', 'EMAIL'),
            'sex' => Yii::t('users', 'SEX'),
            'status' => Yii::t('users', 'STATUS'),
            'created_at' => Yii::t('users', 'CREATED_AT'),
            'updated_at' => Yii::t('users', 'UPDATED_AT'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmailOrUserName($email)
    {
        return static::find()->where(['and', ['status' => self::STATUS_ACTIVE], ['or', ['email' => $email], ['username' => $email]]])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return ($this->password_hash!='' && Yii::$app->security->validatePassword($password, $this->password_hash));
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        return Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getPasswordResetToken()
    {
        return $this->hasOne(UserPasswordResetToken::className(), ['user_id' => 'id']);
    }

    public function createEmailConfirmToken($needConfirmOldEmail = false)
    {
        $token = new UserEmailConfirmToken;
        $token->user_id = $this->id;
        $token->new_email = $this->email;
        $token->new_email_token = Yii::$app->security->generateRandomString();
        $token->new_email_confirm = 0;

        if ($needConfirmOldEmail) {
            $token->old_email_token = Yii::$app->security->generateRandomString();
            $token->old_email_confirm = 0;
            $token->old_email = $this->oldAttributes['email'];
        }

        return $token->save();
    }

    public function sendEmailConfirmationMail($view, $toAttribute)
    {
        return \Yii::$app->mailer->compose(['html' => $view . '-html', 'text' => $view . '-text'], ['user' => $this, 'token' => $this->emailConfirmToken])
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($this->emailConfirmToken->{$toAttribute})
            ->setSubject('Email confirmation for ' . \Yii::$app->name)
            ->send();
    }

    public function getEmailConfirmToken()
    {
        return $this->hasOne(UserEmailConfirmToken::className(), ['user_id' => 'id']);
    }

    public function getOauthKeys()
    {
        return $this->hasMany(UserOauthKey::className(), 'user_id');
    }

    public function setEmail($email)
    {
        $this->email = $email;
        if ($this->createEmailConfirmToken($this->email != '')) {
            if ($this->sendEmailConfirmationMail(Yii::$app->controller->module->getCustomMailView('confirmNewEmail'), 'new_email')) {
                if ($this->sendEmailConfirmationMail(Yii::$app->controller->module->getCustomMailView('confirmChangeEmail'), 'old_email')) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public function getAssignedRules()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->via('assignments');
    }

    public function getNotAssignedRules()
    {
        return AuthItem::find()->where(['not in', 'name', ArrayHelper::getColumn($this->assignedRules, 'name')])->all();
    }
}
