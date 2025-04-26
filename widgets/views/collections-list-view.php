<?php

/**
 * Представление для виджета списка подборок
 *
 * @var yii\data\ArrayDataProvider $dataProvider Провайдер данных
 * @var string $title Заголовок виджета
 * @var bool $showTitle Показывать ли заголовок
 * @var bool $showPagination Показывать ли пагинацию
 * @var array $options Дополнительные HTML-атрибуты для контейнера
 * @var string $emptyText Текст для пустого списка
 */

use yii\helpers\Html;
use yii\bootstrap5\LinkPager;
use yii\helpers\Url;
// use yii\widgets\Pjax; // Убираем Pjax

$models = $dataProvider->getModels();
$pagination = $dataProvider->getPagination();
$sort = $dataProvider->getSort(); // Получаем объект сортировки для ссылок

// // Генерируем уникальный ID для Pjax контейнера // Убираем ID Pjax
// $pjaxId = 'collections-list-pjax-' . uniqid();
?>

<?php // Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 5000]); // Убираем Pjax::begin 
?>
<div <?= Html::renderTagAttributes($options) ?>>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <?php if ($showTitle): ?>
            <h2 class="h4 mb-0"><?= Html::encode($title) ?></h2>
        <?php else: ?>
            <div></div> <?php /* Пустой div для выравнивания кнопки */ ?>
        <?php endif; ?>
        <?php /* Кнопка для открытия модального окна создания */ ?>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCollectionModal">
            <i class="fas fa-plus me-1"></i> Новая подборка
        </button>
    </div>

    <?php if (empty($models)): ?>
        <div class="empty-collections-message text-center py-5">
            <i class="fas fa-folder-open text-muted" style="font-size: 48px;"></i>
            <h3 class="mt-3"><?= Html::encode($emptyText) ?></h3>
            <p class="text-muted mb-4">Создайте свою первую подборку, чтобы она отображалась здесь.</p>
            <?php /* Кнопка для открытия модального окна создания (для пустого состояния) - дублируем логику для консистентности */ ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCollectionModal">
                Создать подборку
            </button>
        </div>
    <?php else: ?>
        <!-- Опционально: добавляем сортировку, если нужно -->
        <div class="d-flex justify-content-end mb-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" id="collectionSortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-sort me-1"></i>
                    Сортировка:
                    <?php
                    $currentOrderLabel = 'По дате обновления'; // Текст по умолчанию
                    $currentOrder = $sort->orders;
                    if (!empty($currentOrder)) {
                        $keys = array_keys($currentOrder);
                        $attribute = reset($keys);
                        $direction = $currentOrder[$attribute];
                        $attributeLabels = [ // Мэппинг для читаемых названий
                            'updatedAt' => 'По дате обновления',
                            'name' => 'По названию',
                            'objectCount' => 'По кол-ву объектов',
                            'clientName' => 'По имени клиента',
                        ];
                        $attributeLabel = $attributeLabels[$attribute] ?? ucfirst($attribute);
                        $directionLabel = $direction === SORT_ASC ? ' (возр.)' : ' (убыв.)';
                        $currentOrderLabel = $attributeLabel . $directionLabel;
                    }
                    echo Html::encode($currentOrderLabel);
                    ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="collectionSortDropdown">
                    <?php
                    $sortAttributes = ['updatedAt', 'name', 'objectCount', 'clientName'];
                    $attributeLabels = [
                        'updatedAt' => 'По дате обновления',
                        'name' => 'По названию',
                        'objectCount' => 'По кол-ву объектов',
                        'clientName' => 'По имени клиента',
                    ];
                    foreach ($sortAttributes as $attribute):
                        $label = $attributeLabels[$attribute] ?? ucfirst($attribute);
                    ?>
                        <li><?= $sort->link($attribute, ['label' => $label, 'class' => 'dropdown-item']) ?></li>
                    <?php endforeach; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?= Url::current([$sort->sortParam => 'updatedAt-desc']) // Ссылка на сортировку по умолчанию 
                                                        ?>">Сбросить (по дате)</a></li>
                </ul>
            </div>
        </div>
        <!-- Конец сортировки -->

        <div class="list-group collections-list">
            <?php foreach ($models as $collection): ?>
                <a href="<?= Url::to(['/collection/view', 'id' => $collection['id']]) /* Пример ссылки на просмотр */ ?>" class="list-group-item list-group-item-action collection-item" data-collection-id="<?= $collection['id'] ?>">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 collection-name"><?= Html::encode($collection['name']) ?></h5>
                            <p class="mb-1 text-muted collection-client">
                                <i class="fas fa-user me-1"></i> <?= Html::encode($collection['clientName']) ?>
                            </p>
                            <small class="text-muted collection-params">
                                <i class="fas fa-filter me-1"></i> <?= Html::encode($collection['keyParams']) ?>
                            </small>
                        </div>
                        <div class="text-end ms-3">
                            <span class="badge bg-primary rounded-pill collection-count mb-2" style="font-size: 0.9em;">
                                <?= Html::encode($collection['objectCount']) ?> <?= Yii::t('app', 'объект|объекта|объектов', $collection['objectCount']) ?>
                            </span>
                            <small class="text-muted d-block" title="Последнее обновление">
                                <i class="fas fa-clock me-1"></i> <?= Yii::$app->formatter->asRelativeTime($collection['updatedAt']) ?>
                            </small>
                        </div>
                    </div>
                    <!-- Опционально: кнопки действий -->
                    <div class="collection-actions mt-2 text-end" style="display: none;">
                        <button class="btn btn-sm btn-outline-secondary me-1" title="Редактировать"><i class="fas fa-pencil-alt"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Удалить"><i class="fas fa-trash"></i></button>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($showPagination && $pagination->pageCount > 1): ?>
            <div class="d-flex justify-content-center mt-4 mb-5">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                    'maxButtonCount' => 5,
                    'prevPageLabel' => '<span aria-hidden="true">&laquo;</span>',
                    'nextPageLabel' => '<span aria-hidden="true">&raquo;</span>',
                ]) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php // Pjax::end(); // Убираем Pjax::end 
?>

<?php /* Модальное окно для создания новой подборки */ ?>
<div class="modal fade" id="createCollectionModal" tabindex="-1" aria-labelledby="createCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCollectionModalLabel">Новая подборка</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php /* Форма для создания подборки */ ?>
                <form id="create-collection-form" action="<?= Url::to(['/favorites/create-collection-ajax']) ?>" method="post">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <div class="mb-3">
                        <label for="collection-name-input" class="form-label">Название подборки</label>
                        <input type="text" class="form-control" id="collection-name-input" name="Collection[name]" required placeholder="Например, Квартиры для Ивана">
                        <div class="invalid-feedback" id="collection-name-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary" form="create-collection-form">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<?php
// Скрипт для обработки формы создания подборки
$createUrl = Url::to(['/favorites/create-collection-ajax']);
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
$(document).on('submit', '#create-collection-form', function(e) {
    e.preventDefault(); // Предотвращаем стандартную отправку формы

    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize(); // Собираем данные формы
    var modal = $('#createCollectionModal');
    var input = $('#collection-name-input');
    var errorDiv = $('#collection-name-error');
    var submitButton = form.find('button[type="submit"]');

    // Убираем предыдущие ошибки
    input.removeClass('is-invalid');
    errorDiv.text('');
    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...'); // Блокируем кнопку

    $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(response) {
            if (response.success && response.redirectUrl) {
                // Закрываем модальное окно
                var modalInstance = bootstrap.Modal.getInstance(modal[0]);
                if (modalInstance) {
                     modalInstance.hide();
                }
                // Очищаем поле ввода после успешного создания
                input.val('');
                // Редирект на страницу новой подборки
                window.location.href = response.redirectUrl;
                // Или можно обновить Pjax контейнер, если не нужен редирект:
                // $.pjax.reload({container: '#pjax-container-id', async: false}); // Убрали переменную PHP

            } else if (response.errors && response.errors.name) {
                // Показываем ошибку валидации для поля name
                input.addClass('is-invalid');
                errorDiv.text(response.errors.name.join(', '));
             } else {
                 // Показываем общую ошибку, если что-то пошло не так
                 input.addClass('is-invalid');
                 errorDiv.text('Произошла неизвестная ошибка. Попробуйте еще раз.');
                 console.error('Collection creation failed:', response);
            }
        },
        error: function(xhr, status, error) {
            // Показываем ошибку AJAX запроса
            input.addClass('is-invalid');
            errorDiv.text('Ошибка отправки запроса: ' + error);
            console.error('AJAX Error:', status, error);
        },
        complete: function() {
            // Разблокируем кнопку после завершения запроса
            submitButton.prop('disabled', false).text('Сохранить');
        }
    });
});

// Очистка ошибок при закрытии модального окна
$('#createCollectionModal').on('hidden.bs.modal', function () {
    var input = $('#collection-name-input');
    var errorDiv = $('#collection-name-error');
    input.removeClass('is-invalid').val(''); // Сбрасываем значение и стиль
    errorDiv.text('');
    // Возвращаем исходное состояние кнопки
    $('#create-collection-form button[type="submit"]').prop('disabled', false).text('Сохранить');
});

JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>