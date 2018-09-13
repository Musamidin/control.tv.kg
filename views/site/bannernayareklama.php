<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'Размещение бегущей строки на все ТВ и Радио! «онлайн сервис»';
?>
<div class="site-index">
    <div class="row">
            <div class="col-md-12">
            <table>
                <thead>
                    <tr>
                        <th>Channal</th>
                        <th>text</th>
                        <th>Dates</th>
                    </tr>
                </thead>

            <?php
                foreach ($data as $items) {
                    echo '<tr>';
                    echo '<td>'.$items['channels'].'</td><td>'.$items['text'].'</td><td>'.$items['dates'].'</td>';
                    //print_r($items);
                    echo '</tr>';
                }
            ?>
            </table>
            <? echo '<pre>'; ?>
            <?=print_r($data); ?>
            <? echo '</pre>'; ?>
            </div>
    </div>
</div>