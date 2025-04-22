<?php

namespace app\controllers\frontend;

use Yii;
use yii\web\Controller;
use app\models\Advertisements;
use app\models\AdvertisementImages;
use app\models\Cities;
use yii\web\UploadedFile;
use yii\helpers\Json;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MobileAdvertisementsController implements the CRUD actions for Advertisements model
 * optimized for mobile devices.
 */
class MyPropertiesController extends FrontendController
{
    /**
     * Initialize controller
     */
    public function init()
    {
        $this->layout = '@app/views/layouts/frontend/main';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Advertisement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Advertisements();

        // Get cities for dropdown
        $cities = Cities::getActiveCities();

        // Define available property types
        $propertyTypes = [
            'apartment' => 'Квартира',
            'house' => 'Дом',
            'land' => 'Участок',
        ];

        // Define available trade types
        $tradeTypes = [
            'sale' => 'Продажа',
            'rent' => 'Аренда',
        ];

        // Define available conditions
        $conditions = [
            'new' => 'Новое',
            'good' => 'Хорошее',
            'needs_repair' => 'Требует ремонта',
        ];

        if ($model->load(Yii::$app->request->post())) {
            // Handle form submission
            if ($model->save()) {
                // Handle image uploads
                $this->handleImageUploads($model->id);

                Yii::$app->session->setFlash('success', 'Объявление успешно создано.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'cities' => $cities,
            'propertyTypes' => $propertyTypes,
            'tradeTypes' => $tradeTypes,
            'conditions' => $conditions,
        ]);
    }

    /**
     * Updates an existing Advertisements model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Get existing images
        $existingImages = AdvertisementImages::find()
            ->where(['advertisement_id' => $id])
            ->all();

        // Get cities for dropdown
        $cities = Cities::getActiveCities();

        // Define available property types
        $propertyTypes = [
            'apartment' => 'Квартира',
            'house' => 'Дом',
            'land' => 'Участок',
        ];

        // Define available trade types
        $tradeTypes = [
            'sale' => 'Продажа',
            'rent' => 'Аренда',
        ];

        // Define available conditions
        $conditions = [
            'new' => 'Новое',
            'good' => 'Хорошее',
            'needs_repair' => 'Требует ремонта',
        ];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Handle image uploads
            $this->handleImageUploads($model->id);

            Yii::$app->session->setFlash('success', 'Объявление успешно обновлено.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'existingImages' => $existingImages,
            'cities' => $cities,
            'propertyTypes' => $propertyTypes,
            'tradeTypes' => $tradeTypes,
            'conditions' => $conditions,
        ]);
    }

    /**
     * Displays a single Advertisements model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $images = AdvertisementImages::find()
            ->where(['advertisement_id' => $id])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'images' => $images,
        ]);
    }

    /**
     * Handles image uploads for advertisements
     * @param integer $advertisementId
     * @return void
     */
    protected function handleImageUploads($advertisementId)
    {
        $uploadedImages = UploadedFile::getInstancesByName('images');

        if (!empty($uploadedImages)) {
            foreach ($uploadedImages as $uploadedImage) {
                $imageName = 'ad_' . $advertisementId . '_' . uniqid() . '.' . $uploadedImage->extension;
                $imagePath = Yii::getAlias('@webroot/uploads/advertisements/' . $imageName);

                // Ensure directory exists
                if (!is_dir(dirname($imagePath))) {
                    mkdir(dirname($imagePath), 0777, true);
                }

                if ($uploadedImage->saveAs($imagePath)) {
                    $image = new AdvertisementImages();
                    $image->advertisement_id = $advertisementId;
                    $image->image = $imageName;
                    $image->save();
                }
            }
        }
    }

    /**
     * Deletes an image using AJAX
     * @return mixed
     */
    public function actionDeleteImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isAjax) {
            $imageId = Yii::$app->request->post('imageId');

            $image = AdvertisementImages::findOne($imageId);
            if ($image) {
                // Delete file
                $imagePath = Yii::getAlias('@webroot/uploads/advertisements/' . $image->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                // Delete record
                if ($image->delete()) {
                    return ['success' => true];
                }
            }
        }

        return ['success' => false];
    }

    /**
     * Finds the Advertisements model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Advertisements the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Advertisements::findOne($id)) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    /**
     * Lists all Advertisements models.
     * @return mixed
     */
    public function actionIndex()
    {
        $advertisements = Advertisements::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'advertisements' => $advertisements,
        ]);
    }
}
