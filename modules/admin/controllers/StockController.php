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
use app\models\StockHead;
use app\models\StockBody;
use yii\data\SqlDataProvider;
use app\models\Account;
use yii\base\Exception;
use app\models\Email;
use app\models\User;
use kartik\mpdf\Pdf;
use app\models\ProviderStock;
use app\models\UnitContibution;
use yii\helpers\Json;
use app\models\Product;
use app\models\ProductNewPrice;

class StockController extends BaseController
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

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProviderStock::findBySql('SELECT ps.* FROM provider_stock as ps
              INNER JOIN stock_body as body ON ps.stock_body_id=body.id INNER JOIN stock_head as head ON body.stock_head_id=head.id
              ORDER BY head.date DESC')->with(['stock_body']),
            'sort' => false
        ]);
        $model= StockHead::find()->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model'=> $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new StockHead();
        if($model->load(Yii::$app->request->post()) && !empty(Yii::$app->request->post('product_list'))) {
            if($model->save()) {
//                echo '<pre>';
//                var_dump(Yii::$app->request->post('product_list'));
//                die();
                $products = json_decode(Yii::$app->request->post('product_list'));
                foreach($products as $product) {
                    $body = new StockBody();
                    $provider_stock = new ProviderStock();
                    $body->stock_head_id = $model->id;
                    $body->product_id = $product->id;
                    $body->tare = $product->tare;
                    $body->weight = $product->weight;
                    $body->measurement = $product->measurement;
                    $body->count = $product->count;
                    $body->summ = $product->summ;
                    $body->total_summ = $product->total_summ;
                    $body->deposit = $product->deposit;
                    $body->comment = $product->comment;
                   
                    if(!$body->save()) {
                        print_r($body->errors);
                        die();
                    };
                    
                    $provider_stock->stock_body_id=$body->id;
                    $provider_stock->total_rent=$body->count;
                    $provider_stock->total_sum=$provider_stock->total_rent*$body->summ;
                    $provider_stock->reaminder_rent=$provider_stock->total_rent;
                    $provider_stock->summ_reminder=$provider_stock->reaminder_rent*$body->summ;
                    if(!$provider_stock->save()){
                        print_r($provider_stock->errors);
                        die();
                    }

                }
                //Отправка денег от заказчика поставщику если установлена галка "Депозит"
                ////if($body->deposit==1){
                //    print_r($model->who);
                //    die();
                //$user_from=explode(' ',$model->who);
                //$from= new Query();
                
                //$from->select('user.id, user.email')->from('user')->where('user.lastname LIKE(:lastname)',[':lastname'=>$user_from[0]])->andWhere('user.firstname LIKE(:firstname"%")',[':firstname'=>$user_from[1]]);
                //$com=$from->createCommand();
                //$from=$com->queryAll();

                //foreach ($from as $user) {
                //    $user_from_id=$user['id'];
                //    $user_email=$user['email'];
                //}
                
               
                //$user_from_account=Account::findOne(['user_id'=>(int) $user_from_id]);
                
                //                $to=new Query();
                //$to->select('provider.user_id')->from('provider',['INNER JOIN','stock_head','provider.id=stock_head.provider_id'])->where('provider.id=:provider_id',[':provider_id'=>(int) $model->provider_id]);
                //$comm=$to->createCommand();
                //$to=$comm->queryAll();

                //foreach ($to as $user) {
                //    $user_to_id=implode($user);
                //}
                
                //$user_to=Account::findOne($user_to_id);
                //$provider=User::findOne((int) $user_to_id);
                //$provider_email=$provider->email;
                
                //if($user_from_account->total<$body->total_summ){
                //    echo 'Мало денег!';
                //    die();
                //}
                //Account::swap($user_from_account, $user_to, $body->total_summ,'С‚РµСЃС‚',false);
                //Email::send('Customer-provide',$user_email);
                //Email::send('provider-customer',$provider_email);
                //}
                $this->redirect(['/admin/stock']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionDelete($id) {
       
        $model=$this->findModel($id);
        $model->deleted_by_admin=1;
        $model->save();
        return $this->redirect(['/admin/provider']);
    }

    protected function findModel($id)
    {
        if (($model = StockHead::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionView($id) {
//        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM stock_body as body INNER JOIN stock_head as head ON body.stock_head_id=head.id WHERE head.provider_id='.$id.'')->queryScalar();
//        $dataProvider = new ActiveDataProvider([
//            'query' => StockBody::find()->where(['stock_head_id'=>$id])
//        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => StockHead::find()->where('provider_id=:id',['id'=>$id])->andWhere('deleted_by_admin !=1')->orderBy('date DESC'),
        ]);
//        $dataProvider = new SqlDataProvider([
//            'sql'=> 'SELECT head.provider_id,body.id, product.name, body.tare,body.measurement,body.weight,body.count, body.summ, body.total_summ, body.deposit, body.comment FROM product ,stock_body as body INNER JOIN stock_head as head ON body.stock_head_id=head.id WHERE head.provider_id= '.$id.' AND head.deleted_by_admin !=1 AND body.product_id=product.id',
//            'totalCount' => $count,
//
//            'pagination' => [
//                'pageSize' => 20,
//            ],
//            ]);
        return $this->render('view', ['dataProvider'=>$dataProvider]);
    }
    public function actionPdf($id){
        $dataProvider= new SqlDataProvider([
            'sql'=>'SELECT product.name, body.tare,body.measurement,body.weight,body.count, body.summ, body.total_summ, body.deposit, body.comment FROM stock_body as body INNER JOIN product ON body.product_id=product.id WHERE body.id= '.$id.'']);
        $content=$this->renderPartial('_print',['dataProvider'=>$dataProvider]);
        $pdf = new Pdf([
            'mode'=>Pdf::MODE_UTF8,
            'format'=>Pdf::FORMAT_A4,
            'orientation'=>Pdf::ORIENT_PORTRAIT,
            'defaultFont'=>'Arial',
            'destination'=>Pdf::DEST_BROWSER,
            'content'=>$content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'cssInline' => '.kv-heading-1{font-size:18px}', 
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetHeader'=>['Поставка товаров'],
                'SetFooter'=>['{PAGENO}'],
            ]
            ]);
        return $pdf->render();
    }

    public function actionUnit($id)
    {
        /*$dataProvider = new ActiveDataProvider([
            'query'=>$stock = UnitContibution::findBySql('SELECT unit.* FROM unit_contibution as unit INNER JOIN provider_stock as ps ON unit.provider_stock_id=ps.id INNER JOIN stock_body as body ON ps.stock_body_id=body.id INNER JOIN stock_head as head  ON body.stock_head_id=head.id WHERE head.provider_id= '.$id.'  AND head.deleted_by_admin=0')
        ]);*/
        
        $dataProvider = ProviderStock::getDepositsByProvider($id, true);
        return $this->render('cont',[
            'dataProvider'=>$dataProvider,
        ]);
    }

    public function actionDeletecon($id, $provider)
    {
        $head = StockHead::findOne($id);
        $head->deleted_by_admin=1;
        $head->save();
        return $this->redirect(['/admin/stock/unit?id=' . $provider]);
    }

    public function actionSearchajax($q)
    {
        $data= Product::find()->where('name LIKE :q',[':q'=>'%'.$q.'%'])->andWhere('visibility != 0')->all();
        $out = [];
        foreach ($data as $d) {
            $out[] = ['text' => $d->name, 'id'=>$d->id];
        }
        echo Json::encode($out);
    }

    public function actionViewbody($id)
    {
        $data_provider = new ActiveDataProvider([
           'query' => StockBody::find()->where('stock_head_id = :id',['id' => $id]),
        ]);
        return $this->render('viewbody',[
            'dataProvider' => $data_provider,
        ]);
    }

    public function actionFilter($from_date, $to_date)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProviderStock::findBySql('SELECT ps.* FROM provider_stock as ps
              INNER JOIN stock_body as body ON ps.stock_body_id=body.id INNER JOIN stock_head as head ON body.stock_head_id=head.id WHERE head.date BETWEEN :date_from AND :date_to
              ORDER BY head.date DESC',[':date_from'=>$from_date, ':date_to'=>$to_date])->with(['stock_body']),
        ]);

        return $this->render('index',[
           'dataProvider'=>$dataProvider,
        ]);
    }
    
    public function actionCnangeDeposit()
    {
        $body = StockBody::findOne($_POST['id']);
        $body->deposit = $_POST['checked'];
        $body->save();
        return true;
    }
    
    public function actionGetProducts()
    {
        $provider_id = $_POST['provider_id'];

	    $products = Product::getProductsByProvider($provider_id)->getModels();
        $data = [];
	    foreach ($products as $k => $val) {
            $data[$val->categoryHasProduct[0]->category->name][$val->id] = $val->name;
        }
        return $this->renderPartial('_products', [
            'data' => $data,
        ]);
    }

    public function actionGetProduct()
    {
        $product_id = $_POST['product_id'];

	    $product = Product::find()->where(['id' => $product_id])->one();
        return $this->renderPartial('_form', [
            'product' => $product,
        ]);
    }


    public function actionAddProduct() 
    {
	    $head = StockHead::find()->where(['who' => $_POST['StockHead']['who'], 'provider_id' => $_POST['StockHead']['provider_id'], 'date' => $_POST['StockHead']['date']])->one();
	    if (!$head) {
		    $head = new StockHead();
		    $head->who = $_POST['StockHead']['who'];
		    $head->date = $_POST['StockHead']['date'];
		    $head->provider_id = $_POST['StockHead']['provider_id'];
		    $head->save();
	    }
	    $product = Product::find()->where(['id' => $_POST['product-id']])->one();
    //	return $head->id;
	    $body = new StockBody();
	    $body->stock_head_id = $head->id;
	    $body->product_id = $product->id;
	    $body->tare = $_POST['tare'];
	    $body->weight = $_POST['weight'];
	    $body->measurement = $_POST['measurement'];
	    $body->count = $_POST['count'];
	    $body->summ = $_POST['summ'];
	    $body->total_summ = $_POST['summ'] * $_POST['count'];
	    $body->deposit = isset($_POST['deposit']) ? 1 : 0;
	    $body->comment = $_POST['comment'];
	    $body->save();
        
        if (isset($_POST['new_price'])) {
            $product_new_price = new ProductNewPrice();
            $product_new_price->product_id = $product->id;
            $product_new_price->price = $_POST['summ'];
            $product_new_price->quantity = $_POST['count'];
            $product_new_price->date = $_POST['StockHead']['date'];
            $product_new_price->save();
            
            /*if (isset($_POST['add_to_avail'])) {
                $product->scenario = 'apply_product';
                $product->inventory += $_POST['count'];
                $product->save();
            }*/
        } else {
            $product->scenario = 'apply_product';
            $product->inventory += $_POST['count'];
            $product->tare = $_POST['tare'];
            $product->weight = $_POST['weight'];
            $product->measurement = $_POST['measurement'];
            $product->save();
        }
        
        $provider_stock = new ProviderStock();
        $provider_stock->stock_body_id = $body->id;
        $provider_stock->total_rent = $body->count;
        $provider_stock->total_sum = $provider_stock->total_rent * $body->summ;
        $provider_stock->reaminder_rent = $provider_stock->total_rent;
        $provider_stock->summ_reminder = $provider_stock->reaminder_rent * $body->summ;
        $provider_stock->summ_on_deposit = 0;
        $provider_stock->save();
	    
        $dataProvider = new ActiveDataProvider([
           'query' => StockBody::find()->where('stock_head_id = :id',['id' => $head->id]),
           'sort' => false,
        ]);
        
        return $this->renderPartial('_print', [
            'dataProvider' => $dataProvider,
        ]);
    }
}