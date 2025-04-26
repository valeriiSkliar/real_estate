<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Selections $model The collection model
 * @var yii\data\ActiveDataProvider $objectsDataProvider (Optional) Data provider for objects in the collection
 */

$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => 'Избранное и подборки', 'url' => ['/favorites/index']]; // Или 'collections' ?
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="collection-view-page container mt-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php // Кнопки действий (редактировать, удалить) можно добавить здесь 
        ?>
        <?= Html::a('Редактировать', ['update-collection', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete-collection', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить эту подборку?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <hr>

    <h2>Объекты в подборке</h2>

    <?php
    // Здесь будет отображение списка объектов в подборке
    // Например, с помощью GridView или ListView, используя $objectsDataProvider
    // if ($objectsDataProvider->getCount() > 0) {
    //     echo yii\widgets\ListView::widget([
    //         'dataProvider' => $objectsDataProvider,
    //         'itemView' => '_object_item', // Путь к представлению одного объекта
    //         'summary' => '',
    //     ]);
    // } else {
    echo '<div class="alert alert-info">В этой подборке пока нет объектов.</div>';
    // }
    ?>

    <?php // Кнопка или форма для добавления объектов в подборку 
    ?>
    <div class="mt-4">
        <?= Html::a('Найти объекты для добавления', ['/search/index'], ['class' => 'btn btn-success']) // Пример ссылки на поиск
        ?>
    </div>

</div>