<?php
require_once dirname(__FILE__). '/../logger/logger.php';
require_once 'connect.php';
require_once dirname(__FILE__) .'/../constants.php';
header(constants::CONTENT_TYPE);
$systemLog = new KLogger (constants::SYSTEM_LOG_PATH, KLogger::DEBUG );
$mysqli = mysqli_connect(connect::LOCALHOST, connect::USER_DB, connect::PASSWORD, connect::DATABASE);
if (mysqli_connect_errno()) {
    $systemLog->logError(constants::MYSQLi_CONNECT_ERROR . mysqli_connect_error());
}