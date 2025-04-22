<?php
/* @var $final_result array */
/** @var yii\web\View $this */

$this->title = 'График активности пользователей';

?>
<style>
    .container {
        margin-right: 5%;
        margin-left: 5%;
        max-width: 85%;
    }
    #container {
        width: 100%;
        height: 70vh; /* optionally set the height */
    }
</style>
<div id="container"></div>
<form>
    <p>Введите количество дней для отслеживания на графике:</p>
    <input type="number" name="days" value="">
    <button class="btn btn-primary" type="submit">Показать</button>
</form>
<script>
    window.addEventListener("load", (event) => {
        Highcharts.chart('container', {
            chart: {
                type: 'spline' // change from 'line' to 'spline' for curves.
            },
            title: {
                text: 'График активности пользователей' // Add your graph's title
            },

            xAxis: {
                title: {
                    text: 'Ось времени' // Add title for x-axis
                },
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'Количество кликов' // Don't forget to name y-Axis too
                }
            },
            series: [{
                data: <?= json_encode($final_result) ?>
            }]
        });
    });

</script>

<script src="https://code.highcharts.com/highcharts.js"></script>
