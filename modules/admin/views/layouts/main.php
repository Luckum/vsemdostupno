<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\bootstrap\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
use app\models\User;
use app\models\Module;


/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Html::encode(Yii::$app->params['name']),
                'brandUrl' => Url::to(['/admin']),
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            if (!Yii::$app->user->isGuest) {
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => [
                        [
                            'label' => 'Заказы',
                            'items' => [
                                ['label' => 'Участников', 'url' => ['/admin/order/member']],
                                ['label' => 'Партнеров', 'url' => ['/admin/order/partner']],
                                ['label' => 'Гостей', 'url' => ['/admin/order/guest']],
                                ['label' => 'Статусы заказов', 'url' => ['/admin/order-status']],
                                //['label' => 'Заказы поставщикам', 'url'=>['/admin/provider-order']]
                                ['label' => 'Коллективная закупка', 'url'=>['/admin/provider-order']]
                            ],
                        ],
                        
                        ['label' => 'Товары', 'url' => ['/admin/product']],
                        ['label' => 'Услуги', 'url' => ['/admin/service']],
                        ['label' => 'Категории', 'url' => ['/admin/category']],
                        [
                            'label' => 'Пользователи',
                            'items' => [
                                ['label' => 'Участники', 'url' => ['/admin/member']],
                                ['label' => 'Партнеры', 'url' => ['/admin/partner']],
                                ['label' => 'Поставщики', 'url' => ['/admin/provider']],
                                ['label' => 'Кандидаты', 'url' => ['/admin/candidate'], 'visible' => Yii::$app->user->identity->role == User::ROLE_SUPERADMIN],
                                ['label' => 'Членские взносы', 'url' => ['/admin/subscriber-payment']],
                                ['label' => 'Поиск контрагентов', 
                                'url' => '#',
                                'options' => ['data-toggle' => 'modal', 'data-target'=>'#myModal'],
                                ],
                            ],
                        ],
                        [
                            'label' => 'Сайт',
                            'items' => [
                                ['label' => 'Фонды', 'url' => ['/admin/fund']],
                                ['label' => 'Страницы', 'url' => ['/admin/page']],
                                ['label' => 'Письма', 'url' => ['/admin/email']],
                                ['label' => 'Города', 'url' => ['/admin/city']],
                                ['label' => 'Параметры', 'url' => ['/admin/parameter']],
                                ['label' => 'Файлы', 'url' => ['/elfinder/manager/', 'lang' => 'ru'], 'linkOptions' => ['target' => '_blank']],
                                ['label' => 'Модули', 'url' => ['/admin/module'], 'visible' => Yii::$app->user->identity->role == User::ROLE_SUPERADMIN],
                            ],
                        ],
                        [
                            'label' => 'Рассылки',
                            'items' => [
                                ['label' => 'Рассылка информации', 'url' => ['/admin/mailing']],
                                ['label' => 'Статистика голосования', 'url' => ['/admin/mailing/vote']],
                                ['label' => 'Жалобы и предложения', 'url' => ['/admin/mailing/message']],
                            ],
                            'visible' => Yii::$app->hasModule('mailing'),
                        ],
                        ['label' => 'Выход (' . Yii::$app->user->identity->username . ')',
                            'url' => ['/admin/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                    ],
                ]);
            }
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'homeLink' => ['label' => Yii::t('yii', 'Home'), 'url' => Url::to(['/admin'])],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; <?= Html::encode(Yii::$app->params['name']) ?> <?= date('Y') ?></p>
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Поиск контрагентов</h4>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Yii::$app->urlManager->createUrl(['admin/search/search'])
        ]); ?>
            <label for="fio" >Поиск по фамилии</label>
            <?php
            echo Typeahead::widget([
    'name' => 'fio',
    'options' => ['placeholder' => 'Начните вводить фамилию'],
    'pluginOptions' => ['highlight'=>true],
    'dataset' => [
        [
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
            'display' => 'value',
            
            'remote' => [
                'url' => Url::to(['search/searchajax']) . '?name=%QUERY',
                'wildcard' => '%QUERY'
            ],
        ]
    ]
            ]); 
        ?>
            <label for="reg_nom" style="margin-top: 20px;">Поиск по регистрационному номеру</label>
            <?php
            echo Typeahead::widget([
    'name' => 'reg_Nom',
    'options' => ['placeholder' => 'Начните вводить регистрационный номер'],
    'pluginOptions' => ['highlight'=>true],
    'dataset' => [
        [
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
            'display' => 'value',
            
            'remote' => [
                'url' => Url::to(['search/searchajax']) . '?disc_number=%QUERY',
                'wildcard' => '%QUERY'
            ],
        ]
    ]
            ]); 
        ?>
            <label for="nomer_order" style="margin-top: 20px;">Поиск по № заказа</label>
            <?php
            echo Typeahead::widget([
    'name' => 'nomer_order',
    'options' => ['placeholder' => 'Начните вводить номер заказа'],
    'pluginOptions' => ['highlight'=>true],
    'dataset' => [
        [
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
            'display' => 'value',
            
            'remote' => [
                'url' => Url::to(['search/searchajax']) . '?order_numb=%QUERY',
                'wildcard' => '%QUERY'
            ],
        ]
    ]
            ]); 
        ?>
            <button type="submit" class="btn btn-success" style="width:150px; margin-left: 73%; margin-top: 5%;">Поиск</button>
            <?php $form= ActiveForm::end(); ?>
        </form>
      </div>
      <div class="modal-footer">
        

      </div>
    </div>
  </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
