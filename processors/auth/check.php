<?php

require_once '../logger.php';
require_once '../constants.php';
require_once '../connection.php';

$id = intval($_COOKIE['id']);
$hash = $_COOKIE['hash'];

if (@$_POST["exit"]) {
    $user = getUser($mysqli, $id);
    logout($user);
}

if (isset($id) and isset($hash)) {

    $user = getUser($mysqli, intval($id));
    $user_id = intval($user['user_id']);
    $user_ip = $user['INET_NTOA(user_ip)'];
    $remote_address = $_SERVER['REMOTE_ADDR'];
    $user_hash = $user['user_hash'];

    if (($user_hash !== $hash) or ($user_id !== $id) or (($user_ip !== $remote_address) and ($user_ip !== "0"))) {
        redirectToLogin();
    } else {
        $log = new KLogger (constants::LOG_PATH . $user['user_login'] . constants::LOG_TYPE, KLogger::DEBUG);
        $log->LogInfo(constants::SIGN_IN);
        ?>
        <!DOCTYPE html>
        <html>
    <head lang="en">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Главная - Crypto</title>

        <link rel="shortcut icon" href="/images/favicon.ico">
        <!-- BOOTSTRAP CSS -->
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <!-- CUSTOM CSS -->
        <link rel="stylesheet" href="/css/style.css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    <div class="main">
        <div style="padding-top: 30px" class="container">
            <div class="row">
                <div class="col-sm-2">
                    <img width="37" src="/images/encryption.png"/>
                    <span style="font-size: 29px; vertical-align: top;">Crypto</span>
                </div>
                <div class="col-sm-5">
                </div>
                <div class="col-sm-1">
                    <span class="btn btn-link" style="color: green;">USD: 50</span>
                </div>
                <div class="col-sm-1">
                    <span class="btn btn-link" style="color: red;">BTC: 50</span>
                </div>
                <div class="col-sm-1">
                    <a class="btn btn-link" href=""><?php echo $user['user_login'] ?></a>
                </div>
                <div class="col-sm-1">
                    <form action="check.php" method="post">
                        <button style="text-align: left;" type="submit" name="exit" value="Выйти" class="btn btn-link">
                            Выход
                        </button>
                    </form>
                </div>
            </div>
            <div style="padding-top: 50px" class="row">
                <div style="text-align: left;" class="col-xs-5 col-sm-3 col-md-2 lh">
                    <a href="">BTC/USD (400)</a>
                    <a href="">LTC/USD (400)</a>
                    <a href="">NVC/USD (400)</a>
                    <a href="">PPC/USD (400)</a>
                    <a href="">ETH/USD (400)</a>
                    <a href="">DSH/USD (400)</a>
                    <a href="">NMC/USD (400)</a>
                </div>
                <div class="col-sm-3 col-md-2 col-xs-7">
                    <span style="font-size: 23px;">BTC/USD (400)</span>

                    <form style="margin-top: 10px;">
                        <div class="form-group">
                            <input class="form-control" id="deposit" placeholder="Депозит" name="deposit">
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="step" placeholder="Шаг" name="step">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-info">Анализировать</button>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Торговать</button>
                        </div>
                    </form>
                </div>
            </div>
            <div style="padding-top: 15px" class="row">
                <div style="text-align: left;" class="col-sm-3 col-md-2 lh">
                    <a href="">LTC/BTC (400)</a>
                    <a href="">NVC/BTC (400)</a>
                    <a href="">PPC/BTC (400)</a>
                    <a href="">EHT/BTC (400)</a>
                    <a href="">DSH/BTC (400)</a>
                    <a href="">NMC/BTC (400)</a>
                </div>
                <div class="col-sm-10">
                    <span style="font-size: 17px;">Консоль</span>

                    <p>
                        <samp>
                            20.06.17 20:41 Добро пожаловать!<br/>
                            20.06.17 22:42 Запуск бота..<br/>
                            20.06.17 00:41 Анализируем графики...<br/>
                        </samp>
                    </p>
                </div>
            </div>
            <div style="padding-top: 40px" class="row">
                <div style="text-align: center" class="col-sm-12">
                    <span><a style="border-bottom-style: dotted; border-bottom-width: 1px;"
                             href="https://webdivision.pro/about/">Web Studio «WebDivision»</a></span>
                </div>
            </div>
        </div>
    </div>
    <!-- JQUERY JS -->
    <script src="/js/jquery-3.2.1.min.js"></script>
    <!-- BOOTSTRAP JS -->
    <script src="/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="/js/custom.js"></script>
    </body>
    <?php
    }

} else {

    redirectToLogin();

}

function getUser($mysqli, $id)
{
    $query = mysqli_query($mysqli, "SELECT *,INET_NTOA(user_ip) FROM users WHERE user_id = '" . $id . "' LIMIT 1");
    return mysqli_fetch_assoc($query);
}

function logout($user)
{
    $log = new KLogger (constants::LOG_PATH . $user['user_login'] . constants::LOG_TYPE, KLogger::DEBUG);
    $log->LogInfo(constants::LOGOUT);
    setcookie("WebEngineerRestrictedArea", "", time() - 60 * 60 * 24);
    setcookie("id", "", time() - 60 * 60 * 24);
    setcookie("hash", "", time() - 60 * 60 * 24);
    header("Location: /index.php");
    exit();
}

function redirectToLogin()
{
    setcookie("id", "", time() - 3600 * 24 * 30 * 12, "/");
    setcookie("hash", "", time() - 3600 * 24 * 30 * 12, "/");
    header("Location: /index.php");
    exit();
}

?>