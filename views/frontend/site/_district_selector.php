<?php
/**
 * District selector partial view
 */
use yii\helpers\Html;
?>

<div class="district-list">
    <div class="form-check">
        <input class="form-check-input district-checkbox" type="checkbox" value="center" id="district-center">
        <label class="form-check-label" for="district-center">Центр</label>
    </div>
    <div class="form-check">
        <input class="form-check-input district-checkbox" type="checkbox" value="north" id="district-north">
        <label class="form-check-label" for="district-north">Север</label>
    </div>
    <div class="form-check">
        <input class="form-check-input district-checkbox" type="checkbox" value="south" id="district-south">
        <label class="form-check-label" for="district-south">Юг</label>
    </div>
    <div class="form-check">
        <input class="form-check-input district-checkbox" type="checkbox" value="east" id="district-east">
        <label class="form-check-label" for="district-east">Восток</label>
    </div>
    <div class="form-check">
        <input class="form-check-input district-checkbox" type="checkbox" value="west" id="district-west">
        <label class="form-check-label" for="district-west">Запад</label>
    </div>
</div>
