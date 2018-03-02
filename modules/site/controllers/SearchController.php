<?php
namespace app\modules\site\controllers;

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

class SearchController extends BaseController
{


    public function actionIndex()
    {
        echo'OK!';
        die();

    }
    public function actionSearch() {
        $fio = $_GET['fio'];
        //Делаем выборку на название компаний чтобы в дальнейшем выводить результат где совпадают название компаний
        $id=$_GET['id'];
        $company_name= new Query();
        $company_name->select('name')->from('partner')->where('partner.user_id=:id',[':id'=>$id]);
        $com = $company_name->createCommand();
        $company_name=$com->queryAll();
        foreach ($company_name as $item) {
            $name_company=implode(',',$item);
        }

        $pieces = explode('"',$name_company);

        $discount_number=$_GET['reg_Nom'];
        $order_number = $_GET['nomer_order'];
        if ($fio!=null && $discount_number==null && $order_number==null)
        {
            $fio = str_replace('  ', ' ', trim($fio));
            $fio=explode(' ',$fio);
            $query = new Query();
            $query->select('user.id')
                ->from('user', ['INNER JOIN', 'member', 'user.id=member.user_id'], ['INNER JOIN', 'partner', 'user.id=partner.user_id'])
                ->Where('user.lastname=:p1', [':p1' => $fio[0]])
                ->andWhere('user.firstname=:p2',[':p2'=>$fio[1]])
                ->andWhere('role != "admin"')
                ->andWhere('role != "superadmin"');
        $command = $query->createCommand();
        $query = $command->queryAll();

        $sub_array=array();
        foreach ($query as $item) {
            $sub_array[]=$item['id'];
             }
        $res_sql=implode(',',$sub_array);
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM user WHERE user.id IN ('.$res_sql.')')->queryScalar();
        $dataProvider = new SqlDataProvider([
            'sql' => 'SELECT u.id as user_id, u.role, u.email, u.phone, u.firstname, u.lastname, u.patronymic, u.number, m.id as member_id, p.id as partner_id, p.name from user u left join member m on u.id = m.user_id left join partner p on (u.id = p.user_id OR m.partner_id = p.id) where u.id in ('.$res_sql.') AND u.role="member" AND p.name LIKE "%'.$pieces[0].'%"',
            
            'totalCount' => $count,

            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
         }   
         if($fio==null && $discount_number!=null && $order_number==null){
            $count = Yii::$app->db
                ->createCommand('SELECT COUNT(*) from user WHERE user.number='.$discount_number.'')
                ->queryScalar();
            $dataProvider= new SqlDataProvider ([
                'sql'=>'SELECT u.id as user_id, u.role, u.email, u.phone, u.firstname, u.lastname, u.number, m.id as member_id, p.id as partner_id, p.name from user u left join member m on u.id = m.user_id left join partner p on (u.id = p.user_id OR m.partner_id = p.id) where u.number = ('.$discount_number.') AND u.role="member" AND p.name LIKE "%'.$pieces[0].'%"',
                'totalCount'=>$count,
                'pagination'=> [
                    'pageSize'=>10,
                    ],
                ]);
            
         }
         if($fio==null && $discount_number==null && $order_number!=null){
            
            $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->where('partner_id = :partner_id', [':partner_id' => $this->identity->entity->partner->id])->andWhere('id=:id',[':id'=>$order_number]),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
            return $this->render('order', [
            'dataProvider' => $dataProvider,
        ]);
         }
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSearchajax($name=null, $disc_number=null, $order_numb=null) {
     if ($name !=null){
     $query = new Query;
     $query->select('lastname, firstname, patronymic')
        ->distinct(true)
        ->from('user')
        ->where('lastname LIKE "%' . $name .'%"')
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
    
    if ($disc_number !=null){
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
    
    if ($order_numb !=null){
     $query = new Query;
     $query->select('id')
        ->from('order')
        ->where('id LIKE "%' . $order_numb .'%"')
        ->andWhere('role != "admin"')
        ->andWhere('role != "superadmin"')
        ->orderBy('id');
    $command = $query->createCommand();
    $data = $command->queryAll();
    $out = [];
    foreach ($data as $d) {
        $out[] = ['value' => $d['id']];
    }
    echo Json::encode($out);
    }
    
}
}
?>