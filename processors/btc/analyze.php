<?php
require_once dirname(__FILE__) . '/../helper/logger/logger.php';
require_once dirname(__FILE__) . '/../helper/constants.php';
require_once dirname(__FILE__) . '/btce-api.php';

$user_login = $argv[1];
$step = $argv[2];

$log = new KLogger (constants::LOG_PATH . $user_login . constants::LOG_TYPE, KLogger::DEBUG);
$log->logInfo('Анализировать.. Пользователь: ' . $user_login . ' Шаг: ' .$step);

?>