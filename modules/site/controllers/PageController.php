<?php

namespace app\modules\site\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Page;

class PageController extends BaseController
{
    public function actionSlug($slug)
    {
        $model = Page::find()
            ->where('slug = :slug AND visibility != 0', [':slug' => $slug])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
