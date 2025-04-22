<?php

use app\models\Languages;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\LanguagesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Languages';
$this->params['breadcrumbs'][] = $this->title;
$currentProvider = Yii::$app->cache->get('translator_provider') ?: Yii::$app->params['translator'] ?? 'google';

?>
<div class="languages-index">
    <div style="background: #dee2e6">
        Текущий провайдер переводов: <span class="badge bg-primary"><?= Languages::TRANSLATORS[$currentProvider] ?? null?></span>
        <?php $form = ActiveForm::begin([
            'action' => ['change-provider'],
            'method' => 'get',
        ]); ?>
        <?= Html::dropDownList(
            'provider',
            null,
            Languages::TRANSLATORS
            , ['class' => 'form-control'])
        ?>
        <?= Html::submitButton('Сменить', ['class' => 'btn btn-success my-3']) ?>

        <?php ActiveForm::end(); ?>
    </div>
    <br>
    <br>
    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'slug',
            'name',
            [
                'attribute' => 'is_active',
                'value' => function ($model) {
                    return $model->is_active ? 'Активно' : 'Скрыто';
                },

                'filter' => Select2::widget(
                    [
                        'model' => $searchModel,
                        'attribute' => 'is_active',
                        'data' => [0 => 'Скрыто', 1 => 'Активно'],
                        'options' => ['placeholder' => '-'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]
                ),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, Languages $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
