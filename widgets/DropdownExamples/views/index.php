<?php
use yii\helpers\Html;
?>

<div <?= Html::renderTagAttributes($containerOptions) ?>>
    <h2><?= Html::encode($title) ?></h2>
    
    <div class="row g-4">
        <?php if (in_array('basic', $examples)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Basic Dropdown</h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_basic') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (in_array('split', $examples)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Split Button Dropdown</h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_split') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (in_array('sizing', $examples)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sizing Options</h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_sizing') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (in_array('directions', $examples)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dropdown Directions</h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_directions') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (in_array('menu-content', $examples)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Menu Content</h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_menu_content') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (in_array('forms', $examples)): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dropdown with Form</h5>
                    </div>
                    <div class="card-body">
                        <?= $this->render('_forms') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
