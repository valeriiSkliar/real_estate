<?php
use yii\helpers\Html;
?>

<div class="mb-3">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuContentButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Dropdown with content
        </button>
        <div class="dropdown-menu p-4 text-muted" style="max-width: 200px;" aria-labelledby="dropdownMenuContentButton">
            <p>Some example text that's free-flowing within the dropdown menu.</p>
            <p class="mb-0">And this is more example text.</p>
        </div>
    </div>
</div>

<div>
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuHeaderButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Dropdown with headers
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuHeaderButton">
            <li><h6 class="dropdown-header">Dropdown header</h6></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><h6 class="dropdown-header">Another header</h6></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
            <li><a class="dropdown-item" href="#">Another action here</a></li>
        </ul>
    </div>
</div>
