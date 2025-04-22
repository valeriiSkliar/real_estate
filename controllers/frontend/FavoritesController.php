<?php

namespace app\controllers\frontend;

use app\models\Favorites;
use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * FavoritesController implements the CRUD actions for Favorites model.
 */
class FavoritesController extends FrontendController
{
    public $layout = 'frontend/main';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add' => ['post'],
                    'remove' => ['post', 'delete'],
                ],
            ],
        ];
    }

    /**
     * Lists all user's favorite advertisements
     * @return mixed
     */
    public function actionIndex()
    {
        // В реальной системе, здесь бы использовалась модель с пагинацией
        // и получением данных из БД для конкретного пользователя
        $favorites = []; // Временно пустой массив, позже будет заменен на реальные данные

        return $this->render('index', [
            'favorites' => $favorites
        ]);
    }

    /**
     * Adds an advertisement to favorites.
     * @return Response
     */
    public function actionAdd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // В реальной системе были бы проверки и сохранение в БД

        // Получаем ID объявления из POST-запроса
        $advertisementId = Yii::$app->request->post('propertyId');

        if (!$advertisementId) {
            return [
                'success' => false,
                'message' => 'Не указан ID объявления',
            ];
        }

        // Имитируем успешное добавление в избранное
        // В реальной системе здесь был бы код для сохранения в БД

        return [
            'success' => true,
            'message' => 'Объявление успешно добавлено в избранное',
            'data' => [
                'propertyId' => $advertisementId,
            ],
        ];
    }

    /**
     * Removes an advertisement from favorites.
     * @return Response
     */
    public function actionRemove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Получаем ID объявления из POST-запроса
        $advertisementId = Yii::$app->request->post('propertyId');

        if (!$advertisementId) {
            return [
                'success' => false,
                'message' => 'Не указан ID объявления',
            ];
        }

        // Имитируем успешное удаление из избранного
        // В реальной системе здесь был бы код для удаления из БД

        return [
            'success' => true,
            'message' => 'Объявление успешно удалено из избранного',
            'data' => [
                'propertyId' => $advertisementId,
            ],
        ];
    }
}
