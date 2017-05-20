<?php
require_once '../logger/logger.php';
require_once '../constants.php';
require_once '../db/connection.php';
$log = new KLogger (constants::LOG_PATH . $user['user_login'] . constants::LOG_TYPE, KLogger::DEBUG);
$step = $_POST['step'];
$action = $_POST['action'];
$user_login = $_COOKIE['userLogin'];

if(empty($step) or empty($action)){
    $log->logError(constants::EMPTY_VALUE);
    die(responseMessage(false ,constants::EMPTY_VALUE));
}

if(isActiveTask($user_login, $action)){
    deleteTask($user_login);
    $log->logInfo(constants::DELETE_TASK);
    echo(responseMessage(true, constants::DELETE_TASK));
} else {
    deleteTask($user_login);
    addTask($user_login, $action, $step);
    $log->logInfo(constants::ADD_TASK);
    echo(responseMessage(true, constants::ADD_TASK));
}

function addTask($user_login, $action, $step){
    $pathCronTaskFile  = dirname(__FILE__) . '/' . constants::TASK_FILE;
    $allTask = shell_exec('crontab -l');
    $addTask = constants::EVERY_MINUTE . ' ' . constants::INTERPETER .' ' .  dirname(__FILE__) . '/../../btc/' . $action . '.php ';
    $params = ' -- "' . $user_login .'" "' . $step . '"';
    file_put_contents($pathCronTaskFile, $allTask . $addTask . $params .PHP_EOL);
    exec('crontab ' . $pathCronTaskFile);
    #exec('crontab -r');
}

function deleteTask($user_login){
    $pathCronTaskFile  = dirname(__FILE__) . '/' . constants::TASK_FILE;
    $tasks = file($pathCronTaskFile);
    $stack = array();
    for ($i = 0; $i < count($tasks); $i++) {
        $isTask = strpos($tasks[$i], $user_login);
        if($isTask === false){
            array_push($stack, $tasks[$i]);
        }
    }
    file_put_contents($pathCronTaskFile, implode("",$stack), LOCK_EX);
    exec('crontab ' . $pathCronTaskFile);
}

function isActiveTask($user_login, $action){
    $pathCronTaskFile  = dirname(__FILE__) . '/' . constants::TASK_FILE;
    $tasks = file($pathCronTaskFile);
    for ($i = 0; $i < count($tasks); $i++) {
        $isTask = strpos($tasks[$i], $user_login);
        $isAction = strpos($tasks[$i], $action);
        if($isTask !== false and $isAction !== false){
            return true;
        }
    }
    return false;
}

function responseMessage($result, $message){
    $res = array();
    $res['success'] = $result;
    $res['message'] = $message;
    return json_encode($res);
}

?>