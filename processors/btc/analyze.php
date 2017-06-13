<?php
require_once dirname(__FILE__) . '/../helper/logger/logger.php';
require_once dirname(__FILE__) . '/../helper/constants.php';
require_once dirname(__FILE__) . '/btce-api.php';

$user_login = $argv[1];
$step = $argv[2];
$pair = 'btc_usd';

$last_prices_trades = getLastPricesTradesPair($pair, $step);
$ema1_values = getEma1Values($last_prices_trades);
$ema2_values = getEma2Values($last_prices_trades);
$macd_line_values = getMacdLineValues($ema1_values, $ema2_values);
$signal_line_values = getSignalLineValues($macd_line_values);
$histogram_values = getHistogramValues($macd_line_values, $signal_line_values);
$reaction = chooseReaction($histogram_values);

$log = new KLogger (constants::LOG_PATH . $user_login . constants::LOG_TYPE, KLogger::DEBUG);
$log->logInfo('Анализировать.. Пользователь: ' . $user_login . ' Шаг: ' . $step . ' histogram(До)=' . $histogram_values[0] . ', histogram(Сейчас)=' . $histogram_values[1]. ' ---> ' . $reaction);

function getLastPricesTradesPair($pair, $step)
{
    $last_prices_trades = array();
    if (($pairs = fopen(constants::CSV_PATH . $pair . constants::CSV_TYPE, "r")) !== FALSE) {
        while (($pair = fgetcsv($pairs, 1000, ";")) !== FALSE) {
            array_push($last_prices_trades, $pair[5]);
        }
        fclose($pairs);
    }
    $last_prices_trades_for_macd = array();
    for($i = 0; $i < constants::FOR_CALCULATE; $i++) {
    	array_push($last_prices_trades_for_macd, $last_prices_trades[count($last_prices_trades) - 1 - ($step * $i)]);
    }
    return $last_prices_trades_for_macd;
}

function getAvarageValue($array, $start_index, $count) {
    $array= array_slice($array, $start_index, $count);
    $avarage_value = array_sum($array) / $count;
    return $avarage_value;
}

function getEma1Values($last_prices_trades) {
    $ema1_values = array();
    array_push($ema1_values, getAvarageValue($last_prices_trades, 0, 12));
    $last_prices_trades = array_slice($last_prices_trades, 11);
    for($i = 1; $i < count($last_prices_trades); $i++) {
        $ema1_value = ((2 / (1 + 12)) * $last_prices_trades[$i]) + (((1 - (2 / (1 + 12))) * $ema1_values[$i-1]));
        array_push($ema1_values, $ema1_value);
    }
    return $ema1_values;
}

function getEma2Values($last_prices_trades) {
    $ema2_values = array();
    array_push($ema2_values, getAvarageValue($last_prices_trades, 0, 26));
    $last_prices_trades = array_slice($last_prices_trades, 25);
    for($i = 1; $i < count($last_prices_trades); $i++) {
        $ema2_value = ((2 / (1 + 26)) * $last_prices_trades[$i]) + (((1 - (2 / (1 + 26))) * $ema2_values[$i-1]));
        array_push($ema2_values, $ema2_value);
    }
    return $ema2_values;
}

function getMacdLineValues($ema1_values, $ema2_values) {
	$macd_line_values = array();
	$ema1_values = array_slice($ema1_values, 14);
	for($i = 0; $i < count($ema2_values); $i++) {
		$macd_line_value = $ema1_values[$i] - $ema2_values[$i];
		array_push($macd_line_values, $macd_line_value);
	}
	return $macd_line_values;
}

function getSignalLineValues($macd_line_values) {
	$signal_line_values = array();
	for($i = 0; $i < (count($macd_line_values) - 8); $i++) {
		$signal_line_value = getAvarageValue($macd_line_values, $i, 9);
		array_push($signal_line_values, $signal_line_value);
	}
	return $signal_line_values;
}

function getHistogramValues($macd_line_values, $signal_line_values) {
	$histogram_values = array();
	$macd_line_values = array_slice($macd_line_values, 8);
	for($i = 0; $i < count($signal_line_values); $i++) {
		$histogram_value = $macd_line_values[$i] - $signal_line_values[$i];
		array_push($histogram_values, $histogram_value);
	}
	return $histogram_values;
}

function chooseReaction($histogram_values) {
	$reaction = 'Ничего не делаем';
    if (($histogram_values[0] < 0) && ($histogram_values[1] > 0)) $reaction = 'Нужно покупать!';
    if (($histogram_values[0] > 0) && ($histogram_values[1] < 0)) $reaction = 'Нужно продавать!';
    return $reaction;
}

?>