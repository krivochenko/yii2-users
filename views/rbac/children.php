<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use budyaga\users\components\PermissionsTreeWidget;
use budyaga\users\UsersAsset;

$this->title = Yii::t('users', 'CHILDREN_FOR', ['modelName' => $modelForm->model->name]);
$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['/user/rbac/index']];
$this->params['breadcrumbs'][] = ['label' => $modelForm->model->name, 'url' => ['/user/rbac/update', 'id' => $modelForm->model->name, 'type' => $modelForm->model->type]];
$this->params['breadcrumbs'][] = $this->title;

UsersAsset::register($this);
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="row permission-children-editor">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-xs-5 children-list">
        <div class="form-group">
            <input type="text" class="form-control listFilter" placeholder="<?= Yii::t('users', 'FILTER_PLACEHOLDER')?>">
        </div>
        <?= $form->field($modelForm, 'assigned')->dropDownList(
            ArrayHelper::map(
                $modelForm->model->children,
                function ($data) {
                    return serialize([$data->name, $data->type]);
                },
                'description'
            ), ['multiple' => 'multiple', 'size' => '20', 'class' => 'col-xs-12'])
        ?>
    </div>
    <div class="col-xs-2 text-center">
        <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="assign"><span class="glyphicon glyphicon-arrow-left"></span></button>
        <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="revoke"><span class="glyphicon glyphicon-arrow-right"></span></button>
    </div>
    <div class="col-xs-5 children-list">
        <div class="form-group">
            <input type="text" class="form-control listFilter" placeholder="<?= Yii::t('users', 'FILTER_PLACEHOLDER')?>">
        </div>
        <?= $form->field($modelForm, 'unassigned')->dropDownList(
            ArrayHelper::map($modelForm->model->notChildren,
                function ($data) {
                    return serialize([$data->name, $data->type]);
                },
                'description'
            ), ['multiple' => 'multiple', 'size' => '20', 'class' => 'col-xs-12'])
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<h3><?= Yii::t('users', 'CHILDREN')?></h3>
<div class="row">
    <div class="col-xs-12">
        <?= PermissionsTreeWidget::widget(['item' => $modelForm->model])?>
    </div>
</div>