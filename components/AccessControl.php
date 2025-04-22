<?php

namespace app\components;

use Yii;
use yii\filters\AccessControl as BaseAccessControl;

class AccessControl extends BaseAccessControl
{
    protected function isActive($action)
    {
        if (!parent::isActive($action)) {
            return false;
        }

        if (Yii::$app->user->id > 1) {
            $allowedRoutes = [
                '/admin/oils/index',
                '/admin/oils/update',
                '/admin/disease/index',
                '/admin/disease/update',
            ];

            $currentRoute = Yii::$app->controller->getRoute();

            if (in_array($currentRoute, $allowedRoutes)) {
                return true;
            }

            return false;
        }

        return true;
    }
}