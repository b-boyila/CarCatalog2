<?php

require_once('btce-api.php');
$BTCeAPI = new BTCeAPI(
                    /*API KEY:   */     '2O79HS0C-QSJAHJGU-DWXZIHK1-PWDQAQK1-1DF01FDO',
                    /*API SECRET:*/     'ae7d4f90bc819f34f7a8caf9d3a83725201a4d132ecb13d8fbc57e9f2d30525c'
                      );

$public_trades = $BTCeAPI->getPairTrades('btc_usd');
$history = $public_trades['btc_usd'];

//array to table
echo "<table border=5>";
$names = each($history);
echo "<tr>";
echo "<td>№</td>";
foreach ($names[1] as $name){
	$key = key($names[1]);
	echo "<td>{$key}</td>";
	next($names[1]);
}
echo "</tr>";

foreach ($history as $line){
    echo "<tr>";
    $key = key($history);
    echo "<td>{$key}</td>";
    foreach ($line as $l){
        echo "<td>{$l}</td>";
    }
    echo "</tr>";
    next($history);
}
echo "</table>";

?>