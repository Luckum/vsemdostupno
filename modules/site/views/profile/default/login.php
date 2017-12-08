<?php
use yii\helpers\Url;
use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Вход в личный кабинет';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Html::pageHeader(Html::encode($this->title)) ?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n<div class=\"col-md-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-2 control-label'],
    ],
]); ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group forgot-password">
        <div class="col-md-5 text-right">
            <?= Html::a('Забыли пароль?', Url::to(['/profile/forgot-request'])) ?>
        </div>
    </div>

    <?= $form->field($model, 'rememberMe')
        ->checkbox([
            'template' => "<div class=\"col-md-offset-2 col-md-3\">{input} {label}</div>\n<div class=\"col-md-8\">{error}</div>",
        ]) ?>

    <div class="form-group">
        <div class="col-md-5">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary pull-right', 'name' => 'login-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
