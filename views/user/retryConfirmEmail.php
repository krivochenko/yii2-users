<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('users', 'EMAIL_CONFIRMATION');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-retry-confirm-email">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('users', 'RETRY_EMAIL_CONFIRMATION_NOTE')?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'retry-confirm-email-form']); ?>
            <?= $form->field($model, 'email')->input('email') ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('users', 'SEND'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>