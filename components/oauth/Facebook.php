<?php

namespace budyaga\users\components\oauth;

use budyaga\users\models\User;
use budyaga\users\models\UserOauthKey;

class Facebook extends \yii\authclient\clients\Facebook
{
    /**
     * @inheritdoc
     */
    public function getViewOptions()
    {
        return [
            'popupWidth' => 900,
            'popupHeight' => 600
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
        $attributes = $this->api('me', 'GET', [
            'fields' => implode(',', [
                'id',
                'email',
                'name',
                'picture.height(200).width(200)',
                'gender'
            ]),
        ]);


        $return_attributes = [
            'User' => [
                'email' => $attributes['email'],
                'username' => $attributes['name'],
                'photo' => $attributes['picture']['data']['url'],
                'sex' => $this->normalizeSex()[$attributes['gender']]
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['facebook'],
        ];

        return $return_attributes;
    }
}
