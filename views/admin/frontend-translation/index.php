<?php
/** @var yii\web\View $this */
/** @var array $siteLanguages */
/** @var array $authLanguages */

use app\models\Languages;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Переводы фронтенда';
$allLanguages = Languages::find()->select('name')->orderBy(['name' => SORT_ASC])->asArray()->indexBy('slug')->column();

$btnName = str_contains(Yii::$app->request->absoluteUrl, 'store')
    ? 'Копировать с теста на прод'
    : 'Копировать с прода на тест';
$btnTitle = str_contains(Yii::$app->request->absoluteUrl, 'store')
    ? 'Скопировать с ТЕСТОВОГО фронтенда на фронтенд ПРОДА(боевого) все файлы перевода(сайта и авторизации). Это нужно если на боевом не актуальный перевод,а сам процесс перевода делали на тестовом'
    : 'Скопировать с фронтенда ПРОДА(боевого) на ТЕСТОВЫЙ фронтенд все файлы перевода(сайта и авторизации). Используем когда поменялись/добавились ключи переводов на боевом фронтенде';
?>
<div>
    <?= Html::a('Копировать оригиналы с фронтенда', ['/admin/queue/copy-original'], [
            'class' => 'btn btn-warning',
            'title' => 'Копировать оригиналы с фронтенда на бэк. Это нужно чтобы на бэке были актуальные версии переводов. Скопирует все переводы и для сайта и для авторизации. Работает в рамках того сайта на котором нажать(тестовый или прод).'
    ]) ?>
    <?= Html::a('Отправить на фронтенд', ['/admin/queue/copy-translations'], [
            'class' => 'btn btn-danger mx-3',
            'title' => 'Копировать с бэка на фронтенд. Это нужно только если на бэке(в админке) вносились изменения в файлы переводов. Скопирует и для сайта и для авторизации. Работает в рамках того сайта на котором нажать(тестовый или прод).'
    ]) ?>

    <?= Html::a($btnName, ['/admin/queue/copy-to-prod'], ['class' => 'btn btn-success', 'title' => $btnTitle]) ?>
</div>
<br>
<div style="background: #dee2e6">
    <?php $form = ActiveForm::begin([
        'action' => ['create'],
        'method' => 'get',
    ]); ?>
    <?= Html::dropDownList(
        'newLang',
        null,
        $allLanguages
        , ['class' => 'form-control'])
    ?>
    <?= Html::radioList('auth', 0, [
        1 => 'Перевод авторизации',
        0 => 'Перевод сайта'
    ], [
        'separator' => '<br>',
    ]) ?>
    <?= Html::submitButton('Создать новый файл перевода (НЕ язык)', [
            'class' => 'btn btn-success my-3',
            'title' => 'необходимо выбрать язык(на какой переводим),также выбрать, что это будет, перевод для сайта или для приложения авторизации. Создаст новый файл переводов под выбранный язык. Если уже такой есть,то не создастся новый. Файл отобразится в списке ниже и останется в памяти,но только на бэке,для переноса(копирования на фронтенд) нужно нажать соответствующую кнопку(Отправить на фронтенд)'
    ]) ?>
    <?php ActiveForm::end(); ?>
</div>
<br>
<div>Список доступных переводов сайта(тексты и кнопки сайта):</div>
<ul>
    <?php foreach ($siteLanguages as $language): ?>
        <li>
            <?= Html::a($allLanguages[$language] ?? $language, ['edit', 'lang' => $language]) ?>
        </li>
    <?php endforeach; ?>
</ul>
<div>Список доступных переводов приложения авторизации(тексты и кнопки в блоке авторизации):</div>
<ul>
    <?php foreach ($authLanguages as $language): ?>
        <li>
            <?= Html::a($allLanguages[$language] ?? $language, ['edit', 'lang' => $language, 'auth' => true]) ?>
        </li>
    <?php endforeach; ?>
</ul>
