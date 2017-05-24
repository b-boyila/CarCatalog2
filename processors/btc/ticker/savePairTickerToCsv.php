<?php
require_once dirname(__FILE__) . '/../../helper/constants.php';
require_once dirname(__FILE__) . '/../btce-api.php';

$pair = $argv[1];
$file = $pair;

$BTCeAPI = new BTCeAPI(
/*API KEY:   */     '2O79HS0C-QSJAHJGU-DWXZIHK1-PWDQAQK1-1DF01FDO',
    /*API SECRET:*/     'ae7d4f90bc819f34f7a8caf9d3a83725201a4d132ecb13d8fbc57e9f2d30525c'
);

$pair = $BTCeAPI->getPairTicker($pair)[$pair];

$pair = array($pair['high'],
              $pair['low'],
              $pair['avg'],
              $pair['vol'],
              $pair['vol_cur'],
              $pair['last'],
              $pair['buy'],
              $pair['sell'],
              $pair['updated']
              );

$fp = fopen(constants::CSV_PATH . $file . constants::CSV_TYPE, 'a');
fputcsv($fp, $pair, ";");
fclose($fp);