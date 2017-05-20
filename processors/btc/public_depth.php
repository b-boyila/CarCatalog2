<?php

require_once('btce-api.php');
$BTCeAPI = new BTCeAPI(
                    /*API KEY:   */     '2O79HS0C-QSJAHJGU-DWXZIHK1-PWDQAQK1-1DF01FDO',
                    /*API SECRET:*/     'ae7d4f90bc819f34f7a8caf9d3a83725201a4d132ecb13d8fbc57e9f2d30525c'
                      );

$public_depth = $BTCeAPI->getPairDepth('btc_usd');
$activity = $public_depth['btc_usd'];

//array to table
echo "<table border=5>";
foreach ($activity as $action){
    echo "<tr>";
    $key = key($activity);
    echo "<td>{$key}</td>";
    foreach ($action as $act){
        $content = null;
        foreach ($act as $a){
            $content .= $a."; ";
        }
        echo "<td>{$content}</td>";
    }
    echo "</tr>";
    next($activity);
}
echo "</table>";

?>