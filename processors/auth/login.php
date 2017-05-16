<?php
header('Content-type: text/html; charset=utf-8');
// Страница авторизации



# Функция для генерации случайной строки

function generateCode($length=6) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";

    $code = "";

    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {

        $code .= $chars[mt_rand(0,$clen)];
    }

    return $code;

}



# Соединямся с БД

mysql_connect("localhost", "crypto", "p*Kcq679");

mysql_select_db("crypto");

mysql_query('SET NAMES utf8');

if(isset($_POST['submit']))

{

    # Вытаскиваем из БД запись, у которой логин равняеться введенному

    $query = mysql_query("SELECT user_id, user_password FROM users WHERE user_login='".mysql_real_escape_string($_POST['login'])."' LIMIT 1");

    $data = mysql_fetch_assoc($query);



    # Соавниваем пароли

    if($data['user_password'] === md5(md5($_POST['password'])))

    {

        # Генерируем случайное число и шифруем его

        $hash = md5(generateCode(10));



        if(!@$_POST['not_attach_ip'])

        {

            # Если пользователя выбрал привязку к IP

            # Переводим IP в строку


            $insip = ", user_ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";

        }



        # Записываем в БД новый хеш авторизации и IP

        mysql_query("UPDATE users SET user_hash='".$hash."' ".$insip." WHERE user_id='".$data['user_id']."'");



        # Ставим куки

        setcookie("id", $data['user_id'], time()+60*60*24*30);

        setcookie("hash", $hash, time()+60*60*24*30);



        # Переадресовываем браузер на страницу проверки нашего скрипта

        header("Location: check.php"); exit();

    }

    else

    {
        print '<div style="z-index: 99999; padding: 30px; position: absolute"><div style="z-index: 99999;" class="alert alert-danger alert-dismissible " role="alert"><a onclick="closeAlert(this);" class="close" data-dismiss="alert" aria-label="close">&times;</a>Вы ввели неправильный логин/пароль!</div></div>';

    }

}

?>


<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/images/favicon.ico">

    <title>Авторизация BTC-E</title>
    
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/signin.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<article>
    <h1><span><a style="border-bottom-style: dotted; border-bottom-width: 1px;" href="https://webdivision.pro/about/">Web Studio «WebDivision»</a></span></h1>
    <form class="form-signin" method="POST">
        <h2 class="form-signin-heading">Авторизация</h2>
        <div class="form-group">
            <input name="login" type="text" class="form-control" placeholder="Логин" required>
        </div>
        <div class="form-group">
            <input name="password" type="password" class="form-control" placeholder="Ключ" required>
        </div>
        <div class="form-group">
            <span style="color: white;">Не прикреплять к IP (небезопасно) </span><input type="checkbox" name="not_attach_ip">
        </div>
        <div class="form-group">
            <input name="submit" class="btn btn-lg btn-primary btn-block" type="submit" value="Войти">
        </div>
        <div style="text-align: center" class="form-group">
            <a style="color: white; border-bottom-style: dotted; border-bottom-width: 1px;" href="register.php">Регистрация</a>
        </div>
    </form>
</article>

<video autoplay loop id="video-background" muted>
    <source src="/images/video/transit_renders.mp4" type="video/mp4">
</video>


<!-- JQUERY JS -->
<script src="/js/jquery-3.2.1.min.js"></script>
<!-- BOOTSTRAP JS -->
<script src="/js/bootstrap.min.js"></script>
<!-- Custom JS -->
<script src="/js/custom.js"></script>

</body>
</html>
