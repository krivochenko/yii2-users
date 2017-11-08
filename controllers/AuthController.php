<?php

namespace budyaga\users\controllers;

use budyaga\users\models\User;
use budyaga\users\models\UserOauthKey;
use Yii;

class AuthController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if ($action->id == 'index' && Yii::$app->request->referrer !== null) {
            Yii::$app->session->set('returnUrl', Yii::$app->request->referrer);
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback']
            ],
        ];
    }

    public function successCallback($client)
    {
        $attributes = $client->userAttributes;

        $this->action->successUrl = Yii::$app->session->get('returnUrl');

        $key = UserOauthKey::findOne([
            'provider_id' => $attributes['provider_id'],
            'provider_user_id' => $attributes['provider_user_id']
        ]);

        if ($key) {
            if (Yii::$app->user->isGuest) {
                return Yii::$app->user->login($key->user, 3600 * 24 * 30);
            } else {
                if ($key->user_id != Yii::$app->user->id) {
                    Yii::$app->session->setFlash('error', 'Данный ключ уже закреплен за другим пользователем сайта.');
                    return true;
                }
            }
        } else {
            if (Yii::$app->user->isGuest) {
                $user = false;
                if ($attributes['User']['email'] != null) {
                    $user = User::findByEmailOrUserName($attributes['User']['email']);
                }
                if (!$user) {
                    $user = new User;
                    $user->generateAuthKey();
                    $user->password_hash = '';
                    $user->scenario = 'oauth';
                    $user->load($attributes);
                    $user->username = $this->findFreeUsername($user->username);

                    $user->validate();

                    return ($user->save() && $this->createKey($attributes, $user->id) && Yii::$app->user->login($user, 3600 * 24 * 30));
                } else {
                    return ($this->createKey($attributes, $user->id) && Yii::$app->user->login($user, 3600 * 24 * 30));
                }

            } else {
                $this->createKey($attributes, Yii::$app->user->id);
                Yii::$app->session->setFlash('success', 'Ключ успешно добавлен.');
                return true;
            }
        }
    }

    public function actionUnbind($id)
    {
        $key = UserOauthKey::findOne(['user_id' => Yii::$app->user->id, 'provider_id' => UserOauthKey::getAvailableClients()[$id]]);

        if (!$key) {
            Yii::$app->session->setFlash('error', 'Ключ не найден');
        } else {
            $key->delete();
            Yii::$app->session->setFlash('success', 'Ключ удален');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function createKey($attributes, $user_id)
    {
        $key = new UserOauthKey;
        $key->provider_id = $attributes['provider_id'];
        $key->provider_user_id = $attributes['provider_user_id'];
        $key->user_id = $user_id;
        return $key->save();
    }

    protected function findFreeUsername($username, $n = '')
    {
        $exists = User::findOne(['username' => $username.$n]);
        if ($exists) {
            $n = ($n == '') ? 2 : ($n + 1);
            return $this->findFreeUsername($username, $n);
        }
        return $username.$n;
    }
}
