<?php

namespace app\modules\site\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\Html;
use app\models\Category;

class DefaultController extends BaseController
{
    const MAX_MAIN_PAGE_ITEMS = 8;

    public function actionIndex()
    {
        return $this->render('index', [
            'newProducts' => $this->getCategoryProducts(Category::RECENT_SLUG),
            'purchaseProducts' => $this->getCategoryProducts(Category::PURCHASE_SLUG),
            'featuredProducts' => $this->getCategoryProducts(Category::FEATURED_SLUG),
            'services' => $this->getServices(),
        ]);
    }

    protected function getCategoryProducts($slug)
    {
        $category = Category::findOne(['slug' => $slug]);

        if ($category) {
            return $category->getAllProductsQuery()
                ->andWhere('visibility != 0')
                ->andWhere('published != 0')
                ->orderBy('RAND()')
                ->limit(self::MAX_MAIN_PAGE_ITEMS)
                ->all();
        }

        return [];
    }

    protected function getServices()
    {
        $category = Category::findOne(['slug' => Category::SERVICE_SLUG]);

        if ($category) {
            return $category->getAllServicesQuery()
                ->andWhere('visibility != 0')
                ->andWhere('published != 0')
                ->orderBy('RAND()')
                ->limit(self::MAX_MAIN_PAGE_ITEMS)
                ->all();
        }

        return [];
    }
}
