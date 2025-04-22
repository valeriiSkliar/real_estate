<?php
namespace app\assets;

use yii\web\AssetBundle;

class NpmAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules';
    
    // public $js = [
    //     'jquery/dist/jquery.min.js',
    //     'bootstrap/dist/js/bootstrap.bundle.min.js',
    //     'moment/min/moment.min.js',
    //     // другие js файлы
    // ];
    
    // public $css = [
    //     'bootstrap/dist/css/bootstrap.min.css',
    //     // другие css файлы
    // ];
    
    public $depends = [
        // 'yii\web\YiiAsset',
    ];
}