<?php

namespace app\modules\site\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Service;

class ServiceController extends BaseController
{
    public function actionIndex($id)
    {
        $model = Service::find()
            ->andWhere('id = :id', [':id' => $id])
            ->andWhere('visibility != 0')
            ->andWhere('published != 0')
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
