<?php
require_once '../constants.php';

$lastLogLine = $_POST['lastLogLine'];

$arr = file(constants::LOG_PATH . $_COOKIE['userLogin'] . constants::LOG_TYPE, FILE_IGNORE_NEW_LINES);
$log = '';
$isRead = false;
for ($i = 0; $i < count($arr); $i++) {
        if($isRead or empty($lastLogLine)){
            $log =  $log . '<span>' . $arr[$i] . '</span><br/>';
            continue;
        }
        if($arr[$i] == $lastLogLine){
           $isRead = true;
        }
}
echo responseMessage(true, $log);

function responseMessage($result, $message){
    $res = array();
    $res['success'] = $result;
    $res['message'] = $message;
    return json_encode($res);
}

?>