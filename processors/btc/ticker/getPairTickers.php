<?php
require_once dirname(__FILE__) . '/../../helper/logger/logger.php';
require_once dirname(__FILE__) . '/../../helper/constants.php';
header(constants::CONTENT_TYPE);

$pair = 'btc_usd';

if (($pairs = fopen(constants::CSV_PATH . $pair . constants::CSV_TYPE, "r")) !== FALSE) {

    echo "<table border='1'>
      <tr>
                <th>Макcимальная цена</th>
                <th>Минимальная цена</th>
                <th>Средняя цена</th>
                <th>Объем торгов</th>
                <th>Объем торгов в валюте</th>
                <th>Цена последней сделки</th>
                <th>Цена покупки</th>
                <th>Цена продажи</th>
                <th>Последнее обновление кэша</th>
      </tr>";

    while (($pair = fgetcsv($pairs, 1000, ";")) !== FALSE) {
            $high = $pair[0];
            $low = $pair[1];
            $avg = $pair[2];
            $vol = $pair[3];
            $vol_cur = $pair[4];
            $last = $pair[5];
            $buy = $pair[6];
            $sell = $pair[7];
            $updated = $pair[8];

            echo "<tr>";
            echo "<td>" . $high . "</td>";
            echo "<td>" . $low . "</td>";
            echo "<td>" . $avg . "</td>";
            echo "<td>" . $vol . "</td>";
            echo "<td>" . $vol_cur . "</td>";
            echo "<td>" . $last . "</td>";
            echo "<td>" . $buy . "</td>";
            echo "<td>" . $sell . "</td>";
            echo "<td>" . $updated . "</td>";
            echo "</tr>";
    }
    echo "</table>";
    fclose($pairs);
}