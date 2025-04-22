<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin" class="brand-link">
        <img src="<?= $assetDir ?>/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Dashboard</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <?php if (!Yii::$app->user->isGuest) : ?>
                <div class="image">
                    <img src="<?= $assetDir ?>/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?= Yii::$app->user->identity->username ?></a>
                </div>
            <?php endif; ?>
        </div>

        <!-- SidebarSearch Form -->
        <!-- href be escaped -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php if (!Yii::$app->user->isGuest) : ?>
            <?php
                echo \hail812\adminlte\widgets\Menu::widget([
                    'items' => [
                        ['label' => 'Пользователи', 'icon' => 'folder', 'items' =>
                            [
                                ['label' => 'Админы', 'icon' => 'fa fa-users', 'url' => ['/admin/user/index'], 'active' => $this->context->id == 'admin/user'],
                                ['label' => 'Клиенты', 'icon' => 'fa fa-address-card', 'url' => ['/admin/bot-users/index'], 'active' => $this->context->id == 'admin/bot-users'],
//                                ['label' => 'Информация о партнере', 'icon' => 'fa fa-address-card', 'url' => ['/admin/user-info/index'], 'active' => $this->context->id == 'admin/user-info'],
                                ['label' => 'Реферальная программа', 'icon' => 'fa fa-users', 'url' => ['/admin/referrals/index'], 'active' => $this->context->id == 'admin/referrals'],
//                                ['label' => 'Выгрузки', 'icon' => 'fa fa-archive', 'url' => ['/admin/export/index'], 'active' => $this->context->id == 'admin/export'],
                                ['label' => 'Избранное', 'icon' => 'fa fa-heart', 'url' => ['/admin/favorites/index'], 'active' => $this->context->id == 'admin/favorites'],

                            ],
                        ],
                        ['label' => 'Объявления', 'icon' => 'folder', 'items' => [
                            ['label' => 'Необработанные объявления', 'icon' => 'fa fa-comments', 'url' => ['/admin/raw-messages/index'], 'active' => $this->context->id == 'admin/raw-messages'],
                            ['label' => 'Объявления', 'icon' => 'fa fa-address-card', 'url' => ['/admin/advertisements/index'], 'active' => $this->context->id == 'admin/advertisements'],
                            ['label' => 'Подборки', 'icon' => 'fa fa-check-circle', 'url' => ['/admin/selections/index'], 'active' => $this->context->id == 'admin/selections'],
                            ['label' => 'Разделы объявлений', 'icon' => 'fa fa-bars', 'url' => ['/admin/advertisement-sections/index'], 'active' => $this->context->id == 'admin/advertisement-sections'],
                            ['label' => 'Парсер', 'icon' => 'fa fa-cubes', 'url' => ['/admin/source-chats/index'], 'active' => $this->context->id == 'admin/source-chats'],
                            ['label' => 'Город', 'icon' => 'fa fa-location-arrow', 'url' => ['/admin/cities/index'], 'active' => $this->context->id == 'admin/cities'],
                            ['label' => 'Район', 'icon' => 'fa fa-building', 'url' => ['/admin/districts/index'], 'active' => $this->context->id == 'admin/districts'],

                        ]],
                        ['label' => 'Бот', 'icon' => 'folder', 'items' => [
                            ['label' => 'Кнопки', 'icon' => 'fa fa-hand-pointer', 'url' => ['/admin/buttons/index'], 'active' => $this->context->id == 'admin/buttons'],
                            ['label' => 'Тексты', 'icon' => 'fa fa-file', 'url' => ['/admin/texts/index'], 'active' => $this->context->id == 'admin/texts'],
                            ['label' => 'Страницы', 'icon' => 'fa fa-book', 'url' => ['/admin/pages/index'], 'active' => $this->context->id == 'admin/pages'],
                            ['label' => 'Чаты', 'icon' => 'fa fa-comment', 'url' => ['/admin/telegram-chats/index'], 'active' => $this->context->id == 'admin/telegram-chats'],
                            ['label' => 'Поддержка', 'icon' => 'fa fa-ambulance', 'url' => ['/admin/support-messages/index'], 'active' => $this->context->id == 'admin/support-messages'],
//                            ['label' => 'Рассылки', 'icon' => 'fa fa-paper-plane', 'url' => ['/admin/sends/index'], 'active' => $this->context->id == 'admin/sends'],
                        ]],
//                        ['label' => 'Общее', 'icon' => 'folder', 'items' => [
//                            ['label' => 'Страницы', 'icon' => 'fa fa-book', 'url' => ['/admin/pages/index'], 'active' => $this->context->id == 'admin/pages'],
//                            ['label' => 'Рассылки', 'icon' => 'fa fa-paper-plane', 'url' => ['/admin/sends/index'], 'active' => $this->context->id == 'admin/sends'],
//                        ]],
//                        ['label' => 'Мультиязычность', 'icon' => 'folder', 'items' => [
//                            ['label' => 'Сайт', 'icon' => 'fa fa-globe', 'url' => ['/admin/frontend-translation/index'], 'active' => $this->context->id == 'admin/frontend-translation'],
//                            ['label' => 'Языки', 'icon' => 'fa fa-language', 'url' => ['/admin/languages/index'], 'active' => $this->context->id == 'admin/languages'],
//                        ]],
                        ['label' => 'Оплаты', 'icon' => 'folder', 'items' => [
                            ['label' => 'Тарифы', 'icon' => 'fa fa-shopping-cart', 'url' => ['/admin/tariffs/index'], 'active' => $this->context->id == 'admin/tariffs'],
                            ['label' => 'Оплаты', 'icon' => 'fa fa-credit-card', 'url' => ['/admin/payments/index'], 'active' => $this->context->id == 'admin/payments'],
//                            ['label' => 'Выплаты', 'icon' => 'fa fa-credit-card', 'url' => ['/admin/payouts/index'], 'active' => $this->context->id == 'admin/payouts'],
//                            ['label' => 'Промокоды', 'icon' => 'fa fa-code', 'url' => ['/admin/promocodes/index'], 'active' => $this->context->id == 'admin/promocodes'],
                        ]],
//                        ['label' => 'Статистика', 'icon' => 'folder', 'items' => [
//                            ['label' => 'Логирование', 'icon' => 'fa fa-book', 'url' => ['/admin/logging/index'], 'active' => $this->context->id == 'admin/logging' && $this->context->action->id == 'index'],
//                            ['label' => 'Подписки', 'icon' => 'fa fa-check-square', 'url' => ['/admin/logging/statistic'], 'active' => $this->context->id == 'admin/logging' && $this->context->action->id == 'statistic'],
//                            ['label' => 'Новые пользователи', 'icon' => 'fa fa-users', 'url' => ['/admin/logging/graph-user'], 'active' => $this->context->id == 'admin/logging' && $this->context->action->id == 'graph-user'],
//                            ['label' => 'Активность', 'icon' => 'fa fa-mouse-pointer', 'url' => ['/admin/logging/graph-click'], 'active' => $this->context->id == 'admin/logging' && $this->context->action->id == 'graph-click'],
//                            ['label' => 'История кликов', 'icon' => 'fa fa-history', 'url' => ['/admin/button-clicks/index'], 'active' => $this->context->id == 'admin/button-clicks'],
//                        ]],
//                        ['label' => 'Логи ошибок', 'icon' => 'folder', 'items' => [
//                            ['label' => 'Оплаты', 'icon' => 'fa fa-credit-card', 'url' => ['/admin/error-logging/index?name=payment'], 'active' => $this->context->id == 'admin/error-logging' && $this->context->action->controller->actionParams['name'] == 'payment'],
//                            ['label' => 'API', 'icon' => 'fa fa-book', 'url' => ['/admin/error-logging/index?name=api'], 'active' => $this->context->id == 'admin/error-logging' && $this->context->action->controller->actionParams['name'] == 'api'],
//                            ['label' => 'Бот', 'icon' => 'fa fa-cog', 'url' => ['/admin/error-logging/index?name=bot'], 'active' => $this->context->id == 'admin/error-logging' && $this->context->action->controller->actionParams['name'] == 'bot'],
//                            ['label' => 'Рассылки', 'icon' => 'fa fa-paper-plane', 'url' => ['/admin/error-logging/index?name=send'], 'active' => $this->context->id == 'admin/error-logging' && $this->context->action->controller->actionParams['name'] == 'send'],
//                            ['label' => 'Переводы', 'icon' => 'fa fa-globe', 'url' => ['/admin/error-logging/index?name=translation'], 'active' => $this->context->id == 'admin/error-logging' && $this->context->action->controller->actionParams['name'] == 'translation'],
//                        ]],
                    ],
                ]);
            ?>
            <?php endif; ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>