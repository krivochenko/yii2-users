<?php

namespace budyaga\users;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'budyaga\users\controllers';

    public $userPhotoUrl = '';

    public $userPhotoPath = '';

    public $customViews = [];

    public $customMailViews = [];

    public function init()
    {
        parent::init();

        self::registerTranslations();
    }

    public static function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['users']) && !isset(Yii::$app->i18n->translations['users/*'])) {
            Yii::$app->i18n->translations['users'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@budyaga/users/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'users' => 'users.php'
                ]
            ];
        }
    }

    public function getCustomView($default)
    {
        if (isset($this->customViews[$default])) {
            return $this->customViews[$default];
        } else {
            return $default;
        }
    }

    public function getCustomMailView($default)
    {
        if (isset($this->customMailViews[$default])) {
            return $this->customMailViews[$default];
        } else {
            return '@budyaga/users/mail/' . $default;
        }
    }
}
