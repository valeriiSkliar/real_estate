<?php

namespace app\controllers\admin;

use app\models\Pages;
use app\models\search\PagesSearch;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * PagesController implements the CRUD actions for Pages model.
 */
class PagesController extends AdminController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Pages models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PagesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pages model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Pages model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Pages();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                $model->fileUpload = UploadedFile::getInstance($model, 'fileUpload');
                $model->audioFile = UploadedFile::getInstance($model, 'audioFile');
                $model->videoFile = UploadedFile::getInstance($model, 'videoFile');

                if($model->image) {
                    if (!isset($model->imageFile->name)) {
                        $model->imageFile = null;
                    }
                }
                if ($model->upload()) {
                    return $this->redirect(['index']);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                $model->fileUpload = UploadedFile::getInstance($model, 'fileUpload');
                $model->audioFile = UploadedFile::getInstance($model, 'audioFile');
                $model->videoFile = UploadedFile::getInstance($model, 'videoFile');

                if ($model->image) {
                    if (!isset($model->imageFile->name)) {
                        $model->imageFile = null;
                    }
                }
                if ($model->upload()) {
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Pages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Удаляет изображение страницы.
     * @param string $type
     * @param int $id ID страницы
     * @return Response
     * @throws Exception если модель не найдена
     * @throws NotFoundHttpException если модель не найдена
     */
    public function actionDeleteMedia(string $type, int $id): Response
    {
        $model = $this->findModel($id);

        if ($model->$type) {
            $path = Yii::getAlias('@webroot/') . $model->$type;
            if (is_file($path)) {
                @unlink($path);
            }
            $model->$type = null;
            $model->save(false, [$type]);
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * Finds the Pages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Pages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pages::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
