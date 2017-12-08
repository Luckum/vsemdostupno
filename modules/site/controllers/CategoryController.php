<?php

namespace app\modules\site\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Category;

class CategoryController extends BaseController
{
    public function actionIndex($id)
    {
        $model = Category::find()
            ->where('visibility != 0')
            ->andWhere('id = :id OR slug = :slug', [':id' => $id, ':slug' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        if ($model->slug && $model->slug != $id) {
            return $this->redirect($model->url);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
