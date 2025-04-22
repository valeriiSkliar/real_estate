<?php

namespace app\controllers\admin;

use app\models\BotUsers;
use app\models\search\BotUsersSearch;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BotUsersController implements the CRUD actions for BotUsers model.
 */
class BotUsersController extends AdminController
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
     * Lists all BotUsers models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BotUsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BotUsers model.
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
     * Creates a new BotUsers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new BotUsers();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
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
     * Updates an existing BotUsers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {

            if ($model->password) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($model->password);
            }

            $model->save();

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BotUsers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BotUsers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BotUsers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BotUsers::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToAdmin($id)
    {
        $model = $this->findModel($id);
        $model->updateAttributes(['role_id' => BotUsers::ADMIN_ROLE_ID]);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionToUser($id)
    {
        $model = $this->findModel($id);
        $model->updateAttributes(['role_id' => BotUsers::USER_ROLE_ID]);
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionNotification($value)
    {
        BotUsers::updateAll(['notification_on' => $value], '1=1');
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionDisconnectCall($id)
    {
        $model = $this->findModel($id);

        if ($model->chat_id){
            $companion = BotUsers::findOne(['uid' => $model->chat_id]);
            $companion?->updateAttributes(['on_call' => 0, 'chat_id' => null]);
        }
        $model?->updateAttributes(['on_call' => 0, 'chat_id' => null]);

        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionFilterDays()
    {
        $days = Yii::$app->request->post('days');

        if ($days) {
            // Рассчитаем новую дату для trial_until
            $newTrialUntilDate = date('Y-m-d H:i:s', strtotime("+$days days"));

            // Обновляем поле trial_until у пользователей, которые попадают под условия
            BotUsers::updateAll(
                ['trial_until' => $newTrialUntilDate],
                [
                    'and',
                    ['or', ['trial_until' => null], ['<', 'trial_until', date('Y-m-d H:i:s')]],
                    ['or', ['paid_until' => null], ['<', 'paid_until', date('Y-m-d H:i:s')]],
                    ['not in', 'id', (new \yii\db\Query())
                        ->select('referral_id')
                        ->from('referrals')
                        ->where(['not', ['referral_id' => null]])]
                ]
            );

            return $this->redirect(['index']);
        }

        return $this->redirect(['index']);
    }
}
