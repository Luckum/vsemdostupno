<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use nirvana\showloading\ShowLoadingAsset;
use raoul2000\widget\scrollup\Scrollup;
use raoul2000\bootswatch\BootswatchAsset;
use kartik\icons\Icon;
use app\assets\AppAsset;
use app\assets\BootboxAsset;
use app\models\Category;
use app\models\Cart;
use yii\bootstrap\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
use app\models\User;
use yii\bootstrap\Alert;


/* @var $this \yii\web\View */
/* @var $content string */

BootswatchAsset::$theme = Yii::$app->params['theme'];

AppAsset::register($this);
BootboxAsset::overrideSystemMessageBox();
ShowLoadingAsset::register($this);
Icon::map($this);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/images/favicon.png']);

function getMenuItems($andWhere = 'TRUE')
{
    $items = [];
    $categories = Category::find()
        ->roots()
        ->andWhere('visibility != 0')
        ->andWhere($andWhere)
        ->orderBy(['name' => SORT_ASC])
        ->all();
    foreach ($categories as $category) {
        $items[] = [
            'content' => Html::encode($category->fullName),
            'url' => $category->url,
        ];
    }

    return $items;
}

$recomendations = ArrayHelper::merge(
    [
        [
            'content' => 'Слушать радио',
            'url' => 'http://рага.рф',
            'options' => [
                'target' => '_blank',
            ],
        ],
    ],
    getMenuItems('slug != "" AND slug != "' . Category::PURCHASE_SLUG . '"')
);
$catalogue = getMenuItems('slug = ""');

$purchases = [];
$purchase = Category::findOne(['slug' => Category::PURCHASE_SLUG]);
if ($purchase) {
    $categories = $purchase
        ->children()
        ->andWhere('visibility != 0')
        ->orderBy([
            'purchase_timestamp' => SORT_ASC,
        ])
        ->all();
    foreach ($categories as $category) {
        $purchases[] = [
            'content' => $category->htmlFormattedFullName,
            'url' => $category->url,
        ];
    }
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php Scrollup::widget([
            'theme' => Scrollup::THEME_PILLS,
            'pluginOptions' => [
                'scrollText' => 'Наверх',
                'scrollName'=> 'scrollUp',
                'topDistance'=> 400,
                'topSpeed'=> 3000,
                'animation' => Scrollup::ANIMATION_SLIDE,
                'animationInSpeed' => 200,
                'animationOutSpeed'=> 200,
                'activeOverlay' => false,
            ]
        ]) ?>
        <?php $this->beginBody() ?>
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
            'action' => Yii::$app->urlManager->createUrl(['site/search/search'])
        ]); ?>
            <label for="fio">Поиск по фамилии</label>
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
        ?>  <input type="hidden" name='id' value="<?= Yii::$app->user->id ?>">
            <button type="submit" class="btn btn-success" style="width:150px; margin-left: 73%; margin-top: 5%;">Поиск</button>
            <?php $form= ActiveForm::end(); ?>
        </form>
      </div>
      
    </div>
  </div>
</div>
        <div class="modal fade" id="providerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Стать поставщиком</h4>
                    </div>



                    <div class="modal-body">

                        <b>Желаете стать поставщиком собственных товаров, тогда Вам необходимо уведомить об этом администрацию сайта</b>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Передумал</button>
                        <?= Html::a('Уведомить', URL::to(['profile/member/becomeprovider']),['class'=>'btn btn-primary']); ?>
                    </div>
                </div>

            </div>
        </div>
            <div class="wrap">
<!--                <div class="top-season-decor"></div>-->
                <?= $this->renderFile('@app/modules/site/views/layouts/snippets/top-nav.php', [
                    'cart' => new Cart(),
                ]) ?>
                <div class="container">
                    <div class="row site-page">
                        <div class="col-md-2">
                            <?= $this->renderFile('@app/modules/site/views/layouts/snippets/menu-panel.php', [
                                'heading' => Icon::show('thumbs-o-up') . ' Рекомендуем',
                                'items' => $recomendations,
                            ]) ?>
                            <?= $this->renderFile('@app/modules/site/views/layouts/snippets/menu-panel.php', [
                                'heading' => Icon::show('list') . ' В наличии',
                                'items' => $catalogue,
                            ]) ?>
                            <?= $this->renderFile('@app/modules/site/views/layouts/snippets/menu-panel.php', [
                                'heading' => Icon::show('calendar') . ' Закупки',
                                'items' => $purchases,
                                'class' => 'menu-purchases',
                            ]) ?>
                        </div>
                        <div class="col-md-10">
                            <?php if (!empty($this->params['breadcrumbs'])): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Breadcrumbs::widget([
                                            'homeLink' => ['label' => Icon::show('home') . ' Главная', 'url' => Url::to(['/'])],
                                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                            'encodeLabels' => false,
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $content ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!--Показ всплывающего окошка об успехе-->
        <?php if (Yii::$app->session->hasFlash('Успех')){
            echo Alert::widget([
                    'options'=>['class'=>'alert-info'],
                'body'=>Yii::$app->session->getFlash('Успех'),
            ]);
        } ?>


        <!--Показ всплывающего окошка об успехе-->

            <footer class="footer">
                <div class="container">
                    <?= $this->renderFile('@app/modules/site/views/layouts/snippets/bottom-nav.php') ?>
                    <p class="pull-right">&copy; <?= Html::encode(Yii::$app->params['name']) ?> <?= date('Y') ?></p>
                </div>
            </footer>
        <?php $this->endBody() ?>
        <?= $this->renderFile('@app/modules/site/views/layouts/snippets/flash-message.php') ?>
    </body>
</html>
<?php $this->endPage() ?>
