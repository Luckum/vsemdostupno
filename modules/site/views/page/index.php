<?php

use kartik\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = $model->title;
$this->params['breadcrumbs'] = [$model->title];

?>

<?= Html::pageHeader(Html::encode($model->title)) ?>

<?= $model->content ?>
