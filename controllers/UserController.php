<?php

namespace budyaga\users\controllers;

use budyaga\users\models\forms\ChangeEmailForm;
use budyaga\users\models\forms\ChangePasswordForm;
use budyaga\users\models\forms\RetryConfirmEmailForm;
use budyaga\users\models\UserEmailConfirmToken;
use budyaga\users\models\forms\LoginForm;
use budyaga\users\models\forms\PasswordResetRequestForm;
use budyaga\users\models\forms\ResetPasswordForm;
use budyaga\users\models\forms\SignupForm;
use Yii;
use yii\helpers\Url;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

class UserController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'uploadPhoto' => [
                'class' => 'budyaga\cropper\actions\UploadAction',
                'url' => Yii::$app->controller->module->userPhotoUrl,
                'path' => Yii::$app->controller->module->userPhotoPath,
            ]
        ];
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render($this->module->getCustomView('login'), [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($user = $model->signup()) {
                if ($user->createEmailConfirmToken() && $user->sendEmailConfirmationMail(Yii::$app->controller->module->getCustomMailView('confirmNewEmail'), 'new_email')) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('users', 'CHECK_YOUR_EMAIL_FOR_FURTHER_INSTRUCTION'));
                    $transaction->commit();
                    return $this->redirect(Url::toRoute('/login'));
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('users', 'CAN_NOT_SEND_EMAIL_FOR_CONFIRMATION'));
                    $transaction->rollBack();
                };
            }
            else {
                Yii::$app->getSession()->setFlash('error', Yii::t('users', 'CAN_NOT_CREATE_NEW_USER'));
                $transaction->rollBack();
            }
        }

        return $this->render($this->module->getCustomView('signup'), [
            'model' => $model,
        ]);
    }

    public function actionRetryConfirmEmail()
    {
        $model = new RetryConfirmEmailForm;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->user->sendEmailConfirmationMail(Yii::$app->module->getCustomMailView('confirmNewEmail'), 'new_email')) {
                Yii::$app->getSession()->setFlash('success', Yii::t('users', 'CHECK_YOUR_EMAIL_FOR_FURTHER_INSTRUCTION'));
                return $this->redirect(Url::toRoute('/user/user/retry-confirm-email'));
            }
        }

        return $this->render($this->module->getCustomView('retryConfirmEmail'), [
            'model' => $model
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('users', 'CHECK_YOUR_EMAIL_FOR_FURTHER_INSTRUCTION'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('users', 'CAN_NOT_SEND_EMAIL_FOR_CONFIRMATION'));
            }
        }

        return $this->render($this->module->getCustomView('requestPasswordResetToken'), [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('users', 'NEW_PASSWORD_WAS_SAVED'));

            return $this->goHome();
        }

        return $this->render($this->module->getCustomView('resetPassword'), [
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(Url::toRoute('/login'));
        }
        $model = Yii::$app->user->identity;
        $changePasswordForm = new ChangePasswordForm;
        $changeEmailForm = new ChangeEmailForm;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('users', 'CHANGES_WERE_SAVED'));

            return $this->redirect(Url::toRoute('/user/user/profile'));
        }

        if ($model->password_hash != '') {
            $changePasswordForm->scenario = 'requiredOldPassword';
        }

        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            $model->setPassword($changePasswordForm->new_password);
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('users', 'NEW_PASSWORD_WAS_SAVED'));
                return $this->redirect(Url::toRoute('/user/user/profile'));
            }
        }

        if ($changeEmailForm->load(Yii::$app->request->post()) && $changeEmailForm->validate() && $model->setEmail($changeEmailForm->new_email)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('users', 'TO_YOURS_EMAILS_WERE_SEND_MESSAGES_WITH_CONFIRMATIONS'));
            return $this->redirect(Url::toRoute('/user/user/profile'));
        }

        return $this->render($this->module->getCustomView('profile'), [
            'model' => $model,
            'changePasswordForm' => $changePasswordForm,
            'changeEmailForm' => $changeEmailForm
        ]);
    }

    public function actionConfirmEmail($token)
    {
        $tokenModel = UserEmailConfirmToken::findToken($token);

        if ($tokenModel) {
            Yii::$app->getSession()->setFlash('success', $tokenModel->confirm($token));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('users', 'CONFIRMATION_LINK_IS_WRONG'));
        }

        return $this->redirect(Url::toRoute('/'));
    }
}
