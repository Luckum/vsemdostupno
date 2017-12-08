<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use mihaildev\ckeditor\CKEditor;
use app\models\Category;
use app\models\Product;
use wbraganca\fancytree\FancytreeWidget;
use kartik\file\FileInput;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs("CKEDITOR.plugins.addExternal('youtube', '/ckeditor/plugins/youtube/youtube/plugin.js', '');");
?>

<div class="product-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]); ?>

    <?= $form->field($model, 'visibility')->checkbox() ?>

    <?= $form->field($model, 'only_member_purchase')->checkbox() ?>
    
    <?= $form->field($model, 'auto_send')->checkbox(); ?>

    <?php if ($model->isNewRecord): ?>
        <?php if (!empty($model->provider_id)): ?>
            <input type="hidden" name="Product[provider_id]" value="<?= $model->provider_id; ?>">
            <div class="form-group field-provider-name required">
                <label class="control-label" for="provider-name">Название организации / ФИО поставщика</label>
                <input id="provider-name" class="form-control" name="provider_name" value="<?= $provider->name . ' / ' . $provider->user->fullName; ?>" readonly="" type="text">
            </div>
            <label for="product-category-id" class="control-label">Категория</label>
            <select id="product-category-id" class="form-control" name="Product[category_id]">
                <option value="0" selected disabled>Выберите категорию товара</option>
                <?php foreach ($categories as $cat):?>
                    <option value="<?= $cat->category->id; ?>"><?= $cat->category->name; ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <div class="form-group field-product-provider_id required">
                <label class="control-label" for="provider_id">ФИО или наименование организации поставщика</label>
                <?= Select2::widget([
                    'id' => 'product-provider_id',
                    'name' => 'Product[provider_id]',
                    'options' => ['placeholder' => 'Введите ФИО или наименование организации поставщика'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'language' => substr(Yii::$app->language, 0, 2),
                        'ajax' => [
                            'url' => Url::to(['/api/profile/admin/provider/id-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                    ],
                    'pluginEvents' => [
                        'select2:select' => new JsExpression('function() { toggleCategoriesContainer("show"); }'),
                        'select2:unselect' => new JsExpression('function() { toggleCategoriesContainer("hide") }'),
                    ],
                ]) ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php if (isset($model->provider)): ?>
            <input type="hidden" name="Product[provider_id]" value="<?= $model->provider->id; ?>">
            <input type="hidden" name="Product[category_id]" value="<?= $model->category->id; ?>">
            <div class="form-group field-provider-name required">
                <label class="control-label" for="provider-name">Название организации / ФИО поставщика</label>
                <input id="provider-name" class="form-control" name="provider_name" value="<?= $model->provider->name . ' / ' . $model->provider->user->fullName; ?>" readonly="" type="text">
            </div>
            <div class="form-group field-category-name required">
                <label class="control-label" for="category-name">Название категории</label>
                <input id="category-name" class="form-control" name="category_name" value="<?= $model->category->name; ?>" readonly="" type="text">
            </div>
        <?php else: ?>
            <div class="form-group field-product-provider_id required">
                <label class="control-label" for="provider_id">ФИО или наименование организации поставщика</label>
                <?= Select2::widget([
                    'id' => 'product-provider_id',
                    'name' => 'Product[provider_id]',
                    'options' => ['placeholder' => 'Введите ФИО или наименование организации поставщика'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'language' => substr(Yii::$app->language, 0, 2),
                        'ajax' => [
                            'url' => Url::to(['/api/profile/admin/provider/id-search']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                    ],
                    'pluginEvents' => [
                        'select2:select' => new JsExpression('function() { toggleCategoriesContainer("show"); }'),
                        'select2:unselect' => new JsExpression('function() { toggleCategoriesContainer("hide") }'),
                    ],
                ]) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="form-group field-product-category required" style="display: none;" id="product-categories-container">
        
    </div>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'storage_price') ?>

    <?= $form->field($model, 'purchase_price') ?>

    <?= $form->field($model, 'partner_price') ?>

    <?= $form->field($model, 'member_price') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'expiry_timestamp')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
        'readonly' => true,
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
            'startDate' => (new \DateTime('now'))->format('Y-m-d'),
        ],
    ]) ?>

    <?= $form->field($model, 'composition')->textArea(['rows' => '6']) ?>

    <?= $form->field($model, 'packing') ?>

    <?= $form->field($model, 'manufacturer') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'description')->widget(CKEditor::className(), [
        'editorOptions' => [
            'extraPlugins' => 'youtube',
            'preset' => 'full',
            'inline' => false,
        ],
    ]) ?>

    <?php
        $initialPreview = [];
        $initialPreviewConfig = [];
        foreach ($model->productHasPhoto as $item) {
            $initialPreview[] = Html::img($item->thumbUrl);
            $initialPreviewConfig[] = [
                'url' => Url::to(['/api/profile/admin/photo/delete']),
                'extra' => [
                    'PhotoDeletion[key]' => $item->photo->id,
                    'PhotoDeletion[id]' => $model->id,
                    'PhotoDeletion[class]' => $model->className(),
                ],
            ];
        }
        echo $form->field($model, 'gallery[]')->widget(FileInput::className(), [
            'name' => get_class($model) . '[gallery[]]',
            'language' => substr(Yii::$app->language, 0, 2),
            'options' => [
                'multiple' => true,
            ],
            'pluginOptions' =>[
                'initialPreview' => $initialPreview,
                'initialPreviewConfig' => $initialPreviewConfig,
                'previewFileType' => 'any',
                'maxFileCount' => Product::MAX_FILE_COUNT,
            ]
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
