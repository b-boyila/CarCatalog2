<?php
require_once 'logger.php';
require_once 'constants.php';
header(constants::CONTENT_TYPE);
$systemLog = new KLogger (constants::SYSTEM_LOG_PATH, KLogger::DEBUG );
$mysqli = mysqli_connect(constants::LOCALHOST, constants::USER_DB, constants::PASSWORD, constants::DATABASE);
if (mysqli_connect_errno($mysqli)) {
    $systemLog->logError(constants::MYSQLi_CONNECT_ERROR . mysqli_connect_error());
}