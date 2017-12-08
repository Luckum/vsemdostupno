<?php

use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use kartik\helpers\Html;
use kartik\icons\Icon;
use kartik\select2\Select2;
use dosamigos\selectize\SelectizeDropDownList;
use app\models\City;
use app\models\User;
use app\models\Partner;

/* @var $this yii\web\View */
$this->title = 'Оформить заказ';
$this->params['breadcrumbs'][] = $this->title;

?>

<?= Html::pageHeader(Html::encode($this->title)) ?>

<?php $form = ActiveForm::begin([
    'id' => 'order-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-4\">{input}</div>\n<div class=\"col-md-6\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-2 control-label'],
    ],
]); ?>

    <?php if ($model->canFilled('partner')): ?>
        <?php
            $data = [];
            foreach (City::find()->each() as $city) {
                $partners = Partner::find()
                    ->joinWith(['user'])
                    ->where('{{%partner}}.city_id = :city_id AND {{%user}}.disabled = 0', [':city_id' => $city->id])
                    ->all();
                if ($partners) {
                    $data[$city->name] = ArrayHelper::map($partners, 'id', 'name');
                }
            }
            echo $form->field($model, 'partner')->widget(Select2::className(), [
                'data' => $data,
                'language' => substr(Yii::$app->language, 0, 2),
                'options' => ['placeholder' => 'Выберите партнера ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
    <?php endif ?>

    <?php if ($model->canFilled('lastname')): ?>
        <?= $form->field($model, 'lastname') ?>
    <?php endif ?>

    <?php if ($model->canFilled('firstname')): ?>
        <?= $form->field($model, 'firstname') ?>
    <?php endif ?>

    <?php if ($model->canFilled('patronymic')): ?>
        <?= $form->field($model, 'patronymic') ?>
    <?php endif ?>

    <?php if ($model->canFilled('phone')): ?>
        <?= $form->field($model, 'phone')->widget(
            MaskedInput::className(), [
            'mask' => '+7 (999)-999-9999',
        ]) ?>
    <?php endif ?>

    <?php if ($model->canFilled('email')): ?>
        <?= $form->field($model, 'email') ?>
    <?php endif ?>

    <?php if ($model->canFilled('address')): ?>
        <?= $form->field($model, 'address')->textArea(['rows' => '6', 'placeholder' => 'Если нужна доставка, то заполните это поле.']) ?>
    <?php endif ?>

    <?php if ($model->canFilled('comment')): ?>
        <?= $form->field($model, 'comment')->textArea(['rows' => '6', 'placeholder' => 'Если хотите сообщить дополнительную информацию к заказу, то заполните это поле.']) ?>
    <?php endif ?>

    <div class="form-group">
        <div class="col-md-6">
            <?= Html::submitButton(Icon::show('send') . ' Отправить заказ', ['class' => 'btn btn-success pull-right', 'name' => 'send-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
