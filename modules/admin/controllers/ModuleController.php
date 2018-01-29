<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\Module;
use yii\bootstrap\Nav;

class ModuleController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                if (!in_array(Yii::$app->user->identity->role, [User::ROLE_SUPERADMIN])) {
                                    throw new ForbiddenHttpException('Действие не разрешено.');
                                }
                                return true;
                            },
                        ],
                    ],
                ],
            ],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post']
                    ]
                ]
            ]
        );
    }
    
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Module::find()
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionUpdateState()
    {
        $post = Yii::$app->request->post();
        $model = Module::findOne($post['id']);
        
        $model->state = $post['state'];
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $model->save(),
        ];
    }
}