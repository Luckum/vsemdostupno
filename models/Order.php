<?php

namespace app\models;

use Yii;
use app\models\Account;
use app\models\Provider;
use app\models\Fund;
use app\models\ProductFeature;
use app\models\ProviderStock;
use app\models\StockBody;
use yii\db\Query;
use yii\data\SqlDataProvider;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property string $created_at
 * @property integer $city_id
 * @property integer $partner_id
 * @property integer $user_id
 * @property string $role
 * @property string $city_name
 * @property string $partner_name
 * @property string $email
 * @property string $phone
 * @property string $firstname
 * @property string $lastname
 * @property string $patronymic
 * @property string $address
 * @property string $total
 * @property string $comment
 * @property string $paid_total
 * @property integer $order_status_id
 * @property integer $hide
 *
 * @property User $user
 * @property City $city
 * @property Partner $partner
 * @property OrderHasProduct[] $orderHasProducts
 * @property string $formattedTotal
 * @property string $fullName
 * @property string $shortName
 * @property string $htmlFormattedInformation
 * @property string $htmlEmailFormattedInformation
 * @property string $partnerName
 * @property OrderStatus $orderStatus
 * @property OrderHasProduct[] $purchaseOrderHasProducts
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['city_id', 'partner_id', 'user_id', 'order_status_id', 'hide'], 'integer'],
            [['city_name', 'email', 'phone', 'firstname', 'lastname', 'patronymic', 'total', 'order_status_id'], 'required'],
            [['role', 'address', 'comment'], 'string'],
            [['total', 'paid_total'], 'number'],
            [['city_name', 'partner_name', 'email', 'phone', 'firstname', 'lastname', 'patronymic'], 'string', 'max' => 255],
            [['order_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['order_status_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'created_at' => 'Дата и время создания',
            'city_id' => 'Идентификатор города',
            'partner_id' => 'Идентификатор партнера',
            'user_id' => 'Идентификатор пользователя',
            'role' => 'Роль',
            'city_name' => 'Название города',
            'partner_name' => 'Название партнера',
            'email' => 'Емайл',
            'phone' => 'Телефон',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'address' => 'Адрес доставки',
            'total' => 'Стоимость',
            'formattedTotal' => 'Стоимость',
            'comment' => 'Комментарий',
            'paid_total' => 'Оплаченная стоимость',
            'order_status_id' => 'Идентификатор статуса',
            'fullName' => 'ФИО',
            'shortName' => 'ФИО',
            'htmlFormattedInformation' => 'Информация',
            'htmlEmailFormattedInformation' => 'Информация',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderHasProducts()
    {
        return $this->hasMany(OrderHasProduct::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'order_status_id']);
    }

    public function getFormattedTotal()
    {
        return Yii::$app->formatter->asCurrency($this->total, 'RUB');
    }

    public function getFullName()
    {
        return implode(' ', [$this->lastname, $this->firstname, $this->patronymic]);
    }

    public function getShortName()
    {
        return sprintf(
            '%s %s. %s.',
            $this->lastname,
            mb_substr($this->firstname, 0, 1, Yii::$app->charset),
            mb_substr($this->patronymic, 0, 1, Yii::$app->charset)
        );
    }

    public function getHtmlFormattedInformation()
    {
        return Yii::$app->view->renderFile('@app/modules/site/views/order/snippets/information.php', [
            'model' => $this,
        ]);
    }

    public function getHtmlEmailFormattedInformation()
    {
        return Yii::$app->view->renderFile('@app/modules/site/views/order/snippets/email-information.php', [
            'model' => $this,
        ]);
    }

    public function getPartnerName()
    {
        if ($this->partner) {
            return $this->partner->name;
        } elseif ($this->user) {
            return $this->user->partner->name;
        }

        return '';
    }

    public function getPurchaseOrderHasProducts()
    {
        return $this->hasMany(OrderHasProduct::className(), ['order_id' => 'id'])
            ->where('UNIX_TIMESTAMP({{%order_has_product}}.order_timestamp) > 0 AND {{%order_has_product}}.purchase = 0');
    }

    public function canCompleted()
    {
        unset($this->purchaseOrderHasProducts);

        return !$this->purchaseOrderHasProducts;
    }

    public function getProductPriceTotal($priceName)
    {
        $total = 0;

        foreach ($this->orderHasProducts as $orderHasProduct) {
            $total += $orderHasProduct->quantity *
                (isset($orderHasProduct->$priceName) ? $orderHasProduct->$priceName : $orderHasProduct->product->$priceName);
        }

        return $total;
    }

    public function getUnitContibution()
    {
        return $this->hasOne(UnitContibution::className(),['order_id'=>'id']);
    }
    
    public static function getProvidersOrder($dateStart, $dateEnd, $isPurchase = -1, $deleted = 0)
    {
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM `order` WHERE created_at BETWEEN "' . $dateStart . '" AND "' . $dateEnd . '"')->queryScalar();
        $count = 0;
        $where = $isPurchase == -1 ? '1' : 'ohp.purchase = ' . $isPurchase;
        $whereD = $deleted == -1 ? '1' : 'ohp.deleted = ' . $deleted;
        $dataProvider = new SqlDataProvider([
            'sql' => 'SELECT pr.id,
                            ohp.id AS ohp_id, 
                            o.partner_id AS pid, 
                            o.partner_name,
                            ohp.quantity, 
                            ohp.name AS product_name,
                            ohp.total,
                            p.id AS provider_id,
                            p.name AS provider_name,
                            ohp.price,
                            ohp.product_feature_id,
                            COUNT(ohp.product_feature_id) AS row_cnt,
                            SUM(ohp.quantity) AS total_qnt,
                            SUM(ohp.total) AS total_price,
                            CONCAT(pf.tare, ", ", pf.volume, " ", pf.measurement) AS product_feature_name
                        FROM `order` o
                        LEFT JOIN `order_has_product` ohp ON o.id = ohp.order_id
                        LEFT JOIN `provider` p ON ohp.provider_id = p.id
                        LEFT JOIN `product` pr ON ohp.product_id = pr.id
                        LEFT JOIN `product_feature` pf ON ohp.product_feature_id = pf.id
                        WHERE `o`.created_at BETWEEN "' . $dateStart . '" AND "' . $dateEnd . '"
                            AND ' . $whereD . '
                            AND ' . $where . '
                        GROUP BY product_feature_id',
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => '20',
            ],
        ]);
        
        return $dataProvider;
    }
    public static function getOrderByProduct($product, $date)
    {
        $query = new Query;
        $query->select([
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id',
                'IF (order.partner_name IS NULL, partner.name, order.partner_name) AS p_name',
                'SUM(order_has_product.quantity) AS quantity',
                'SUM(order_has_product.total) AS total'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['order_has_product.product_feature_id' => $product])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']])
            ->groupBy('p_name');
        
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getProviderOrderDetails($product_id, $date, $partner_id)
    {
        $query = new Query;
        $query->select([
                'order.id',
                'CONCAT(order.lastname, " ", order.firstname, " ", order.patronymic) AS fio',
                'order_has_product.quantity',
                'order_has_product.price',
                'order_has_product.total',
                'order_has_product.product_feature_id',
                'order_has_product.name',
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['order_has_product.product_feature_id' => $product_id])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']])
            ->having(['p_id' => $partner_id]);
        
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getProviderOrderByPartner($partner_id, $date, $isPurchase = -1)
    {
        $where = $isPurchase == -1 ? '1' : 'order_has_product.purchase = ' . $isPurchase;
        $query = new Query;
        $query->select([
                'order_has_product.product_id',
                'order_has_product.name AS product_name',
                'order_has_product.product_feature_id',
                'provider.name AS provider_name',
                'provider.id AS provider_id',
                'SUM(order_has_product.quantity) AS quantity',
                'SUM(order_has_product.total) AS total',
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id',
                'CONCAT(product_feature.tare, ", ", product_feature.volume, " ", product_feature.measurement) AS product_feature_name',
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->join('LEFT JOIN', 'provider', 'order_has_product.provider_id=provider.id')
            ->join('LEFT JOIN', 'product_feature', 'order_has_product.product_feature_id=product_feature.id')
            ->where(['between', 'created_at', $date['start'], $date['end']])
            ->andWhere($where)
            ->groupBy('product_feature_id, p_id')
            ->having(['p_id' => $partner_id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        return $dataProvider;
    }
    public static function getProviderIdByDate($date, $isPurchase = -1)
    {
        $where = $isPurchase == -1 ? '1' : 'order_has_product.purchase = ' . $isPurchase;
        $query = new Query;
        $query->select([
                'DISTINCT(order_has_product.provider_id)'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->where(['between', 'created_at', $date['start'], $date['end']])
            ->andWhere($where);
            
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getPartnerIdByProvider($date, $provider_id, $isPurchase = -1)
    {
        $where = $isPurchase == -1 ? '1' : 'order_has_product.purchase = ' . $isPurchase;
        $query = new Query;
        $query->select([
                'DISTINCT(IF (order.partner_id IS NULL, partner.id, order.partner_id)) AS partner_id',
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['order_has_product.provider_id' => $provider_id])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']])
            ->andWhere($where);
        
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getOrderDetailsByProviderPartner($date, $provider_id, $partner_id, $isPurchase = -1)
    {
        $where = $isPurchase == -1 ? '1' : 'order_has_product.purchase = ' . $isPurchase;
        $query = new Query;
        $query->select([
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id',
                'order_has_product.name AS product_name',
                'order_has_product.product_id',
                'order_has_product.product_feature_id AS product_feature',
                'CONCAT(product_feature.tare, ", ", product_feature.volume, " ", product_feature.measurement) AS product_feature_name',
                'SUM(order_has_product.quantity) AS quantity',
                'SUM(order_has_product.total) AS total'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->join('LEFT JOIN', 'product', 'order_has_product.product_id=product.id')
            ->join('LEFT JOIN', 'product_feature', 'order_has_product.product_feature_id=product_feature.id')
            ->where(['order_has_product.provider_id' => $provider_id])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']])
            ->andWhere(['product.auto_send' => '1'])
            ->andWhere($where)
            ->groupBy('product_feature, p_id')
            ->having(['p_id' => $partner_id]);
            
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getPartnerIdByDate($date, $isPurchase = -1)
    {
        $where = $isPurchase == -1 ? '1' : 'order_has_product.purchase = ' . $isPurchase;
        $query = new Query;
        $query->select([
                'DISTINCT(IF (order.partner_id IS NULL, partner.id, order.partner_id)) AS partner_id',
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['between', 'created_at', $date['start'], $date['end']])
            ->andWhere($where);
        
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getOrdersDate()
    {
        $query = new Query;
        $query->select([
                'DATE_FORMAT(created_at, "%Y-%m-%d") AS order_date'
            ])
            ->from('order')
            ->where(['hide' => 0])
            ->groupBy('order_date')
            ->orderBy('order_date DESC');
            
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function hideOrdersByDate($date)
    {
        //return self::updateAll(['hide' => 1], 'created_at BETWEEN "' . $date['start'] . '" AND "' . $date['end'] . '"');
        return self::deleteAll('created_at BETWEEN "' . $date['start'] . '" AND "' . $date['end'] . '"');
    }
    
    public function deleteReturn()
    {
        if ($this->paid_total) {
            $message = sprintf('Возврат за заказ №%d', $this->id);
            Account::swap(null, $this->user->deposit, $this->paid_total, $message);
            $message = sprintf('Возврат заказа №%d', $this->id);
            foreach ($this->orderHasProducts as $product) {
                $provider = Provider::findOne($product->provider_id);
                Fund::setDeductionForOrder($product->product_feature_id, -$product->purchase_price, $product->quantity);
                $feature = ProductFeature::findOne($product->product_feature_id);
                $feature->quantity += $product->quantity;
                $feature->save();
                
                $stock_provider = ProviderStock::getCurrentStockReturn($product->product_feature_id, $product->provider_id);
                if ($stock_provider) {
                    if ($stock_provider->reaminder_rent + $product->quantity <= $stock_provider->total_rent) {
                        $stock_provider->reaminder_rent += $product->quantity;
                        $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                        $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                        $paid_for_provider = $product->quantity * $body->summ;
                        $stock_provider->summ_on_deposit -= $paid_for_provider;
                        $stock_provider->save();
                    } else {
                        $rest = $product->quantity - $stock_provider->total_rent;
                        $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                        $stock_provider->summ_on_deposit = 0;
                        $stock_provider->reaminder_rent = $stock_provider->total_rent;
                        $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                        $stock_provider->save();
                        
                        while ($rest > 0) {
                            $stock_provider = ProviderStock::getCurrentStockReturn($product->product_feature_id, $product->provider_id);
                            
                            if ($stock_provider->reaminder_rent + $rest <= $stock_provider->total_rent) {
                                $stock_provider->reaminder_rent += $rest;
                                $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                                $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                                $paid_for_provider = $rest * $body->summ;
                                $stock_provider->summ_on_deposit -= $paid_for_provider;
                                $stock_provider->save();
                                $rest = 0;
                            } else {
                                $rest -= $stock_provider->total_rent;
                                $body = StockBody::findOne(['id' => $stock_provider->stock_body_id]);
                                $stock_provider->summ_on_deposit = 0;
                                $stock_provider->reaminder_rent = $stock_provider->total_rent;
                                $stock_provider->summ_reminder = $stock_provider->reaminder_rent * $body->summ;
                                $stock_provider->save();
                            }
                        }
                    }
                    if ($body->deposit == 1) {
                        Account::swap(null, $provider->user->deposit, -$product->purchase_price * $product->quantity, $message);
                    }
                }
            }
            $this->delete();
        }
    }
    
    public static function getDetalization($date_s, $date_e, $isPurchase = -1, $hide = 0)
    {
        $where = $isPurchase == -1 ? '1' : 'order_has_product.purchase = ' . $isPurchase;
        
        $query = self::find();
        $query->joinWith('orderHasProducts');
        $query->where(['between', 'created_at', $date_s, $date_e]);
        $query->andWhere(['hide' => $hide]);
        $query->andWhere($where);
            
            
            
        /*$query = new Query;
        $query->select([
                'order.*'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->where(['between', 'created_at', $date_s, $date_e])
            ->andWhere($where);*/
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        return $dataProvider;
    }
}
