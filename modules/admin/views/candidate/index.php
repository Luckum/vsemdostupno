<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use kartik\tabs\TabsX;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;
use app\models\Candidate;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Кандидаты';
$this->params['breadcrumbs'][] = $this->title;

$dd_items = $items = [];
if (count($groups)) {
    $dd_items = ArrayHelper::map($groups, 'id', 'name');
    foreach ($groups as $val) {
        $dataProvider = new ActiveDataProvider([
            'query' => Candidate::find()->where(['group_id' => $val['id']]),
        ]);
        $items[] = [
            'label' => $val['name'],
            'content' => GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'email',
                    'fullName',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ])
        ];
    }
}

?>
<div class="candidate-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить кандидата', ['create'], ['class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#add-candidate-modal']) ?>
        <?= Html::a('Добавить группу', ['/admin/candidate-group/create'], ['class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-target' => '#add-group-modal']) ?>
    </p>
    <br />
    
    <?php if (count($items)): ?>
        <?= TabsX::widget([
            'items' => $items,
            'position' => TabsX::POS_LEFT,
            'encodeLabels' => false
        ]); ?>
    <?php endif; ?>
</div>

<?php Modal::begin([
    'id' => 'add-group-modal',
    'options' => ['tabindex' => false,],
    'header' => '<h4>' . 'Добавить группу кандидатов' . '</h4>',
]); ?>
    
    <?php $form = ActiveForm::begin(['action' => ['/admin/candidate-group/create']]); ?>
    
    <?= $form->field($modelGroup, 'name')->textInput(['maxlength' => true]) ?>
    
    <div class="form-group" style="text-align: right;">
        <?= Html::button('Закрыть', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'add-candidate-modal',
    'options' => ['tabindex' => false,],
    'header' => '<h4>' . 'Добавить кандидата' . '</h4>',
]); ?>
    
    <?php $form = ActiveForm::begin(['action' => ['/admin/candidate/create']]); ?>
    
        <?= $form->field($modelCandidate, 'group_id')->dropDownList($dd_items) ?>
        
        <?= $form->field($modelCandidate, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($modelCandidate, 'firstname')->textInput(['maxlength' => true]) ?>

        <?= $form->field($modelCandidate, 'lastname')->textInput(['maxlength' => true]) ?>

        <?= $form->field($modelCandidate, 'patronymic')->textInput(['maxlength' => true]) ?>

        <?= $form->field($modelCandidate, 'birthdate')->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'readonly' => true,
            'layout' => '{input}{picker}',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],
        ]) ?>

        <?= $form->field($modelCandidate, 'phone')->widget(
            MaskedInput::className(), [
            'mask' => '+7 (999)-999-9999',
        ]) ?>

        <?= $form->field($modelCandidate, 'block_mailing')->checkbox() ?>
        
        <div class="form-group" style="text-align: right;">
            <?= Html::button('Закрыть', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
        </div>
    
    <?php ActiveForm::end(); ?>
<?php Modal::end(); ?>
