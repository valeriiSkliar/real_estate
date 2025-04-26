<?php

namespace app\controllers\frontend;

use app\models\Favorites;
use app\models\Selections;
use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use app\widgets\FavoritesListWidget;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;

/**
 * FavoritesController implements the CRUD actions for Favorites model.
 */
class FavoritesController extends FrontendController
{
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
                    'create-collection-ajax' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays the favorites page.
     * @return string
     */
    public function actionIndex()
    {
        // Здесь будет логика получения данных для виджета FavoritesListWidget
        // $dataProvider = new ActiveDataProvider([...]); // Пример для реальных данных

        return $this->render('index', [
            'activeTab' => 'favorites', // Указываем активную вкладку
            // 'dataProvider' => $dataProvider, // Передаем провайдер, если виджет не будет получать данные сам
        ]);
    }

    /**
     * Displays the collections page.
     * @return string
     */
    public function actionCollections()
    {
        // Здесь будет логика получения данных для виджета CollectionsListWidget
        // $dataProvider = new ActiveDataProvider([...]); // Пример для реальных данных

        return $this->render('index', [ // Рендерим то же представление
            'activeTab' => 'collections', // Указываем активную вкладку
            // 'dataProvider' => $dataProvider, // Передаем провайдер, если виджет не будет получать данные сам
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

    /**
     * Creates a new Selections model via AJAX.
     * Expects POST request with 'Selections[name]'.
     * Returns JSON response: {success: boolean, redirectUrl?: string, errors?: array}
     * @return array
     * @throws BadRequestHttpException if the request is not POST or not AJAX
     * @throws ForbiddenHttpException if the user is not logged in
     */
    public function actionCreateCollectionAjax()
    {
        Yii::error('Create collection AJAX request received');

        if (!Yii::$app->request->isPost || !Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Invalid request method or type.');
        }
        // Проверка авторизации уже сделана через AccessControl

        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Selections();

        $model->user_id = Yii::$app->user->id; // Устанавливаем ID пользователя

        if ($model->load(Yii::$app->request->post()) && $model->validate(['name'])) {
            if ($model->save(false)) { // save(false) чтобы не запускать валидацию еще раз
                // Используем Url::to для генерации URL для фронтенда
                $redirectUrl = Url::to(['/favorites/view-collection', 'id' => $model->id]);
                return ['success' => true, 'redirectUrl' => $redirectUrl];
            } else {
                // Ошибки сохранения (маловероятно при save(false) после validate(), но на всякий случай)
                return ['success' => false, 'errors' => $model->getErrors()]; // Могут быть ошибки базы данных
            }
        } else {
            // Ошибки валидации
            return ['success' => false, 'errors' => $model->getErrors()];
        }
    }

    /**
     * Displays a single Selections model (user's collection).
     * @param int $id Collection ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found or does not belong to the user
     */
    public function actionViewCollection($id)
    {
        $model = Selections::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if ($model === null) {
            throw new NotFoundHttpException('The requested collection does not exist or you do not have permission to view it.');
        }

        // Здесь можно будет передать $dataProvider для объектов в подборке,
        // когда будет реализована логика добавления/просмотра объектов
        // $objectsDataProvider = new ActiveDataProvider([...]);

        return $this->render('view-collection', [
            'model' => $model,
            // 'objectsDataProvider' => $objectsDataProvider,
        ]);
    }
}
