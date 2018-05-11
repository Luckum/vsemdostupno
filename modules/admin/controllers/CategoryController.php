<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\models\Category;
use app\models\Photo;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'update-structure' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $items = Category::getFancyCategories();
        return $this->render('index', [
            'items' => $items,
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category(['order' => 0]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $files = UploadedFile::getInstances($model, 'photo');
            if ($files) {
                $photo = Photo::createPhoto(
                    Category::MAX_IMAGE_SIZE,
                    Category::MAX_THUMB_WIDTH,
                    Category::MAX_THUMB_HEIGHT,
                    $files[0]->tempName
                );
                $model->photo_id = $photo->id;
            }
            $model->saveNode();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $files = UploadedFile::getInstances($model, 'photo');
            if ($files) {
                if ($model->photo) {
                    $model->photo->updatePhoto(
                        Category::MAX_IMAGE_SIZE,
                        Category::MAX_THUMB_WIDTH,
                        Category::MAX_THUMB_HEIGHT,
                        $files[0]->tempName
                    );
                } else {
                    $photo = Photo::createPhoto(
                        Category::MAX_IMAGE_SIZE,
                        Category::MAX_THUMB_WIDTH,
                        Category::MAX_THUMB_HEIGHT,
                        $files[0]->tempName
                    );
                    $model->photo_id = $photo->id;
                }
            }
            $model->saveNode();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateStructure()
    {
        $post = Yii::$app->request->post();
        $data = Json::decode($post['data']);

        foreach ($data as $item) {
            $node = Category::findOne($item['id']);
            if (!$node->isRoot()) {
                $node->moveAsRoot();
            }

            if (isset($item['children'])) {
                $moveNode = function($parentId, $children) use (&$moveNode) {
                    $parent = Category::findOne($parentId);

                    foreach ($children as $child) {
                        $node = Category::findOne($child['id']);

                        $nodeParent = $node->parent()->one();
                        if (!$nodeParent || $parent->id != $nodeParent->id) {
                            $node->moveAsFirst($parent);
                        }

                        if (isset($child['children'])) {
                            $moveNode($child['id'], $child['children']);
                        }
                    }
                };

                $moveNode($item['id'], $item['children']);
            }
        }

        return $this->redirect(['index']);
    }
    
    public function actionGetChecked()
    {
        $res = [];
        $cat = Category::find()->where(['for_reg' => 1])->all();
        if ($cat) {
            foreach ($cat as $val) {
                $res[] = $val->id;
            }
        }
        return json_encode($res);
    }
    
    public function actionChangeForReg()
    {
        $checked = $_POST['checked'];
        $id = $_POST['id'];
        $cat = Category::findOne($id);
        $cat->for_reg = $checked;
        $cat->save();
        return true;
    }
    
    public function actionUpdateCollapsed()
    {
        $id = $_POST['id'];
        $value = $_POST['value'];
        $cat = Category::findOne($id);
        $cat->collapsed = $value;
        $cat->save();
        return true;
    }
    
    public function actionSaveCategory()
    {
        $id = $_POST['id'];
        $parent_id = $_POST['parent_id'];
        
        $category = Category::findOne($id);
        $category->parent = $parent_id;
        $category->save();
    }
    
    public function actionAddCategory()
    {
        $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
        $name = $_POST['title'];
        if (!empty($name)) {
            $category = new Category;
            $category->name = $name;
            $category->parent = $parent_id;
            $category->order = 0;
            $category->save();
        }
    }
}
