<?php

class constants
{

    const CONTENT_TYPE = 'Content-type: text/html; charset=utf-8';

    //База данных
    const MYSQLi_CONNECT_ERROR = 'Не удалось подключиться к MySQL: ';


    const SYSTEM_LOG_PATH = '/var/www/vhosts/crypto-trader.ru/httpdocs/logs/system.txt';
    const LOG_PATH = '/var/www/vhosts/crypto-trader.ru/httpdocs/logs/';
    const LOG_TYPE = '.txt';
    const LOG_USER_SIZE = 100;
    const SIGN_IN = 'Вход в систему';
    const LOGOUT = 'Выход из системы';
    const LOGIN_OR_PASSWORD_NOT_VALID = 'Вы ввели неправильный логин/пароль!';
    const LOGIN_ONLY_LETTERS = 'Логин может состоять только из букв английского алфавита и цифр!';
    const LOGIN_ONLY_3_30 = 'Логин должен быть не меньше 3-х символов и не больше 30!';
    const LOGIN_DUPLICATE = 'Пользователь с таким логином уже существует в базе данных!';
    const EMPTY_VALUE = 'Пустые значения!';
    
    //Крон
    const TASK_FILE = '/var/www/vhosts/crypto-trader.ru/httpdocs/processors/helper/cron/cronTask.txt';
    const EVERY_MINUTE = '*/1 * * * *';
    const INTERPETER = '/usr/bin/php -f';
    const ADD_TASK = 'Задача добавлена в крон!';
    const DELETE_TASK = 'Задача удалена из крон!';

    //CSV
    const CSV_PATH = '/var/www/vhosts/crypto-trader.ru/httpdocs/processors/btc/ticker/history/';
    const CSV_TYPE = '.csv';
    const LOG_PAIR_SIZE = 100;

    //MACD
    const FOR_CALCULATE = 35;

}

?>