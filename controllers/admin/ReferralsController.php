<?php

namespace app\controllers\admin;

use app\components\export\CsvExportService;
use app\components\export\ExportHandler;
use app\models\BotUsers;
use app\models\Referrals;
use app\models\search\ReferralsSearch;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ReferralsController implements the CRUD actions for Referrals model.
 */
class ReferralsController extends AdminController
{
    private string $basePath;
    private ExportHandler $exportHandler;
    private CsvExportService $csvExportService;

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

    public function init(): void
    {
        parent::init();
        $this->basePath = Yii::getAlias('@webRoot/uploads/');

        // Инициализация сервиса экспорта CSV
        $this->csvExportService = new CsvExportService($this->basePath);

        // Инициализация обработчика экспорта
        $this->exportHandler = new ExportHandler($this->csvExportService);
    }

    /**
     * Экспортирует реферальные данные в CSV файл.
     *
     * @param string $filename Имя файла
     * @return Response
     */
    public function actionExportReferral(string $filename = 'referral.csv'): Response
    {
        try {
            $csvContent = $this->exportHandler->exportReferralToString();

            return $this->csvExportService->sendCsv($csvContent, $filename);
        } catch (Exception $e) {
            Yii::error('Ошибка при экспорте общих данных: '. $e->getMessage());

            Yii::$app->session->setFlash('error', 'Операция не удалась');
        }

        return $this->redirect(['index']);
    }

    /**
     * Lists all Referrals models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ReferralsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Referrals model.
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
     * Creates a new Referrals model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Referrals();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $this->fillUpModel($model);

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
     * Updates an existing Referrals model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $this->fillUpModel($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Referrals model.
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
     * Finds the Referrals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Referrals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Referrals::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function fillUpModel($model)
    {
        $parent = BotUsers::findOne($model->parent_id);
        $referral = BotUsers::findOne($model->referral_id);
        $model->parent_username = $parent->username;
        $model->referral_username = $referral->username;
        $model->created_at = date('Y-m-d');

        return $model->save();
    }
}
