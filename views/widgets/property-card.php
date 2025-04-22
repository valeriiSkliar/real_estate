<?php
/**
 * @var string $imageUrl
 * @var string $price
 * @var string $status
 * @var string $title
 * @var string $address
 * @var int $bedrooms
 * @var int $bathrooms
 * @var int $squareFeet
 * @var int $garages
 * @var int $imageCount
 * @var int $videoCount
 * @var string $detailUrl
 */

use yii\helpers\Html;
?>

<div class="card" data-animate="fadeInUp">
    <div class="hover-change-image bg-hover-overlay rounded-lg card-img-top">
        <?= Html::img($imageUrl, ['alt' => $title]) ?>
        <div class="card-img-overlay d-flex flex-column">
            <div class="mt-auto d-flex hover-image">
                <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                    <?php if ($imageCount > 0): ?>
                    <li class="list-inline-item mr-2" data-toggle="tooltip" title="<?= $imageCount ?> Images">
                        <a href="#" class="text-white hover-primary">
                            <i class="far fa-images"></i><span class="pl-1"><?= $imageCount ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($videoCount > 0): ?>
                    <li class="list-inline-item" data-toggle="tooltip" title="<?= $videoCount ?> Video">
                        <a href="#" class="text-white hover-primary">
                            <i class="far fa-play-circle"></i><span class="pl-1"><?= $videoCount ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                    <li class="list-inline-item mr-3 h-32" data-toggle="tooltip" title="Wishlist">
                        <a href="#" class="text-white fs-20 hover-primary">
                            <i class="far fa-heart"></i>
                        </a>
                    </li>
                    <li class="list-inline-item mr-3 h-32" data-toggle="tooltip" title="Compare">
                        <a href="#" class="text-white fs-20 hover-primary">
                            <i class="fas fa-exchange-alt"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <p class="fs-17 font-weight-bold text-heading mb-0 lh-1">
            <?= Html::encode($price) ?>
        </p>
        <span class="badge badge-primary"><?= Html::encode($status) ?></span>
    </div>
    <div class="card-body py-2">
        <h2 class="fs-16 lh-2 mb-0">
            <?= Html::a(Html::encode($title), $detailUrl, ['class' => 'text-dark hover-primary']) ?>
        </h2>
        <p class="font-weight-500 text-gray-light mb-0"><?= Html::encode($address) ?></p>
    </div>
    <div class="card-footer bg-transparent pt-3 pb-4">
        <ul class="list-inline d-flex mb-0 flex-wrap mr-n5">
            <?php if ($bedrooms > 0): ?>
            <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="<?= $bedrooms ?> Bedroom">
                <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                    <use xlink:href="#icon-bedroom"></use>
                </svg>
                <?= $bedrooms ?> Br
            </li>
            <?php endif; ?>
            
            <?php if ($bathrooms > 0): ?>
            <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="<?= $bathrooms ?> Bathrooms">
                <svg class="icon icon-shower fs-18 text-primary mr-1">
                    <use xlink:href="#icon-shower"></use>
                </svg>
                <?= $bathrooms ?> Ba
            </li>
            <?php endif; ?>
            
            <?php if ($squareFeet > 0): ?>
            <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="Size">
                <svg class="icon icon-square fs-18 text-primary mr-1">
                    <use xlink:href="#icon-square"></use>
                </svg>
                <?= $squareFeet ?> Sq.Ft
            </li>
            <?php endif; ?>
            
            <?php if ($garages > 0): ?>
            <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="<?= $garages ?> Garage">
                <svg class="icon icon-Garage fs-18 text-primary mr-1">
                    <use xlink:href="#icon-Garage"></use>
                </svg>
                <?= $garages ?> Gr
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
