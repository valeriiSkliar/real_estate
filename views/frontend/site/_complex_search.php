<?php
/**
 * Complex search partial view
 */
use yii\helpers\Html;

$mockData = [
  ['id' => 'complex1', 'name' => 'ЖК Солнечный', 'address' => 'ул. Солнечная, 10'],
  ['id' => 'complex2', 'name' => 'ЖК Морской', 'address' => 'ул. Морская, 15'],
  ['id' => 'complex3', 'name' => 'ЖК Парковый', 'address' => 'ул. Парковая, 5'],
  ['id' => 'complex4', 'name' => 'ЖК Центральный', 'address' => 'ул. Центральная, 20'],
  ['id' => 'complex5', 'name' => 'ЖК Речной', 'address' => 'ул. Речная, 8'],
];
?>

<div class="mb-3">
    <input type="text" class="form-control" id="complexSearch" placeholder="Введите название ЖК, адрес, район...">
</div>
<div id="searchResults" class="mt-3">
    <?php foreach ($mockData as $complex) { ?>
        <div class="form-check">
            <input class="form-check-input complex-checkbox" type="checkbox" value="<?= $complex['id'] ?>" id="complex-<?= $complex['id'] ?>" data-name="<?= $complex['name'] ?>">
            <label class="form-check-label" for="complex-<?= $complex['id'] ?>">
                <div class="search-result-name" style="font-weight: bold;">
                    <?= $complex['name'] ?>
                </div>
                <div class="search-result-address" style="color: #666;">
                    <?= $complex['address'] ?>
                </div>
            </label>
        </div>
    <?php } ?>
</div>
