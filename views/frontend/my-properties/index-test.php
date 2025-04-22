<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Мои объявления';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="my-property-index">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row mb-4">
            <div class="col-12">
                <?= Html::a('Создать объявление', ['create'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php if (!empty($advertisements)): ?>
            <div class="row">
                <?php foreach ($advertisements as $advertisement): ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php
                            $image = \app\models\AdvertisementImages::find()
                                ->where(['advertisement_id' => $advertisement->id])
                                ->one();
                            if ($image): ?>
                                <img src="<?= Url::to('@web/uploads/advertisements/' . $image->image) ?>"
                                    class="card-img-top" alt="<?= Html::encode($advertisement->title) ?>">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?= Html::encode($advertisement->title) ?></h5>
                                <p class="card-text">
                                    <strong>Тип:</strong> <?= Html::encode($advertisement->property_type) ?><br>
                                    <strong>Цена:</strong> <?= number_format($advertisement->price, 0, '.', ' ') ?> ₴
                                </p>
                            </div>

                            <div class="card-footer">
                                <div class="btn-group w-100">
                                    <?= Html::a('Просмотр', ['view', 'id' => $advertisement->id], ['class' => 'btn btn-info']) ?>
                                    <?= Html::a('Редактировать', ['update', 'id' => $advertisement->id], ['class' => 'btn btn-primary']) ?>
                                    <?= Html::a('Удалить', ['delete', 'id' => $advertisement->id], [
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => 'Вы уверены, что хотите удалить это объявление?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                У вас пока нет объявлений. <?= Html::a('Создать первое объявление', ['create']) ?>
            </div>
        <?php endif; ?>
    </div>
</div>