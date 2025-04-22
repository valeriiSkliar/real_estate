<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var string $name */

$this->title = 'Логи '  . $name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="parser-index">
    <p>
        <?= Html::a('Очистить лог', ['clear-log?name=' . $name], ['class' => 'btn btn-danger']) ?>
    </p>
    <div id="log-container"></div>
</div>

<script>
    function fetchLogs() {
        $.ajax({
            url: '/admin/error-logging/fetch-logs?name=' + <?= json_encode($name) ?>,
            success: function (data) {
                // Check if data is not empty
                if (data) {
                    // Split the log messages with line breaks
                    var logMessages = data.split('\n');

                    // Wrap each log message with <p> tags
                    var formattedLogs = logMessages.map(function (message) {
                        return '<p>' + message + '</p>';
                    });

                    // Join the formatted log messages with line breaks
                    var logsHtml = formattedLogs.join('\n');

                    // Update the log container
                    $('#log-container').html(logsHtml);
                } else {
                    // No log messages available
                    $('#log-container').text('Нет новых сообщений.');
                }
            },
            complete: function () {
                setTimeout(fetchLogs, 5000); // Fetch logs every 5 seconds
            }
        });
    }
    window.addEventListener("load", (event) => {
        // Start fetching logs initially
        fetchLogs();
    });
</script>