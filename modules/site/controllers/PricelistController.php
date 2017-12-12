<?php

namespace app\modules\site\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\Category;

class PricelistController extends BaseController
{
    public function actionProduct()
    {
        $products = [];
        $productQuery = Product::getPriceList();
        foreach ($productQuery as $product) {
            $products[] = [
                'name' => Category::getCategoryPath($product->categoryHasProduct[0]->category->id) . $product->name,
                //'date' => (new \DateTime($product->stock_date))->format('d.m.Y'),
                'date' => $product->purchaseDate ? (new \DateTime($product->purchaseDate))->format('d.m.Y') : '',
                'inventory' => $product->inventory,
                'price' => $product->price != 0 ? $product->price : '',
                'member_price' => $product->member_price != 0 ? $product->member_price : ''
            ];
        }

        usort($products, function($a, $b){
            return ($a['name'] > $b['name']);
        });
        
        return $this->renderFile('@app/modules/site/views/pricelist/product.php', [
            'productQuery' => $products
        ]);
    }
}
