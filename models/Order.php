<?php

namespace app\models;

use Yii;
use app\models\Account;
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
            [['city_id', 'partner_id', 'user_id', 'order_status_id'], 'integer'],
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
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->paid_total) {
                $message = sprintf('Возврат за заказ №%d', $this->id);
                if (Account::swap(null, $this->user->deposit, $this->paid_total, $message)) {
                    $this->paid_total = 0;
                    $this->save();
                }
            }

            return true;
        } else {
            return false;
        }
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
    
    public static function getProvidersOrder($dateStart, $dateEnd)
    {
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM `order` WHERE created_at BETWEEN "' . $dateStart . '" AND "' . $dateEnd . '"')->queryScalar();
        $count = 0;
        $dataProvider = new SqlDataProvider([
            'sql' => 'SELECT pr.id, o.partner_id AS pid, o.partner_name, ohp.quantity, ohp.name AS product_name, ohp.total, p.id AS provider_id, p.name AS provider_name, ohp.price, pr.packing, COUNT(ohp.name) AS row_cnt, SUM(ohp.quantity) AS total_qnt, SUM(ohp.total) AS total_price
                        FROM `order` o
                        LEFT JOIN `order_has_product` ohp ON o.id = ohp.order_id
                        LEFT JOIN `provider` p ON ohp.provider_id = p.id
                        LEFT JOIN `product` pr ON ohp.product_id = pr.id
                        WHERE `o`.created_at BETWEEN "' . $dateStart . '" AND "' . $dateEnd . '"
                        GROUP BY product_name',
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
            ->where(['order_has_product.name' => $product])
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
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['order_has_product.product_id' => $product_id])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']])
            ->having(['p_id' => $partner_id]);
        
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getProviderOrderByPartner($partner_id, $date)
    {
        $query = new Query;
        $query->select([
                'order_has_product.product_id',
                'order_has_product.name AS product_name',
                'provider.name AS provider_name',
                'provider.id AS provider_id',
                'SUM(order_has_product.quantity) AS quantity',
                'SUM(order_has_product.total) AS total',
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->join('LEFT JOIN', 'provider', 'order_has_product.provider_id=provider.id')
            ->where(['between', 'created_at', $date['start'], $date['end']])
            ->groupBy('product_name')
            ->having(['p_id' => $partner_id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        return $dataProvider;
    }
    public static function getProviderIdByDate($date)
    {
        $query = new Query;
        $query->select([
                'DISTINCT(order_has_product.provider_id)'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->where(['between', 'created_at', $date['start'], $date['end']]);
            
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getPartnerIdByProvider($date, $provider_id)
    {
        $query = new Query;
        $query->select([
                'DISTINCT(IF (order.partner_id IS NULL, partner.id, order.partner_id)) AS partner_id',
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['order_has_product.provider_id' => $provider_id])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']]);
        
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getOrderDetailsByProviderPartner($date, $provider_id, $partner_id)
    {
        $query = new Query;
        $query->select([
                'IF (order.partner_id IS NULL, partner.id, order.partner_id) AS p_id',
                'order_has_product.name AS product_name',
                'order_has_product.product_id',
                'SUM(order_has_product.quantity) AS quantity',
                'SUM(order_has_product.total) AS total'
            ])
            ->from('order')
            ->join('LEFT JOIN', 'order_has_product', 'order.id=order_has_product.order_id')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->join('LEFT JOIN', 'product', 'order_has_product.product_id=product.id')
            ->where(['order_has_product.provider_id' => $provider_id])
            ->andWhere(['between', 'created_at', $date['start'], $date['end']])
            ->andWhere(['product.auto_send' => '1'])
            ->groupBy('product_name')
            ->having(['p_id' => $partner_id]);
            
        $command = $query->createCommand();
        return $command->queryAll();
    }
    
    public static function getPartnerIdByDate($date)
    {
        $query = new Query;
        $query->select([
                'DISTINCT(IF (order.partner_id IS NULL, partner.id, order.partner_id)) AS partner_id',
            ])
            ->from('order')
            ->join('LEFT JOIN', 'partner', 'order.user_id=partner.user_id')
            ->where(['between', 'created_at', $date['start'], $date['end']]);
        
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
}
