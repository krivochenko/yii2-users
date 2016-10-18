<?php

namespace budyaga\users\components\oauth;

use budyaga\users\models\User;
use budyaga\users\models\UserOauthKey;

class Google extends \yii\authclient\clients\Google
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
            'male' => User::SEX_MALE,
            'female' => User::SEX_FEMALE
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'profile',
                'email',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('people/me', 'GET');

        $return_attributes = [
            'User' => [
                'email' => $attributes['emails'][0]['value'],
                'username' => $attributes['displayName'],
                'photo' => str_replace('sz=50', 'sz=200', $attributes['image']['url']),
                'sex' => $this->normalizeSex()[$attributes['gender']]
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['google'],
        ];

        return $return_attributes;
    }
}
