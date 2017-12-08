<?php

use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
use himiklab\yii2\recaptcha\ReCaptcha;

/* @var $this yii\web\View */
$this->title = 'Восстановление пароля';
$this->params['breadcrumbs'] = [$this->title];

?>

<?= Html::pageHeader(Html::encode($this->title)) ?>

<?php $form = ActiveForm::begin([
    'id' => 'forgot-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-4\">{input}</div>\n<div class=\"col-md-6\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-2 control-label'],
    ],
]); ?>

    <div class="hidden">
        <?= $form->field($model, 'token')->hiddenInput()->label(false) ?>
    </div>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'password_repeat')->passwordInput() ?>

    <div class="form-group">
        <div class="col-md-6">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary pull-right', 'name' => 'forgot-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
