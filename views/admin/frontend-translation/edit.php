<?php
/** @var yii\web\View $this */
/** @var string $lang */
/** @var array $translations */

use yii\helpers\Html;

$this->title = "Редактирование языка: $lang";
?>

<h1>Редактирование языка: <?= Html::encode($lang) ?></h1>

<?= Html::beginForm(['edit', 'lang' => $lang], 'post') ?>

<?php
/**
 * Рекурсивная функция для генерации HTML-формы из вложенного массива
 */
function renderTranslationForm($translations, $prefix = '')
{
    foreach ($translations as $key => $value) {
        $name = $prefix ? "{$prefix}[{$key}]" : $key;

        echo '<div style="margin-left: 20px;">';
        echo '<label><strong>' . Html::encode($key) . ':</strong></label>';

        if (is_array($value)) {
            // Если значение массив, обрабатываем рекурсивно
            renderTranslationForm($value, $name);
        } else {
            // Если значение строка, отображаем поле ввода
            echo Html::textInput("translations[$name]", $value, [
                'class' => 'form-control',
                'style' => 'margin-top: 5px;',
            ]);
        }

        echo '</div>';
    }
}
?>

<?= Html::beginForm(['edit', 'lang' => $lang], 'post') ?>

<div>
    <?php renderTranslationForm($translations); ?>
</div>

<div style="margin-top: 20px;">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>

<?= Html::endForm() ?>
