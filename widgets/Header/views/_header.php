<?php
use yii\helpers\Html;
?>

<?= Html::beginTag('header', $options) ?>
    <div <?= Html::renderTagAttributes($stickyAreaOptions) ?>>
        <div <?= Html::renderTagAttributes($containerOptions) ?>>
            <nav <?= Html::renderTagAttributes($navbarOptions) ?>>
              <a <?= Html::renderTagAttributes(['class' => 'navbar-brand', 'href' => $currentUrl]) ?>>
                <?= Html::img($logo, ['alt' => $siteTitle, 'class' => 'd-none d-lg-inline-block']) ?>
                <?= Html::img($logoWhite, ['alt' => $siteTitle, 'class' => 'd-inline-block d-lg-none']) ?>

              </a>
              <div class="d-flex d-lg-none ml-auto">
                    <a class="mr-4 position-relative text-white p-2" href="#">
                        <i class="fal fa-heart fs-large-4"></i>
                        <span class="badge badge-primary badge-circle badge-absolute">1</span>
                    </a>
                    <button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse"
                            data-target="#primaryMenu01"
                            aria-controls="primaryMenu01" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="text-white fs-24"><i class="fal fa-bars text-black"></i></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse mt-3 mt-lg-0 mx-auto flex-grow-0" id="primaryMenu01">
                    <?= $this->render('_navbar-main', [
                        'menu' => $menu,
                        'currentUrl' => $currentUrl
                    ]) ?>
                    <div class="d-block d-lg-none">
                        <?= $this->render('_navbar-right-mobile') ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
<?= Html::endTag('header') ?>