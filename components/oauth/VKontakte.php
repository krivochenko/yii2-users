<?php

namespace budyaga\users\components\oauth;

use budyaga\users\models\User;
use budyaga\users\models\UserOauthKey;

class VKontakte extends \yii\authclient\clients\VKontakte
{
    /**
     * @inheritdoc
     */
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
            '1' => User::SEX_FEMALE,
            '2' => User::SEX_MALE
        ];
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('users.get.json', 'GET', [
            'fields' => implode(',', [
                'uid',
                'first_name',
                'last_name',
                'photo_200',
                'sex'
            ]),
        ]);

        $attributes = array_shift($attributes['response']);

        $return_attributes = [
            'User' => [
                'email' => (isset($this->accessToken->params['email'])) ? $this->accessToken->params['email'] : null,
                'username' => $attributes['first_name'] . ' ' . $attributes['last_name'],
                'photo' => $attributes['photo_200'],
                'sex' => $this->normalizeSex()[$attributes['sex']]
            ],
            'provider_user_id' => $attributes['uid'],
            'provider_id' => UserOauthKey::getAvailableClients()['vkontakte'],
        ];

        return $return_attributes;
    }
}
