<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use app\models\Category;
use app\models\Product;
use app\models\Photo;
use app\models\ProductHasPhoto;
use app\models\ProviderHasCategory;
use app\models\Provider;
use app\models\Fund;
use app\models\FundProduct;
use app\models\FundCommonPrice;
use app\models\ProductPrice;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->get();
        $category_id = isset($get['category_id']) ? $get['category_id'] : 'all';
        $categories = ArrayHelper::merge(
            [
                'all' => '&ndash; Все товары &ndash;',
                'none' => '&ndash; Товары без категорий &ndash;',
            ],
            Category::getSelectTree()
        );

        if (is_numeric($category_id)) {
            $category = Category::findOne(['id' => $category_id]);
            if ($category) {
                $query = $category->getAllProductsQuery();
            } else {
                $query = Product::find()->where('FALSE');
            }
        } elseif ($category_id == 'none') {
            $query = Product::find();
            $query->joinWith('categories')
                ->where('{{%category}}.id IS NULL');
        } else {
            $query = Product::find();
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'category_id' => $category_id,
            'categories' => $categories,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            //'model' => $this->findModel($id),
            'model' => Product::getProductModelById($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($provider_id = '')
    {
        if (!empty($provider_id)) {
            $model = new Product(['visibility' => 1, 'published' => 0, 'only_member_purchase' => 0, 'auto_send' => 1, 'provider_id' => $provider_id]);
            $provider = Provider::find()->where(['id' => $provider_id])->with('user')->one();
            $categories = ProviderHasCategory::getCategoriesByProvider($provider_id);
        } else {
            $model = new Product(['visibility' => 1, 'published' => 0, 'only_member_purchase' => 0, 'auto_send' => 1]);
            $provider = [];
            $categories = [];
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $gallery = UploadedFile::getInstances($model, 'gallery');
            foreach ($gallery as $file) {
                $photo = Photo::createPhoto(
                    Product::MAX_GALLERY_IMAGE_SIZE,
                    Product::MAX_GALLERY_THUMB_WIDTH,
                    Product::MAX_GALLERY_THUMB_HEIGHT,
                    $file->tempName
                );
                $productHasPhoto = new ProductHasPhoto();
                $productHasPhoto->photo_id = $photo->id;
                $model->link('productHasPhoto', $productHasPhoto);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->render('create', [
            'model' => $model,
            'provider' => $provider,
            'categories' => $categories,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //$model = $this->findModel($id);
        $model = Product::getProductModelById($id);
        $model_fund = Fund::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $gallery = UploadedFile::getInstances($model, 'gallery');
            foreach ($gallery as $file) {
                $photo = Photo::createPhoto(
                    Product::MAX_GALLERY_IMAGE_SIZE,
                    Product::MAX_GALLERY_THUMB_WIDTH,
                    Product::MAX_GALLERY_THUMB_HEIGHT,
                    $file->tempName
                );
                $productHasPhoto = new ProductHasPhoto();
                $productHasPhoto->photo_id = $photo->id;
                $model->link('productHasPhoto', $productHasPhoto);
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'model_fund' => $model_fund,
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetCategories()
    {
        $provider_id = $_POST['provider_id'];
        
        $categories = ProviderHasCategory::getCategoriesByProvider($provider_id);
        
        return $this->renderPartial('_categories', [
            'categories' => $categories,
        ]);
    }
    
    public function actionProvider($id)
    {
        $provider = Provider::findOne($id);
        $dataProvider = Product::getProductsByProvider($id);
        
        return $this->render('provider', [
            'provider' => $provider,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionGetFund()
    {
        $feature_id = $_POST['id'];
        $funds = FundProduct::find()->where(['product_feature_id' => $feature_id])->all();
        $res = [];
        if ($funds) {
            foreach ($funds as $fund) {
                $res[] = [
                    $fund->fund_id => $fund->percent
                ];
            }
            
        } else {
            $funds = Fund::find()->all();
            foreach ($funds as $fund) {
                $res[] = [
                    $fund->id => $fund->percent
                ];
            }
        }
        return json_encode($res);
    }
    
    public function actionGetCommonPrice()
    {
        $feature_id = $_POST['id'];
        $common_price = ProductPrice::find()->where(['product_feature_id' => $feature_id])->one();
        return $common_price->price;
    }
    
    public function actionSetPercent()
    {
        $feature_id = $_POST['f_id'];
        $fund_id = $_POST['fund_id'];
        $percent = $_POST['percent'];
        
        $fund = FundProduct::find()->where(['product_feature_id' => $feature_id, 'fund_id' => $fund_id])->one();
        if ($fund) {
            $fund->percent = $percent;
            $fund->save();
        } else {
            $fund_common = Fund::findOne($fund_id);
            if ($fund_common->percent != $percent) {
                $fund = new FundProduct();
                $fund->product_feature_id = $feature_id;
                $fund->fund_id = $fund_id;
                $fund->percent = $percent;
                $fund->save();
            }
        }
        return true;
    }
    
    public function actionGetPrices()
    {
        $feature_id = $_POST['f_id'];
        $product_price = ProductPrice::find()->where(['product_feature_id' => $feature_id])->one();
        $res = [
             'price' => $product_price->price,
             'member_price' => $product_price->member_price
        ];
        
        return json_encode($res);
    }
    
    public function actionSetCommonPrice()
    {
        $feature_id = $_POST['f_id'];
        $price = $_POST['price'];
        $common_price = FundCommonPrice::find()->where(['product_feature_id' => $feature_id])->one();
        $product_price = ProductPrice::find()->where(['product_feature_id' => $feature_id])->one();
        if ($common_price) {
            if ($common_price->price != $price) {
                $common_price->price = $price;
                if ($common_price->save()) {
                    $product_price->price = $price;
                    $product_price->save();
                }
            }
        } else {
            if ($product_price->price != $price) {
                $common_price = new FundCommonPrice();
                $common_price->product_feature_id = $feature_id;
                $common_price->price = $price;
                if ($common_price->save()) {
                    $product_price->price = $price;
                    $product_price->save();
                }
            }
        }
    }
}
