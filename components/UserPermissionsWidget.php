<?php

namespace budyaga\users\components;

use budyaga\users\models\forms\LoginForm;
use yii\base\Widget;

class UserPermissionsWidget extends Widget
{
    public $user;

    public function run()
    {
        return $this->render('userPermissionsWidget', ['user' => $this->user]);
    }
}