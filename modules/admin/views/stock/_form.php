<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use kartik\editable\Editable;
use wbraganca\fancytree\FancytreeWidget;
use app\models\Category;
use app\models\City;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;

?>

<div class="provider-form">

    <div class="stockbody-1">
        

        <div class="form-group">
            <label for="tare">Тара</label>
            <?= Html::dropDownList(
                'tare',
                '',
                [
                    'с/бут.' => 'с/бут.',
                    'п/бут.' => 'п/бут.',
                    'c/бан.' => 'c/бан.',
                    'п/к.' => 'п/к.',
                    'кор.' => 'кор.',
                    'п/п.' => 'п/п.',
                    'п/м.' => 'п/м.',
                    'мешок' => 'мешок'],
                ['class' => 'form-control', 'id' => 'tare']
            ); ?>
        </div>

        <div class="form-group">
            <label for="weight">Масса</label>
            <?= Html::textInput('weight', $product->weight, ['class' => 'form-control', 'id' => 'weight']); ?>
        </div>

        <div class="form-group">
            <label for="measurement">Ед. измерения</label>
            <?= Html::dropDownList(
                'measurement',
                '',
                [
                    'кг.' => 'кг.',
                    'л.' => 'л.',
                    'шт.' => 'шт.',
                    'гр.' => 'гр.',
                    'мл.' => 'мл.',
                    'мг.' => 'мг.',
                    'разновес.' => 'разновес.'],
                ['class' => 'form-control', 'id' => 'measurement']
            ); ?>
        </div>

        <div class="form-group">
            <label for="count">Количество</label>
            <?= Html::textInput('count', null, ['class' => 'form-control', 'id' => 'count']); ?>
        </div>

        <div class="form-group">
            <label for="summ">Сумма за ед./т.</label>
            <?= Html::textInput('summ', $product->purchase_price, ['class' => 'form-control', 'id' => 'summ', 'readonly' => true]); ?>
        </div>

        <div class="form-group">
            <?= Html::checkbox('new_price', false, ['id' => 'new-price', 'onchange' => 'toggleNewPrice(this)']); ?>
            <label for="new-price">Принять по новой цене</label>
        </div>
        
        <div class="form-group">
            <?= Html::checkbox('deposit', false, ['id' => 'deposit']); ?>
            <label for="deposit">Зачислять на лицевой счёт</label>
        </div>
        
        <div class="form-group">
            <label for="comment">Комментарий</label>
            <?= Html::textarea('comment', '', ['class' => 'form-control', 'id' => 'comment']); ?>
        </div>
    </div>


</div>
