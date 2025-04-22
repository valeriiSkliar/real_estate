<?php

namespace app\controllers\admin;

use yii\web\Controller;

class AdminController extends Controller
{
    public function init()
    {
        parent::init();
        $this->layout = '@app/views/layouts/admin/main';
    }
}