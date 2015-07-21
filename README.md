Yii2-users
=======================
Module for management users and their rights with the support of registration via social services and assigned to each user more than one social service.

Abilities
---------
#### User registration and authorization by e-mail and via social networks with the ability to bind several different social networking accounts to a one account on the your site
![Authorization widget](https://cloud.githubusercontent.com/assets/7313306/8025081/22fe3ae0-0d52-11e5-8c38-583ddd985ecf.png)
![OAuth keys manage widget](https://cloud.githubusercontent.com/assets/7313306/8025094/a61f284e-0d52-11e5-8efc-2125d9327aad.png)
#### Changing password
![Changing password](https://cloud.githubusercontent.com/assets/7313306/8025109/e242004e-0d52-11e5-9a09-9f2636414afb.png)
#### Recovering password
![Recovering password](https://cloud.githubusercontent.com/assets/7313306/8025124/59b3eebc-0d53-11e5-8c86-83539689d2ad.png)
#### Changing e-mail. Confirmation will be sent to the old and new address
![Changing e-mai](https://cloud.githubusercontent.com/assets/7313306/8025142/a6b96a3e-0d53-11e5-8960-756a8e6bea59.png)
#### Editinig profile
![Editinig profile](https://cloud.githubusercontent.com/assets/7313306/8025147/ce65cef6-0d53-11e5-87d3-e1c8d6b951a9.png)
####  Administation module for manage users
![Admin module](https://cloud.githubusercontent.com/assets/7313306/8025155/31222efe-0d54-11e5-918a-e8a7a3b1a95d.png)
####  Editing RBAC structure and user rights via the GUI
![Editing RBAC structure](https://cloud.githubusercontent.com/assets/7313306/8025181/dc581a9a-0d54-11e5-93d1-d720883f8a72.png)

![Editiong user permissions](https://cloud.githubusercontent.com/assets/7313306/8025425/45d77c04-0d5f-11e5-9540-ba4613df53f2.png)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist budyaga/yii2-users "*"
```

or add

```
"budyaga/yii2-users": "*"
```

to the require section of your `composer.json` file.


Config
-------

```
'user' => [
    'identityClass' => 'budyaga\users\models\User',
    'enableAutoLogin' => true,
    'loginUrl' => ['/login'],
],
'authClientCollection' => [
    'class' => 'yii\authclient\Collection',
    'clients' => [
        'vkontakte' => [
            'class' => 'budyaga\users\components\oauth\VKontakte',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
            'scope' => 'email'
        ],
        'google' => [
            'class' => 'budyaga\users\components\oauth\Google',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
        ],
        'facebook' => [
            'class' => 'budyaga\users\components\oauth\Facebook',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
        ],
        'github' => [
            'class' => 'budyaga\users\components\oauth\GitHub',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
            'scope' => 'user:email, user'
        ],
        'linkedin' => [
            'class' => 'budyaga\users\components\oauth\LinkedIn',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
        ],
        'live' => [
            'class' => 'budyaga\users\components\oauth\Live',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
        ],
        'yandex' => [
            'class' => 'budyaga\users\components\oauth\Yandex',
            'clientId' => 'XXX',
            'clientSecret' => 'XXX',
        ],
        'twitter' => [
            'class' => 'budyaga\users\components\oauth\Twitter',
            'consumerKey' => 'XXX',
            'consumerSecret' => 'XXX',
        ],
    ],
],
'urlManager' => [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '/signup' => '/user/user/signup',
        '/login' => '/user/user/login',
        '/logout' => '/user/user/logout',
        '/requestPasswordReset' => '/user/user/request-password-reset',
        '/resetPassword' => '/user/user/reset-password',
        '/profile' => '/user/user/profile',
        '/retryConfirmEmail' => '/user/user/retry-confirm-email',
        '/confirmEmail' => '/user/user/confirm-email',
        '/unbind/<id:[\w\-]+>' => '/user/auth/unbind',
        '/oauth/<authclient:[\w\-]+>' => '/user/auth/index'
    ],
],
'authManager' => [
    'class' => 'yii\rbac\DbManager',
],

'modules' => [
    'user' => [
        'class' => 'budyaga\users\Module',
        'userPhotoUrl' => 'http://example.com/uploads/user/photo',
        'userPhotoPath' => '@frontend/web/uploads/user/photo'
    ],
],
```
Instead of XXX, you have to use yours values. To receive them, you must create applications on the social networks. 

| Client     | Registration address    | 
| --------|---------|
| vkontakte  | https://vk.com/editapp?act=create|
| google | https://console.developers.google.com/project|
| facebook | https://developers.facebook.com/apps|
| github | https://github.com/settings/applications/new|
| linkedin | https://www.linkedin.com/secure/developer|
| live | https://account.live.com/developers/applications|
| yandex | https://oauth.yandex.ru/client/new|
| twitter | https://dev.twitter.com/apps/new|

If you are using advanced template and Yandex client, then you have to add authClientCollection in configurations for frontend and backend applications. However Yandex client should be added to only one of the applications. The reason for this is that Yandex one application can work with only one domain, you can not add two different Callback URL on different domains. 

Not all services returns all user data. Some of the data get the default settings, if the service does not return them.

Database migrations
--------
*yii migrate/up --migrationPath=@vendor/budyaga/yii2-users/migrations*

This migration create all necessary for the operation of the module tables and two users:

| E-mail     | Password    | 
| --------|---------|
| administrator@example.com| administrator@example.com|
| moderator@example.com| moderator@example.com|

Usage
--------
in main layout:
```
use budyaga\users\components\AuthorizationWidget;
```

```
<?= AuthorizationWidget::widget() ?>
```

**Signup**: http://example.com/signup

**Profile**: http://example.com/profile

**Restore password**: http://example.com/requestPasswordReset

**Manage users**: http://example.com/user/admin

**Manage RBAC**: http://example.com/user/rbac

Custom views and email templates
---------
If you want use custom views and email templates you can override their in config. For example:
```
'modules' => [
    'user' => [
        'class' => 'budyaga\users\Module',
        'customViews' => [
            'login' => '@app/views/site/login'
        ],
        'customMailViews' => [
            'confirmChangeEmail' => '@app/mail/confirmChangeEmail' //in this case you have to create files confirmChangeEmail-html.php and confirmChangeEmail-text.php in mail folder
        ]
    ],
],
```
You can override all views from *vendor\budyaga\yii2-users\views\user* and *vendor\budyaga\yii2-users\mail* folders.