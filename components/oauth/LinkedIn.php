<?php

namespace budyaga\users\components\oauth;

use budyaga\users\models\User;
use budyaga\users\models\UserOauthKey;

class LinkedIn extends \yii\authclient\clients\LinkedIn
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

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'r_basicprofile',
                'r_emailaddress',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('people/~:(' . implode(',', [
            'id',
            'email-address',
            'first-name',
            'last-name',
            'picture-urls::(original)']
        ) . ')', 'GET');

        $return_attributes = [
            'User' => [
                'email' => $attributes['email-address'],
                'username' => $attributes['first-name'] . ' ' . $attributes['last-name'],
                'photo' => (isset($attributes['picture-urls'])) ? $attributes['picture-urls']['picture-url'] : '',
                'sex' => User::SEX_MALE
            ],
            'provider_user_id' => $attributes['id'],
            'provider_id' => UserOauthKey::getAvailableClients()['linkedin'],
        ];

        return $return_attributes;
    }
}
