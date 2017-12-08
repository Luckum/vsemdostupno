<?php

use kartik\helpers\Html;

?>
<?php if ($items): ?>
    <div class="row">
        <div class="col-md-12">
            <?= Html::panel([
                    'heading' => $heading,
                    'postBody' => Html::listGroup($items),
                    'headingTitle' => true,
                ],
                isset($type) ? $type : Html::TYPE_PRIMARY,
                [
                    'class' => 'menu-panel ' . (isset($class) ? $class : ''),
                ]
            ) ?>
        </div>
    </div>
<?php endif ?>
