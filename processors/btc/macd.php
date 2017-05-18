<?php

require_once('btce-api.php');
$BTCeAPI = new BTCeAPI(
                    /*API KEY:   */     '2O79HS0C-QSJAHJGU-DWXZIHK1-PWDQAQK1-1DF01FDO',
                    /*API SECRET:*/     'ae7d4f90bc819f34f7a8caf9d3a83725201a4d132ecb13d8fbc57e9f2d30525c'
                      );


$history_trades = $BTCeAPI->getPairTrades('btc_usd');
print_r($history_trades);

?>