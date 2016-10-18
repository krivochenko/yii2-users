<?php

namespace budyaga\users\components\oauth;

use budyaga\users\models\User;
use budyaga\users\models\UserOauthKey;

class Yandex extends \yii\authclient\clients\Yandex
{

    public function getViewOptions()
    {
        return [
            'popupWidth' => 900,
            'popupHeight' => 500
        ];
    }

    public function normalizeSex()
    {
        return [
            'male' => User::SEX_MALE,
            'female' => User::SEX_FEMALE
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $attributes =  $this->api('info', 'GET');

        $return_attributes = [
            'User' => [
                'email' => $attributes['emails'][0],
                'username' => $attributes['real_name'],
                'photo' => 'https://avatars.yandex.net/get-yapic/' . $attributes['default_avatar_id'] . '/islands-200',
                'sex' => $this->normalizeSex()[$attributes['sex']]
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['yandex'],
        ];

        return $return_attributes;
    }
}
