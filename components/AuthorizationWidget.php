<?php

namespace budyaga\users\components;

use budyaga\users\models\forms\LoginForm;
use budyaga\users\Module;
use yii\base\Widget;

class AuthorizationWidget extends Widget
{
    public function run()
    {
        $model = new LoginForm;
        Module::registerTranslations();
        return $this->render('authorizationWidget', ['model' => $model]);
    }
}