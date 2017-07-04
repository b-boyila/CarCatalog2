<?php
require_once dirname(__FILE__) . '/../helper/logger/logger.php';
require_once dirname(__FILE__) . '/../helper/constants.php';
require_once dirname(__FILE__) . '/btce-api.php';

$user_login = $argv[1];
$step = $argv[2] + 1;
$pair = 'btc_usd';

//MACD
$last_prices_trades_for_macd = getLastPricesTradesPairForMACD($pair, $step);
$ema1_values = getEma1Values($last_prices_trades_for_macd);
$ema2_values = getEma2Values($last_prices_trades_for_macd);
$macd_line_values = getMacdLineValues($ema1_values, $ema2_values);
$signal_line_values = getSignalLineValues($macd_line_values);
$histogram_values = getHistogramValues($macd_line_values, $signal_line_values);

//RSI STOCHASTIC
$last_prices_trades = getLastPricesTradesPair($pair);
$last_prices_trades_for_rsi = getLastPricesTradesPairForRSI($last_prices_trades, $step);
$rsi_values = getRSIValues($last_prices_trades_for_rsi);
$rsi_values_min = getRSIminValues($rsi_values);
$rsi_values_max = getRSImaxValues($rsi_values);
$lowest_low_values = getLowestLowValues($rsi_values_min);
$highest_high_values = getHighestHighValues($rsi_values_max);
$k_fast_values = getKfastValues($rsi_values, $lowest_low_values, $highest_high_values);
$k_values = getKValues($k_fast_values);
$d_value = getDValue($k_values);

//PARABOLIC SAR
$last_prices_trades = getLastPricesTradesPair($pair);
$high_prices_trades = getHighPricesTradesPairForSAR($last_prices_trades, $step);
$low_prices_trades = getLowPricesTradesPairForSAR($last_prices_trades, $step);
$trend_directions = getTrendDirections($high_prices_trades, $low_prices_trades);

$isBuy = ($histogram_values[0] < 0) && ($histogram_values[1] > 0);
$isSell = ($histogram_values[0] > 0) && ($histogram_values[1] < 0);
$log = new KLogger (constants::LOG_PATH . $user_login . constants::LOG_TYPE, KLogger::DEBUG);
$log->logInfo('Анализировать.. Пользователь: ' . $user_login . ' Шаг: ' . ($step - 1) . ' histogram(до) = ' . $histogram_values[0] . ', histogram(сейчас) = ' . $histogram_values[1]. ', K = ' . $k_values[4]. ', D = ' . $d_value. ', ТРЕНД => '.$trend_directions[4]);

function getLastPricesTradesPair($pair)
{
    $last_prices_trades = array();
    if (($pairs = fopen(constants::CSV_PATH . $pair . constants::CSV_TYPE, "r")) !== FALSE) {
        while (($pair = fgetcsv($pairs, 1000, ";")) !== FALSE) {
            array_push($last_prices_trades, $pair[5]);
        }
        fclose($pairs);
    }
    return $last_prices_trades;
}

function getLastPricesTradesPairForMACD($pair, $step)
{
    $last_prices_trades = getLastPricesTradesPair($pair);
    $last_prices_trades_for_macd = array();
    for($i = 0; $i < constants::MACD_POINTS; $i++) {
    	array_push($last_prices_trades_for_macd, $last_prices_trades[count($last_prices_trades) - 1 - ($step * $i)]);
    }
    return array_reverse($last_prices_trades_for_macd);
}

function getAvarageValue($array, $start_index, $count) {
    $array= array_slice($array, $start_index, $count);
    $avarage_value = array_sum($array) / $count;
    return $avarage_value;
}

function getEma1Values($last_prices_trades) {
    $ema1_values = array();
    $average_value = getAvarageValue($last_prices_trades, 0, 12);
    array_push($ema1_values, $average_value);
    $last_prices_trades = array_slice($last_prices_trades, 11);
    for($i = 1; $i < count($last_prices_trades); $i++) {
        $ema1_value = ((2 / (1 + 12)) * $last_prices_trades[$i]) + (((1 - (2 / (1 + 12))) * $ema1_values[$i-1]));
        array_push($ema1_values, $ema1_value);
    }
    return $ema1_values;
}

function getEma2Values($last_prices_trades) {
    $ema2_values = array();
    $average_value = getAvarageValue($last_prices_trades, 0, 26);
    array_push($ema2_values, $average_value);
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

function getLastPricesTradesPairForRSI($last_prices_trades, $step)
{
    $last_prices_trades_for_rsi = array();
    for($i = 0; $i < (3 * (constants::RSI_POINTS)) + (constants::STOCHASTIC_POINTS) - 2; $i++) {
    	array_push($last_prices_trades_for_rsi, $last_prices_trades[count($last_prices_trades) - 1 - ($step * $i)]);
    }
    return array_reverse($last_prices_trades_for_rsi);
}

function getRSIValues($last_prices_trades) {
	$rsi_values = array();
	$change_values = getChangeValues($last_prices_trades);
	$gain_values = getGainValues($change_values);
	$loss_values = getLossValues($change_values);
	$avg_gain_values = getAvgGainOrLossValues($gain_values);
	$avg_loss_values = getAvgGainOrLossValues($loss_values);
	$rs_values = getRSValues($avg_gain_values, $avg_loss_values);

	for($i = 0; $i < (2 * (constants::RSI_POINTS - 1)) + (constants::STOCHASTIC_POINTS); $i++) {
		$rsi_value = 100;
		if ($avg_loss_values[$i] <> 0) {
			$rsi_value = 100 - (100/(1 + $rs_values[$i]));
		}
		array_push($rsi_values, $rsi_value);
	}
	return $rsi_values;
}

function getChangeValues($last_prices_trades) {
	$change_values = array();
	for($i = 1; $i < count($last_prices_trades); $i++) {
		$change_value = $last_prices_trades[$i] - $last_prices_trades[$i-1];
		array_push($change_values, $change_value);
	}
	return $change_values;
}

function getGainValues($change_values) {
	for($i = 0; $i < count($change_values); $i++) {
		if ($change_values[$i] <= 0) {
			$change_values[$i] = 0;
		}
	}
	return $change_values;
}

function getLossValues($change_values) {
	for($i = 0; $i < count($change_values); $i++) {
		if ($change_values[$i] < 0) {
			$change_values[$i] = -$change_values[$i];
		} else {
			$change_values[$i] = 0;
		}
	}
	return $change_values;
}

function getAvgGainOrLossValues($values) {
	$avg_values = array();
	$avg_value = getAvarageValue($values, 0, constants::RSI_POINTS);
	array_push($avg_values, $avg_value);
	for($i = 1; $i < (2 * (constants::RSI_POINTS - 1)) + (constants::STOCHASTIC_POINTS); $i++) {
		$avg_value = (($avg_values[$i-1] * ((constants::RSI_POINTS) - 1)) + $values[(constants::RSI_POINTS) + $i - 1])/constants::RSI_POINTS;
		array_push($avg_values, $avg_value);
	}
	return $avg_values;
}

function getRSValues($avg_gain_values, $avg_loss_values) {
	$rs_values = array();
	for($i = 0; $i < (2 * (constants::RSI_POINTS - 1)) + (constants::STOCHASTIC_POINTS); $i++) {
		$rs_value = $avg_gain_values[$i]/$avg_loss_values[$i];
		array_push($rs_values, $rs_value);
	}
	return $rs_values;
}

function getRSIminValues($rsi_values) {
	$rsi_values_min = array();
	for($i = 0; $i < (constants::RSI_POINTS) + (constants::STOCHASTIC_POINTS) - 1; $i++) {
		$rsi_values_step = array_slice($rsi_values, $i, constants::RSI_POINTS);
		$rsi_value_min = min($rsi_values_step);
		array_push($rsi_values_min, $rsi_value_min);
	}
	return $rsi_values_min;
}

function getRSImaxValues($rsi_values) {
	$rsi_values_max = array();
	for($i = 0; $i < (constants::RSI_POINTS) + (constants::STOCHASTIC_POINTS) - 1; $i++) {
		$rsi_values_step = array_slice($rsi_values, $i, constants::RSI_POINTS);
		$rsi_value_max = max($rsi_values_step);
		array_push($rsi_values_max, $rsi_value_max);
	}
	return $rsi_values_max;
}


function getLowestLowValues($rsi_values_min) {
	$lowest_low_values = array();
	for($i = 0; $i < constants::STOCHASTIC_POINTS; $i++) {
		$lowest_low_values_step = array_slice($rsi_values_min, $i, constants::RSI_POINTS);
		$lowest_low_value = min($lowest_low_values_step);
		array_push($lowest_low_values, $lowest_low_value);
	}
	return $lowest_low_values;
}

function getHighestHighValues($rsi_values_max) {
	$highest_high_values = array();
	for($i = 0; $i < constants::STOCHASTIC_POINTS; $i++) {
		$highest_high_values_step = array_slice($rsi_values_max, $i, constants::RSI_POINTS);
		$highest_high_value = max($highest_high_values_step);
		array_push($highest_high_values, $highest_high_value);
	}
	return $highest_high_values;
}

function getKfastValues($rsi_values, $lowest_low_values, $highest_high_values) {
	$k_fast_values = array();
	for($i = 0; $i < constants::STOCHASTIC_POINTS; $i++) {
		$k_fast_value = ($rsi_values[$i + (2 * ((constants::RSI_POINTS) - 1))] - $lowest_low_values[$i])/($highest_high_values[$i] - $lowest_low_values[$i]);
		array_push($k_fast_values, $k_fast_value);
	}
	return $k_fast_values;
}

function getKValues($k_fast_values) {
	$k_values = array();
	for($i = 0; $i < constants::STOCHASTIC_POINTS - 2; $i++) {
		$k_value = getAvarageValue($k_fast_values, $i, constants::STOCHASTIC_POINTS - 4);
		array_push($k_values, $k_value);
	}
	return $k_values;
}

function getDValue($k_values) {
	$d_value = getAvarageValue($k_values, 0, 5);
	return $d_value;
}

function getHighPricesTradesPairForSAR($last_prices_trades, $step) {
	$high_prices_trades = array();
    for($i = 0; $i < (constants::PARABOLIC_POINTS) + (constants::TRENDS_POINTS) - 1; $i++) {
    	$high_price_step = array_slice($last_prices_trades, -$step*$i, $step);
    	$high_price = max($high_price_step);
    	array_push($high_prices_trades, $high_price);
    }
    return array_reverse($high_prices_trades);
}

function getLowPricesTradesPairForSAR($last_prices_trades, $step) {
	$low_prices_trades = array();
    for($i = 0; $i < (constants::PARABOLIC_POINTS) + (constants::TRENDS_POINTS) - 1; $i++) {
    	$low_price_step = array_slice($last_prices_trades, -$step*$i, $step);
    	$low_price = min($low_price_step);
    	array_push($low_prices_trades, $low_price);
    }
    return array_reverse($low_prices_trades);
}

function getTrendDirections($high_prices_trades, $low_prices_trades) {
	$sar_values = array();
	$ep_values = array();
	$ep_sar_values = array();
	$trend_directions = array();
	$af_values = array();
	$af_ep_sar_values = array();

	for($i = 0; $i < constants::TRENDS_POINTS; $i++) {
		$sar_value = getSARValue($high_prices_trades, $low_prices_trades, $i, $trend_directions, $ep_values, $sar_values, $af_ep_sar_values);
		array_push($sar_values, $sar_value);
		$ep_value = getEPValue($high_prices_trades, $low_prices_trades, $i, $trend_directions, $ep_values);
		array_push($ep_values, $ep_value);
		$ep_sar_value = $ep_value - $sar_value;
		array_push($ep_sar_values, $ep_sar_value);
		$trend_direction = getTrendDirection($high_prices_trades, $low_prices_trades, $i, $trend_directions, $sar_values);
		array_push($trend_directions, $trend_direction);
		$af_value = getAFValue($trend_directions, $high_prices_trades, $low_prices_trades, $i, $ep_values, $af_values);
		array_push($af_values, $af_value);
		$af_ep_sar_value = $af_value * $ep_sar_value;
		array_push($af_ep_sar_values, $af_ep_sar_value);
	}
	return $trend_directions;
}

function getSARValue($high_prices_trades, $low_prices_trades, $index, $trend_directions, $ep_values, $sar_values, $af_ep_sar_values) {
	$sar_value = 0;
	if ($index == 0) {
		$high_prices_trades_slice = array_slice($high_prices_trades, $index, constants::PARABOLIC_POINTS);
		$low_prices_trades_slice = array_slice($low_prices_trades, $index, constants::PARABOLIC_POINTS);
		$high_prices_trades_slice_min = min($high_prices_trades_slice);
		$low_prices_trades_slice_min = min($low_prices_trades_slice);
		$sar_value = min($high_prices_trades_slice_min, $low_prices_trades_slice_min);
	} else {
		$trend_direction_before = $trend_directions[$index-2];
		if ($index == 1) {
			$trend_direction_before = "UP";
		}
		if ($trend_directions[$index-1] == $trend_direction_before) {
			if ($trend_directions[$index-1] == "UP") {
				if (($sar_values[$index-1] + $af_ep_sar_values[$index-1]) < min($low_prices_trades[constants::PARABOLIC_POINTS + $index - 2], $low_prices_trades[constants::PARABOLIC_POINTS + $index - 3])) {
					$sar_value = $sar_values[$index-1] + $af_ep_sar_values[$index-1];
				} else {
					$sar_value = min($low_prices_trades[constants::PARABOLIC_POINTS + $index - 2], $low_prices_trades[constants::PARABOLIC_POINTS + $index - 3]);
				}
			} else {
				if (($sar_values[$index-1] + $af_ep_sar_values[$index-1]) > max($high_prices_trades[constants::PARABOLIC_POINTS + $index - 2], $high_prices_trades[constants::PARABOLIC_POINTS + $index - 3])) {
					$sar_value = $sar_values[$index-1] + $af_ep_sar_values[$index-1];
				} else {
					$sar_value = max($high_prices_trades[constants::PARABOLIC_POINTS + $index - 2], $high_prices_trades[constants::PARABOLIC_POINTS + $index - 3]);
				}
			}
		} else {
			$sar_value = $ep_values[$index-1];
		}
	}
	return $sar_value;
}

function getEPValue($high_prices_trades, $low_prices_trades, $index, $trend_directions, $ep_values) {
	$ep_value = 0;
	if ($index == 0) {
		$high_prices_trades_slice = array_slice($high_prices_trades, $index, constants::PARABOLIC_POINTS);
		$low_prices_trades_slice = array_slice($low_prices_trades, $index, constants::PARABOLIC_POINTS);
		$high_prices_trades_slice_max = max($high_prices_trades_slice);
		$low_prices_trades_slice_max = max($low_prices_trades_slice);
		$ep_value = max($high_prices_trades_slice_max, $low_prices_trades_slice_max);
	} else {
		if ($trend_directions[$index-1] == "UP") {
			if ($high_prices_trades[constants::PARABOLIC_POINTS + $index - 1] > $ep_values[$index - 1]) {
				$ep_value = $high_prices_trades[constants::PARABOLIC_POINTS + $index - 1];
			} else {
				$ep_value = $ep_values[$index - 1];
			}
		} else {
			if ($low_prices_trades[constants::PARABOLIC_POINTS + $index - 1] < $ep_values[$index - 1]) {
				$ep_value = $low_prices_trades[constants::PARABOLIC_POINTS + $index - 1];
			} else {
				$ep_value = $ep_values[$index - 1];
			}
		}
	}
	return $ep_value;
}

function getTrendDirection($high_prices_trades, $low_prices_trades, $index, $trend_directions, $sar_values) {
	$trend_direction = "";
	if ($index == 0) {
		if ($low_prices_trades[(constants::PARABOLIC_POINTS) + $index - 1] > $sar_values[$index]) {
			$trend_direction = "UP";
		} else {
			$trend_direction = "DOWN";
		}
	} else {
		if ($trend_directions[$index-1] == "UP") {
			if ($low_prices_trades[constants::PARABOLIC_POINTS + $index - 1] > $sar_values[$index]) {
				$trend_direction = "UP";
			} else {
				$trend_direction = "DOWN";
			}
		} else {
			if ($high_prices_trades[constants::PARABOLIC_POINTS + $index - 1] < $sar_values[$index]) {
				$trend_direction = "DOWN";
			} else {
				$trend_direction = "UP";
			}
		}
	}
	return $trend_direction;
}

function getAFValue($trend_directions, $high_prices_trades, $low_prices_trades, $index, $ep_values, $af_values) {
	$af_value = 0;
	if ($index == 0) {
		$af_value = constants::STEP_AF;
	} else {
		if ($trend_directions[$index] == $trend_directions[$index-1]) {
			if ($trend_directions[$index] == "UP") {
				if ($ep_values[$index] > $ep_values[$index-1]) {
					if ($af_values[$index-1] == constants::MAX_AF) {
						$af_value = $af_values[$index-1];
					} else {
						$af_value = $af_values[$index-1] + constants::STEP_AF;
					}
				} else {
					$af_value = $af_values[$index-1];
				}
			} else {
				if ($ep_values[$index] < $ep_values[$index-1]) {
					if ($af_values[$index-1] == constants::MAX_AF) {
						$af_value = $af_values[$index-1];
					} else {
						$af_value = $af_values[$index-1] + constants::STEP_AF;
					}
				} else {
					$af_value = $af_values[$index-1];
				}
			}
		} else {
			$af_value = constants::STEP_AF;
		}
	}
	return $af_value;
}

?>