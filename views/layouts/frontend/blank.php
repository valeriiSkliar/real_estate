<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\NpmAsset;
use app\assets\WebpackAsset;
use app\widgets\Header\Header;
use app\widgets\SearchWidget;
use app\widgets\DropdownExamples\DropdownExamples;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use app\widgets\Alert;

// AppAsset::register($this);
WebpackAsset::register($this);


$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <?php $this->head() ?>
    <script src="https://telegram.org/js/telegram-web-app.js?56"></script>
    <script>
      var webApp = window.Telegram.WebApp;
      webApp.ready();
      webApp.expand();
      webApp.lockOrientation();
    </script>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header id="header">
        <?= Header::widget([
            'logo' => '@web/images/logo.png',
            'siteTitle' => 'My Site'
        ]) ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
    <?= SearchWidget::widget() ?>
        <?= $this->render('partials/sidebar') ?>
        <div class="container">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?php endif ?>
            <?= $content ?>
        </div>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-muted">
                <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
                <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>