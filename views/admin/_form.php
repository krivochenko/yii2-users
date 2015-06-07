<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use budyaga\cropper\Widget;
use budyaga\users\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model budyaga\users\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'photo')->widget(Widget::className(), [
        'uploadUrl' => Url::toRoute('/user/user/uploadPhoto'),
    ]) ?>

    <?= $form->field($model, 'sex')->dropDownList(User::getSexArray()); ?>

    <?= $form->field($model, 'status')->dropDownList(User::getStatusArray()); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('users', 'CREATE') : Yii::t('users', 'UPDATE'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
