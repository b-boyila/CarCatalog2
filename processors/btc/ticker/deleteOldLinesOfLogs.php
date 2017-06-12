<?php
require_once dirname(__FILE__) . '/../../helper/logger/logger.php';
require_once dirname(__FILE__) . '/../../helper/constants.php';
header(constants::CONTENT_TYPE);

$path = constants::LOG_PATH;
$usersFiles = scandir($path);
delete($usersFiles, $path, constants::LOG_USER_SIZE);

$path = constants::CSV_PATH;
$pairsFiles = scandir($path);
delete($pairsFiles, $path, constants::LOG_PAIR_SIZE);

function delete($logsFiles, $path, $size) {
    for ($i = 0; $i < count($logsFiles); $i++) {
        $fileLog = $logsFiles[$i];
        $lines = file($path . $fileLog);
        $sliceLog = array_splice($lines, -$size, $size);
        file_put_contents($path . $fileLog, $sliceLog);
    }
}