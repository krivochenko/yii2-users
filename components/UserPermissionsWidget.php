<?php

namespace budyaga\users\components;

use budyaga\users\models\AuthItem;
use yii\base\Widget;
use Yii;

class UserPermissionsWidget extends Widget
{
    public $user;

    public function run()
    {
        $defaultAssignments = AuthItem::find()->where(['in', 'name', Yii::$app->authManager->defaultRoles])->all();
        return $this->render('userPermissionsWidget', ['user' => $this->user, 'defaultAssignments' => $defaultAssignments]);
    }
}