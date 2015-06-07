<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model budyaga\users\models\AuthRule */

$this->title = Yii::t('users', 'UPDATE_MODEL', ['type' => Yii::t('users', $this->context->getModelTypeTitle($type)), 'name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/user/rbac/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formAuthItem', [
        'model' => $model,
        'type' => $type
    ]) ?>

</div>
