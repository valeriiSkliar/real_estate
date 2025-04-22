<?php

namespace app\controllers\admin;

use app\models\BotUsers;
use app\models\Payouts;
use app\models\search\PayoutsSearch;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PayoutsController implements the CRUD actions for Payouts model.
 */
class PayoutsController extends AdminController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
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
     * Lists all Payouts models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PayoutsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payouts model.
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
     * Creates a new Payouts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate($status)
    {
        $model = new Payouts();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $result = $this->withdrawProcess($model, $status);
                if ($result) {
                    Payouts::sendPayoutNotification($model->telegram_id, $model->amount, $model->status);
                }
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Payouts model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Payouts model.
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
     * Finds the Payouts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payouts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payouts::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionWithdraw($id): Response
    {
        $model = $this->findModel($id);
        $result = $this->withdrawProcess($model, Payouts::STATUS_WITHDRAWN);

        if ($result) {
            Payouts::sendPayoutNotification($model->telegram_id, $model->amount, $model->status);
        }

        return $this->redirect(['index']);
    }

    /**
     * @throws Exception
     */
    private function withdrawProcess($model, $status): bool
    {
        $user = BotUsers::findOne($model->uid);
        $model->username = $user->username;
        $model->telegram_id = $user->uid;
        $model->status = $status;

        if ($model->save()){
            $amount = ($status == Payouts::STATUS_ADDED) ? $model->amount : -$model->amount;
            $user->bonus = $user->bonus + $amount;

            if($user->bonus >= 0){
                $user->save();
                Yii::$app->session->setFlash('success', 'Операция прошла успешно');

                return true;
            }
        }

        Yii::$app->session->setFlash('error', 'Что-то пошло не так');

        return false;
    }
}
