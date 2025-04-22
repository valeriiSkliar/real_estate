<?php

namespace app\controllers\admin;

use app\models\BotUsers;
use app\models\Sends;
use app\models\search\SendsSearch;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * SendsController implements the CRUD actions for Sends model.
 */
class SendsController extends AdminController
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
     * Lists all Sends models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SendsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sends model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sends model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sends();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                $model->fileUpload = UploadedFile::getInstance($model, 'fileUpload');
                $model->audioFile = UploadedFile::getInstance($model, 'audioFile');
                $model->videoFile = UploadedFile::getInstance($model, 'videoFile');

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

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->fileUpload = UploadedFile::getInstance($model, 'fileUpload');
            $model->audioFile = UploadedFile::getInstance($model, 'audioFile');
            $model->videoFile = UploadedFile::getInstance($model, 'videoFile');

            if ($model->upload()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sends model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sends model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Sends the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sends::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws \JsonException
     */
    public function actionFindUser($q = null): bool|string
    {
        $query = BotUsers::find()
            ->select(['id', 'username', 'email', 'uid'])
            ->where(['like', 'username', $q])
            ->orWhere(['like', 'email', $q])
            ->orWhere(['like', 'id', $q])
            ->orWhere(['like', 'uid', $q])
            ->limit(20)
            ->all();

        $out = [];
        foreach ($query as $user) {
            $name = $user->username;

            if (!$user->username) {
                $name = $user->email ? $user->email : $user->uid;
            }

            $out[] = ['id' => $user->id, 'text' => "{$user->id} - {$name}"];
        }

        return json_encode(['results' => $out], JSON_THROW_ON_ERROR);
    }
}
