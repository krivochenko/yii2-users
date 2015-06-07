<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model budyaga\users\models\AuthRule */

$this->title = Yii::t('users', 'CREATE_MODEL', ['type' => $this->context->getModelTypeTitle($type)]);
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form' . $this->context->getModelName($type), [
        'model' => $model,
        'type' => $type
    ]) ?>

</div>
