<?php

namespace app\controllers\frontend;

use yii\web\Controller;

class FrontendController extends Controller
{
    public function init()
    {
        parent::init();
        $this->layout = '@app/views/layouts/frontend/blank';
    }
}