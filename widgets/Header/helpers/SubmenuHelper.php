<?php

namespace app\widgets\Header\helpers;

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\StringHelper;

class SubmenuHelper
{
    public static function renderSubmenu($submenu, $parentSlug, $currentUrl = null)
    {
        if ($currentUrl === null) {
            $currentUrl = \Yii::$app->request->url;
        }

        $html = Html::beginTag('ul', [
            'class' => 'dropdown-menu pt-3 pb-0 pb-lg-3',
            'aria-labelledby' => "navbar-item-{$parentSlug}"
        ]);
        
        foreach ($submenu as $item) {
            $itemSlug = StringHelper::slugify($item['title']);
            $isActive = $currentUrl === $item['url'];
            $hasSubmenu = isset($item['submenu']);
            
            $itemClass = ['dropdown-item'];
            if ($hasSubmenu) $itemClass[] = 'dropdown dropright';
            if ($isActive) $itemClass[] = 'active';
            
            $html .= Html::beginTag('li', ['class' => implode(' ', $itemClass)]);
            
            $linkClass = ['dropdown-link'];
            if ($hasSubmenu) $linkClass[] = 'dropdown-toggle';
            
            $linkOptions = [
                'id' => "navbar-link-{$itemSlug}",
                'class' => implode(' ', $linkClass),
            ];
            if ($hasSubmenu) {
                $linkOptions['data-bs-toggle'] = 'dropdown';
                $linkOptions['aria-expanded'] = 'false';
            }
            
            $html .= Html::a($item['title'], Url::to($item['url']), $linkOptions);
            
            if ($hasSubmenu) {
                $html .= Html::beginTag('ul', [
                    'class' => 'dropdown-menu dropdown-submenu pt-3 pb-0 pb-lg-3',
                    'aria-labelledby' => "navbar-link-{$itemSlug}"
                ]);
                
                foreach ($item['submenu'] as $subItem) {
                    $isSubActive = $currentUrl === $subItem['url'];
                    $html .= Html::beginTag('li', ['class' => 'dropdown-item' . ($isSubActive ? ' active' : '')]);
                    $html .= Html::a($subItem['title'], Url::to($subItem['url']), ['class' => 'dropdown-link']);
                    $html .= Html::endTag('li');
                }
                
                $html .= Html::endTag('ul');
            }
            
            $html .= Html::endTag('li');
        }
        
        $html .= Html::endTag('ul');
        return $html;
    }
}