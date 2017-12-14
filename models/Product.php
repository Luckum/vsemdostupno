<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\base\Exception;
use app\models\User;
use app\models\Provider;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $visibility
 * @property string $name
 * @property string $description
 * @property string $price
 * @property string $member_price
 * @property string $partner_price
 * @property integer $inventory
 * @property string $composition
 * @property string $packing
 * @property string $manufacturer
 * @property string $status
 * @property integer $published
 * @property string $purchase_price
 * @property string $storage_price
 * @property integer $only_member_purchase
 * @property string $expiry_timestamp
 * @property string $weight
 * @property integer $min_inventory
 * @property integer $auto_send
 *
 * @property CategoryHasProduct[] $categoryHasProduct
 * @property ProductHasPhoto[] $productHasPhoto
 * @property string $categoryIds
 * @property Category[] $categories
 * @property double $calculatedPrice
 * @property double $calculatedTotalPrice
 * @property double $calculatedStoragePrice
 * @property double $calculatedInvitePrice
 * @property double $calculatedFraternityPrice
 * @property double $calculatedGroupPrice
 * @property double $calculatedPartnerPrice
 * @property double $calculatedGuestPrice
 * @property double $calculatedMemberPrice
 * @property string $formattedPrice
 * @property string $formattedMemberPrice
 * @property string $formattedPartnerPrice
 * @property string $formattedCalculatedPrice
 * @property string $formattedCalculatedTotalPrice
 * @property string $purchaseCategories
 * @property string $purchaseCategory
 * @property string $orderDate
 * @property string $formattedOrderDate
 * @property string $htmlFormattedOrderDate
 * @property string $purchaseDate
 * @property string $formattedPurchaseDate
 * @property string $htmlFormattedPurchaseDate
 * @property string $url
 * @property integer $currentInventory
 * @property Provider $provider
 * @property Category[] $providerCategories
 */
class Product extends \yii\db\ActiveRecord
{
    public $category_id;
    public $provider_id;
    public $categories = [];
    public $gallery; /* dummy property */
    public $quantity = 1;

    const MAX_FILE_COUNT = 10;
    const MAX_GALLERY_IMAGE_SIZE = 1024;
    const MAX_GALLERY_THUMB_WIDTH = 500;
    const MAX_GALLERY_THUMB_HEIGHT = 500;
    const DEFAULT_IMAGE = '/images/default-image.jpg';
    const DEFAULT_THUMB = '/images/default-thumb.jpg';

    const MAX_INVENTORY = 100;

    const STORAGE_PERCENTS = 33;
    const INVITE_PERCENTS = 6.5;
    const FRATERNITY_PERCENTS = 15.5;
    const GROUP_PERCENTS = 45;
    const GUEST_PERCENTS = 25;
    const MEMBER_PERCENTS = 45;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visibility', 'only_member_purchase', 'min_inventory', 'auto_send'], 'integer'],
            [['inventory'], 'integer', 'min' => 0],
            [['name', 'description', 'price', 'member_price', 'partner_price', 'purchase_price', 'storage_price'], 'required'],
            [['category_id', 'provider_id'], 'required', 'except' => ['apply_product', 'order_product']],
            [['description', 'composition', 'packing', 'manufacturer', 'status'], 'string'],
            [['price', 'member_price', 'partner_price', 'purchase_price', 'storage_price', 'weight'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['tare', 'measurement'], 'string', 'max' => 10],
            [['gallery', 'quantity', 'expiry_timestamp', 'stock_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'visibility' => 'Видимость',
            'name' => 'Название',
            'description' => 'Описание',
            'price' => 'Цена для всех',
            'member_price' => 'Цена для участников',
            'partner_price' => 'Цена для партнеров',
            'inventory' => 'Количество',
            'composition' => 'Состав',
            'packing' => 'Фасовка',
            'manufacturer' => 'Производитель',
            'status' => 'Статус продукта',
            'published' => 'Опубликованный',
            'purchase_price' => 'Закупочная цена',
            'storage_price' => 'Складской сбор',
            'only_member_purchase' => 'Товар для участников',
            'expiry_timestamp' => 'Срок годности',
            'weight' => 'Вес',
            'min_inventory' => 'Минимальный запас',
            'currentInventory' => 'Количество',
            'category_id' => 'Категория',
            'gallery' => 'Фотографии',
            'thumbUrl' => 'Фотография',
            'quantity' => 'Количество',
            'auto_send' => 'Отправление авто заявки поставщику',
            'tare' => 'Тара',
            'measurement' => 'Ед. измерения',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->productHasPhoto as $productHasPhoto) {
                $productHasPhoto->delete();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->member_price == 0) {
                $this->member_price = $this->calculatedMemberPrice;
            }
            if ($this->storage_price == 0) {
                $this->storage_price = $this->calculatedStoragePrice;
            }
            if ($this->partner_price == 0) {
                $this->partner_price = $this->calculatedPartnerPrice;
            }
            if ($this->price == 0) {
                $this->price = $this->calculatedGuestPrice;
            }

            if (!$this->expiry_timestamp) {
                $this->expiry_timestamp = '0000-00-00 00:00:00';
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->isNewRecord) {
            if ($this->category_id != 0) {
                $categoryHasProduct = CategoryHasProduct::findOne(['product_id' => $this->id, 'category_id' => $this->category_id]);
                if (!$categoryHasProduct) {
                    $categoryHasProduct = new CategoryHasProduct();
                    $categoryHasProduct->category_id = $this->category_id;
                    $categoryHasProduct->product_id = $this->id;
                    $categoryHasProduct->save();
                }
            }
            
            $providerHasProduct = ProviderHasProduct::findOne(['product_id' => $this->id, 'provider_id' => $this->provider_id]);
            if (!$providerHasProduct) {
                $providerHasProduct = new ProviderHasProduct();
                $providerHasProduct->provider_id = $this->provider_id;
                $providerHasProduct->product_id = $this->id;
                $providerHasProduct->save();
            }
        } else {
            if ($this->scenario != 'apply_product') {
                if ($this->category_id != 0) {
                    $categoryForDel = CategoryHasProduct::findAll(['product_id' => $this->id]);
                    if ($categoryForDel) {
                        foreach ($categoryForDel as $cat) {
                            $cat->delete();
                        }
                    }
                    $categoryHasProduct = CategoryHasProduct::findOne(['product_id' => $this->id, 'category_id' => $this->category_id]);
                    if (!$categoryHasProduct) {
                        $categoryHasProduct = new CategoryHasProduct();
                        $categoryHasProduct->category_id = $this->category_id;
                        $categoryHasProduct->product_id = $this->id;
                        $categoryHasProduct->save();
                    }
                }
                
                $providerHasProduct = ProviderHasProduct::findOne(['product_id' => $this->id, 'provider_id' => $this->provider_id]);
                if (!$providerHasProduct) {
                    $providerHasProduct = new ProviderHasProduct();
                    $providerHasProduct->provider_id = $this->provider_id;
                    $providerHasProduct->product_id = $this->id;
                    $providerHasProduct->save();
                }
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryHasProduct()
    {
        return $this->hasMany(CategoryHasProduct::className(), ['product_id' => 'id']);
    }
    
    public function getProviderHasProduct()
    {
        return $this->hasMany(ProviderHasProduct::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductHasPhoto()
    {
        return $this->hasMany(ProductHasPhoto::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id'])->viaTable('{{%category_has_product}}', ['product_id' => 'id']);
    }
    
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id'])->viaTable('{{%provider_has_product}}', ['product_id' => 'id']);
    }

    public function deletePhoto($photo)
    {
        if ($photo) {
            $productHasPhoto = ProductHasPhoto::findOne([
                'product_id' => $this->id,
                'photo_id' => $photo->id,
            ]);

            if ($productHasPhoto) {
                $this->unlink('productHasPhoto', $productHasPhoto, true);
                return true;
            }
        }

        return false;
    }

    public function getThumbUrl()
    {
        return $this->productHasPhoto ? $this->productHasPhoto[0]->getThumbUrl() : self::DEFAULT_THUMB;
    }

    public function getImageUrl()
    {
        return $this->productHasPhoto ? $this->productHasPhoto[0]->getImageUrl() : self::DEFAULT_IMAGE;
    }

    public function getPriceByRole($role)
    {
        $prices = [
            User::ROLE_MEMBER => $this->member_price,
            User::ROLE_PROVIDER => $this->member_price,
            User::ROLE_PARTNER => $this->partner_price,
        ];

        return isset($prices[$role]) ? $prices[$role] : $this->price;
    }

    public function getCalculatedMemberPrice()
    {
        $price = round($this->purchase_price + ($this->purchase_price * self::MEMBER_PERCENTS) / 100., 2);

        return $price > 0 ? $price : 0;
    }
    
    public function getCalculatedPrice()
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity->entity;
            return $this->getPriceByRole($user->role);
        }

        return $this->price;
    }

    public function getCalculatedTotalPrice()
    {
        return $this->quantity * $this->calculatedPrice;
    }

    public function getCalculatedStoragePrice()
    {
        $price = round((($this->member_price - $this->purchase_price) * self::STORAGE_PERCENTS) / 100., 2);

        return $price > 0 ? $price : 0;
    }

    public function getCalculatedInvitePrice()
    {
        $price = round((($this->member_price - $this->purchase_price) * self::INVITE_PERCENTS) / 100., 2);

        return $price > 0 ? $price : 0;
    }

    public function getCalculatedFraternityPrice()
    {
        $price = round((($this->member_price - $this->purchase_price) * self::FRATERNITY_PERCENTS) / 100., 2);

        return $price > 0 ? $price : 0;
    }

    public function getCalculatedGroupPrice()
    {
        $price = round((($this->member_price - $this->purchase_price) * self::GROUP_PERCENTS) / 100., 2);

        return $price > 0 ? $price : 0;
    }

    public function getCalculatedPartnerPrice()
    {
        return $this->purchase_price +
            ($this->storage_price > 0 ? $this->storage_price : $this->calculatedStoragePrice) +
            $this->calculatedInvitePrice +
            $this->calculatedFraternityPrice;
    }

    public function getCalculatedGuestPrice()
    {
        $price = $this->member_price +
            round(($this->member_price * self::GUEST_PERCENTS) / 100., 2);

        return $price;
    }

    public function getFormattedPrice()
    {
        return Yii::$app->formatter->asCurrency($this->price, 'RUB');
    }

    public function getFormattedMemberPrice()
    {
        return Yii::$app->formatter->asCurrency($this->member_price, 'RUB');
    }

    public function getFormattedPartnerPrice()
    {
        return Yii::$app->formatter->asCurrency($this->partner_price, 'RUB');
    }

    public function getFormattedCalculatedPrice()
    {
        return Yii::$app->formatter->asCurrency($this->calculatedPrice, 'RUB');
    }

    public function getFormattedCalculatedTotalPrice()
    {
        return Yii::$app->formatter->asCurrency($this->calculatedTotalPrice, 'RUB');
    }

    public function isPurchase()
    {
        foreach ($this->categories as $category) {
            if ($category->isPurchase()) {
                return true;
            }
        }

        return false;
    }

    public function getPurchaseCategories()
    {
        $categories = [];
        foreach ($this->categories as $category) {
            if ($category->isPurchase() && $category->formattedPurchaseDate) {
                $categories[$category->orderDate] = $category;
            }
        }

        ksort($categories);

        return array_values($categories);
    }

    public function getPurchaseCategory()
    {
        $categories = $this->purchaseCategories;

        return $categories ? $categories[0] : null;
    }

    public function getOrderDate()
    {
        return $this->purchaseCategory ? $this->purchaseCategory->orderDate : '';
    }

    public function getFormattedOrderDate()
    {
        return $this->purchaseCategory ? $this->purchaseCategory->formattedOrderDate : '';
    }

    public function getHtmlFormattedOrderDate()
    {
        return $this->purchaseCategory ? $this->purchaseCategory->htmlFormattedOrderDate : '';
    }

    public function getPurchaseDate()
    {
        return $this->purchaseCategory ? $this->purchaseCategory->purchaseDate : '';
    }

    public function getFormattedPurchaseDate()
    {
        return $this->purchaseCategory ? $this->purchaseCategory->formattedPurchaseDate : '';
    }

    public function getHtmlFormattedPurchaseDate()
    {
        return $this->purchaseCategory ? $this->purchaseCategory->htmlFormattedPurchaseDate : '';
    }

    public function getUrl()
    {
        return Url::to(['/product/' . $this->id]);
    }

    public function getCurrentInventory()
    {
        return isset($this->inventory) ? $this->inventory : self::MAX_INVENTORY;
    }

    public function getProviderCategories()
    {
        if ($this->provider && $this->provider->providerHasCategory) {
            return Category::find()
                ->joinWith(['providerHasCategory'])
                ->where('provider_id = :provider_id', [':provider_id' => $this->provider->id])
                ->all();
        }

        return [];
    }

    public function getStock_body()
    {
        return $this->hasOne(StockBody::className(),['product_id'=>'id']);
    }
    
    public static function getProductModelById($id)
    {
        return self::find()->where(['id' => $id])->with('category', 'provider', 'provider.user')->one();
    }
    
    public static function getProductsByProvider($provider_id)
    {
        $query = self::find();
        $query->joinWith('categoryHasProduct');
        $query->joinWith('categoryHasProduct.category');
        $query->joinWith('providerHasProduct')->where(['provider_has_product.provider_id' => $provider_id])->orderBy('category_has_product.category_id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        return $dataProvider;
    }
    
    public function getProviderForView()
    {
        return isset($this->provider) ? ($this->provider->name . ' / ' . $this->provider->user->fullName) : '';
    }
    
    public function getCategoryForView()
    {
        return isset($this->category) ? $this->category->name : '';
    }
    
    public static function getPriceList()
    {
        return self::find()
            //->select('product.*, category.*')
            ->joinWith('categoryHasProduct')
            ->joinWith('categoryHasProduct.category')
            ->where(['product.visibility' => 1, 'product.published' => 1])
            ->andWhere(['<>', 'product.inventory', 0])
            ->all();
    }
}
