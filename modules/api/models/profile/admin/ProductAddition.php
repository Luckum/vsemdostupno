<?php

namespace app\modules\api\models\profile\admin;

use Yii;
use yii\base\Model;

/**
 * This is the model class for product addition.
 *
 */
class ProductAddition extends Model
{
    public $user_id;
    public $product_id;
    public $quantity;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'product_id', 'quantity'], 'required'],
            [['user_id', 'product_id', 'quantity'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Идентификатор пользователя',
            'product_id' => 'Идентификатор товара',
            'quantity' => 'Количество',
        ];
    }
}
