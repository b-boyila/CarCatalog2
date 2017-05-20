<?php

require_once('btce-api.php');
$BTCeAPI = new BTCeAPI(
                    /*API KEY:   */     '2O79HS0C-QSJAHJGU-DWXZIHK1-PWDQAQK1-1DF01FDO',
                    /*API SECRET:*/     'ae7d4f90bc819f34f7a8caf9d3a83725201a4d132ecb13d8fbc57e9f2d30525c'
                      );

$public_info = $BTCeAPI->getPairsInfo();
$pairs = $public_info['pairs'];

//array to table
echo "<table border=5>";
$names = each($pairs);
echo "<tr>";
echo "<td>pair</td>";
foreach ($names[1] as $name){
	$key = key($names[1]);
	echo "<td>{$key}</td>";
	next($names[1]);
}
echo "</tr>";

foreach ($pairs as $pair){
    echo "<tr>";
    $key = key($pairs);
    echo "<td>{$key}</td>";
    foreach ($pair as $p){
        echo "<td>{$p}</td>";
    }
    echo "</tr>";
    next($pairs);
}
echo "</table>";

?>