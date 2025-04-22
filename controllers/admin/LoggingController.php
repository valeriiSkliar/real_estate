<?php

namespace app\controllers\admin;

use app\models\BotUsers;
use app\models\Logging;
use app\models\search\BotUsersSearch;
use app\models\search\LoggingSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LoggingController implements the CRUD actions for Logging model.
 */
class LoggingController extends AdminController
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
                    'class'   => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Logging models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LoggingSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStatistic()
    {
        $searchModel = new BotUsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $statistic = BotUsers::calculateStatistics();
        return $this->render('statistic', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'statistic'    => $statistic,
        ]);
    }

    /**
     * Displays a single Logging model.
     *
     * @param int $id ID
     *
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
     * Creates a new Logging model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Logging();

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
     * Updates an existing Logging model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id ID
     *
     * @return string|\yii\web\Response
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
     * Deletes an existing Logging model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id ID
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionGraphClick($days = null)
    {
        if (is_numeric($days)) {
            $dateDaysAgo = date('Y-m-d', strtotime("-$days days"));
        } else {
            $dateDaysAgo = null;
        }

        $query = (new \yii\db\Query())
            ->select([
                'DATE(date) as date',
                'SUM(counter) as data'
            ])
            ->from('button_clicks');

        if ($dateDaysAgo) {
            $query->andWhere(['>=', 'DATE(date)', $dateDaysAgo]);
        }

        $logData = $query->groupBy('DATE(date)')
            ->orderBy('date ASC')
            ->all();

        $days = array_column($logData, 'date');
        $data = array_column($logData, 'data');

        // Преобразуем строки в числа
        $data = array_map('intval', $data);

        $days = array_map(function($day) {
            return strtotime($day) * 1000; // Convert to JavaScript timestamp
        }, $days);

        $final_result = [];
        foreach ($days as $index => $day) {
            $final_result[] = [$day, $data[$index]];
        }

        return $this->render('graph-click', [
            'final_result' => $final_result,
        ]);
    }

    public function actionGraphUser($days = null)
    {
        if (is_numeric($days)) {
            $dateDaysAgo = date('Y-m-d', strtotime("-$days days"));
        } else {
            $dateDaysAgo = null;
        }

        $log = \app\models\Logging::find()
            ->select([
                'DATE(created_at) as date',
                'COUNT(*) as data',
            ])
            ->where(['type' => Logging::TYPE_NEW_USER,]);

        if ($dateDaysAgo) {
            $log->andWhere(['>=', 'DATE(created_at)', $dateDaysAgo]);
        }

        $log = $log->groupBy('date')
            ->orderBy('date ASC')
            ->asArray()
            ->all();

        $days = array_column($log, 'date');
        $data = array_column($log, 'data');

        $days = array_map(function ($day) {
            return strtotime($day) * 1000; // Convert to JavaScript timestamp
        }, $days);

        $final_result = [];
        foreach ($days as $index => $day) {
            $final_result[] = [$day, $data[$index]];
        }

        return $this->render('graph-user',
            [
                'final_result' => $final_result,
            ]
        );
    }

    /**
     * Finds the Logging model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id ID
     *
     * @return Logging the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Logging::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
