<?php
namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\base\Exception;
use app\models\Email;
use app\models\Order;
use app\models\User;
use app\models\Member;
use app\models\Partner;
use yii\db\Query;
use app\models\Product;
use app\models\OrderHasProduct;
use app\models\Template;
use app\models\OrderStatus;
use app\models\Account;
use app\modules\admin\models\OrderForm;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\helpers\Json;

use app\modules\purchase\models\PurchaseOrder;

class SearchController extends BaseController
{


    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find(),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);


    }
    
    public function actionSearch()
    {
        $fio = $_GET['fio'];
        $discount_number = isset($_GET['reg_Nom']) ? $_GET['reg_Nom'] : null;
        $order_number = isset($_GET['nomer_order']) ? $_GET['nomer_order'] : null;
        $purchase_order_number = isset($_GET['purchase_order_number']) ? $_GET['purchase_order_number'] : null;
        if ($fio != null && $discount_number == null && $order_number == null && $purchase_order_number == null) {
            $fio = str_replace('  ', ' ', trim($fio));
            $temp_fio = explode(' ',$fio);
            $query = new Query();
            $query->select('user.id')
                ->from('user', ['INNER JOIN', 'member', 'user.id=member.user_id'], ['INNER JOIN', 'partner', 'user.id=partner.user_id'])
                ->where('user.lastname=:p1', [':p1' => $temp_fio[0]])
                ->andWhere('user.firstname=:p2',[':p2' => isset($temp_fio[1]) ? $temp_fio[1] : ""])
                ->andWhere('role != "admin"')
                ->andWhere('role != "superadmin"');
            $command = $query->createCommand();
            $query = $command->queryAll();
            $res_query= new Query();
            $sub_array=array();
            if ($query) {
                foreach ($query as $item) {
                    $sub_array[] = $item['id'];
                }
                $res_sql = implode(',', $sub_array);
                $count = Yii::$app->db
                    ->createCommand('SELECT COUNT(*) FROM user WHERE user.id IN ('.$res_sql.')')
                    ->queryScalar();
                $dataProvider = new SqlDataProvider([
                    'sql' => 'SELECT u.id as user_id, u.role, u.email, u.phone, u.firstname, u.lastname, u.patronymic, u.number, m.id as member_id, p.id as partner_id, p.name from user u left join member m on u.id = m.user_id left join partner p on (u.id = p.user_id OR m.partner_id = p.id) where role != "admin" AND role != "superadmin" AND u.id in ('.$res_sql.')',
                    'totalCount' => $count,
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);
            } else {
                $dataProvider = new ActiveDataProvider([
                    'models' => [],
                ]);
            }
        }   
        if ($fio==null && $discount_number!=null && $order_number==null  && $purchase_order_number == null) {
            $count= Yii::$app->db->createCommand('SELECT COUNT(*) from user WHERE user.number='.$discount_number.'')->queryScalar();
            $dataProvider= new SqlDataProvider ([
                'sql'=>'SELECT u.id as user_id, u.role, u.email, u.phone, u.firstname, u.lastname, u.number, m.id as member_id, p.id as partner_id, p.name from user u left join member m on u.id = m.user_id left join partner p on (u.id = p.user_id OR m.partner_id = p.id) where role != "admin" AND role != "superadmin" AND u.number = ('.$discount_number.')',
                'totalCount'=>$count,
                'pagination'=> [
                    'pageSize'=>10,
                ],
            ]);
        }
        if ($fio==null && $discount_number==null && $order_number!=null  && $purchase_order_number == null) {
            $dataProvider = new ActiveDataProvider([
                'query' => Order::find()->where('order_id = :id', [':id' => $order_number]),
            ]);
            return $this->render('order', [
                'dataProvider' => $dataProvider,
            ]);
        }
        
        if ($fio == null && $discount_number == null && $order_number == null && $purchase_order_number != null) {
            $dataProvider = new ActiveDataProvider([
                'query' => PurchaseOrder::find()->where('order_number = :id', [':id' => $purchase_order_number]),
            ]);
            return $this->render('purchase', [
                'dataProvider' => $dataProvider,
            ]);
        }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSearchajax($name=null, $disc_number=null, $order_numb=null, $purchase_order_number = null)
    {
        if ($name !=null) {
            $temp_name = explode(' ', $name);
            $query = new Query;
            $query->select('lastname, firstname, patronymic')
                ->distinct(true)
                ->from('user')
                ->where('lastname LIKE "%' . $temp_name[0] .'%"')
                ->andWhere('role != "admin"')
                ->andWhere('role != "superadmin"')
                ->orderBy('lastname');
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out = [];
            foreach ($data as $d) {
                $out[] = ['value' => $d['lastname']. ' ' .$d['firstname']. ' ' .$d['patronymic']];
            }
            echo Json::encode($out);
        }

        if ($disc_number != null) {
            $query = new Query;
            $query->select('number')
                ->from('user')
                ->where('number LIKE "%' . $disc_number .'%"')
                ->andWhere('role != "admin"')
                ->andWhere('role != "superadmin"')
                ->orderBy('number');
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out = [];
            foreach ($data as $d) {
                $out[] = ['value' => $d['number']];
            }
            echo Json::encode($out);
        }

        if ($order_numb != null) {
            $query = new Query;
            $query->select(['LPAD(`order_id`, 5, "0") as `order_id`'])
                ->from('order')
                ->where('LPAD(order_id, 5, "0") LIKE "%' . $order_numb .'%"')
                ->orderBy('order_id');
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out = [];
            foreach ($data as $d) {
                $out[] = ['value' => $d['order_id']];
            }
            echo Json::encode($out);
        }
        
        if ($purchase_order_number != null) {
            $query = new Query;
            $query->select(['order_number'])
                ->from('purchase_order')
                ->where('order_number LIKE "%' . $purchase_order_number .'%"')
                ->orderBy('order_number');
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out = [];
            foreach ($data as $d) {
                $out[] = ['value' => $d['order_number']];
            }
            echo Json::encode($out);
        }
    }
}
