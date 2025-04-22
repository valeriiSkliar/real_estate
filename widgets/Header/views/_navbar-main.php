<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\StringHelper;
use app\widgets\Header\helpers\SubmenuHelper;
?>

<ul class="navbar-nav hover-menu main-menu px-0 mx-lg-n4">
    <?php foreach ($menu as $nav): ?>
        <?php
        $linkSlug = StringHelper::slugify($nav['title']);
        $hasSubmenu = isset($nav['submenu']);
        $isActive = $currentUrl === $nav['url'];
        
        $itemClass = ['nav-item', 'py-2', 'py-lg-5', 'px-0', 'px-lg-4'];
        if ($hasSubmenu) $itemClass[] = 'dropdown';
        if ($isActive) $itemClass[] = 'active';
        ?>
        
        <li id="navbar-item-<?= $linkSlug ?>" 
            aria-haspopup="true" 
            aria-expanded="false"
            class="<?= implode(' ', $itemClass) ?>">
            
            <?php
            $linkClass = ['nav-link', 'p-0'];
            if ($hasSubmenu) $linkClass[] = 'dropdown-toggle';
            
            $linkOptions = ['class' => implode(' ', $linkClass)];
            if ($hasSubmenu) {
                $linkOptions['data-bs-toggle'] = 'dropdown';
                $linkOptions['aria-expanded'] = 'false';
            }
            
            echo Html::a(
                $nav['title'] . ($hasSubmenu ? '<span class="caret"></span>' : ''),
                Url::to($nav['url']),
                $linkOptions
            );
            
            if ($hasSubmenu && !is_string($nav['submenu'])) {
                echo SubmenuHelper::renderSubmenu($nav['submenu'], $linkSlug);
            } elseif ($hasSubmenu && is_string($nav['submenu'])) {
                echo $this->render($nav['submenu']);
            }
            ?>
        </li>
    <?php endforeach; ?>
</ul>