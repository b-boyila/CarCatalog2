<?php
require_once dirname(__FILE__) . '/../helper/logger/logger.php';
require_once dirname(__FILE__) . '/../helper/constants.php';
require_once dirname(__FILE__) . '/btce-api.php';

$user_login = $argv[1];
$step = (int) $argv[2];

$pair = 'btc_usd';

if (($pairs = fopen(constants::CSV_PATH . $pair . constants::CSV_TYPE, "r")) !== FALSE) {

	$l = 0;
    while (($pair = fgetcsv($pairs, 1000, ";")) !== FALSE) {

            $last[$l] = $pair[5];

            $l++;
    }
    fclose($pairs);

    $startIndex = $l - (35 * $step);
    $ema1 = array();
    $ema2 = array();
    $macdLine = array();
    $signalLine = array();
    for ($n = 0; $n < 35; $n++) {

        if ($n == 11) {
            $ema1[0] = 0;
            for ($i = 0; $i <= 11; $i++) {
                $ema1[0] += $last[$startIndex + ($i * $step)];                
            }
            $ema1[0] = $ema1[0]/12;
        }
            
        if ($n > 11) {
            $ema1[$n-11] = ((2/(1+12))*$last[$startIndex + ($n * $step)])+(((1-(2/(1+12)))*$ema1[$n-12]));
        }

        if ($n == 25) {
            $ema2[0] = 0;
            for ($i = 0; $i <= 25; $i++) {
                $ema2[0] += $last[$startIndex + ($i * $step)];
            }
            $ema2[0] = $ema2[0]/26;
            $macdLine[0] = $ema1[$n-11] - $ema2[$n-25];
        }

        if ($n > 25) {
            $ema2[$n-25] = ((2/(1+26))*$last[$startIndex + ($n * $step)])+(((1-(2/(1+26)))*$ema2[$n-26]));
            $macdLine[$n-25] = $ema1[$n-11] - $ema2[$n-25];
        }

        if ($n >= 33) {
            $signalLine[$n-33] = 0;
            for ($i = 0; $i <= 9; $i++) {
                $signalLine[$n-33] += $macdLine[$n-33+$i];
            }
            $signalLine[$n-33] = $signalLine[$n-33]/9;
            $histogram[$n-33] = $macdLine[$n-25] - $signalLine[$n-33];
        }

    }
    $msg = 'Ничего не делаем';
    if (($histogram[0] < 0) && ($histogram[1] > 0)) $msg = 'Нужно покупать!';
    if (($histogram[0] > 0) && ($histogram[1] < 0)) $msg = 'Нужно продавать!';

    $log = new KLogger (constants::LOG_PATH . $user_login . constants::LOG_TYPE, KLogger::DEBUG);
    $log->logInfo('Анализировать.. Пользователь: ' . $user_login . ' Шаг: ' .$step. ' histogram1='.$histogram[0].', histogram2='.$histogram[1].' -> '.$msg);  
}

?>