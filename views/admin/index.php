<?php

use yii\helpers\Html;
use yii\grid\GridView;
use budyaga\users\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('users', 'USERS');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= (Yii::$app->user->can('userCreate')) ? Html::a(Yii::t('users', 'CREATE'), ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'sex',
                'value' => function($data) {
                    return User::getSexArray()[$data->sex];
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($data) {
                    return User::getStatusArray()[$data->status];
                }
            ],
             'created_at:datetime',
             'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {permissions}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        if (!Yii::$app->user->can('userView', ['user' => $model])) {
                            return '';
                        }
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                    },
                    'update' => function ($url, $model, $key) {
                        if (!Yii::$app->user->can('userUpdate', ['user' => $model])) {
                            return '';
                        }
                        $options = [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                    'permissions' => function ($url, $model, $key) {
                        if (!Yii::$app->user->can('userPermissions', ['user' => $model])) {
                            return '';
                        }
                        $options = [
                            'title' => Yii::t('users', 'PERMISSIONS'),
                            'aria-label' => Yii::t('users', 'PERMISSIONS'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-cog"></span>', $url, $options);
                    },
                    'delete' => function($url, $model, $key) {
                        if (!Yii::$app->user->can('userDelete', ['user' => $model])) {
                            return '';
                        }
                        $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
