<?php
header('Content-type: text/html; charset=utf-8');

mysql_connect("localhost", "crypto", "p*Kcq679");

mysql_select_db("crypto");

mysql_query('SET NAMES utf8');

if(isset($_POST['submit']))

{

    $err = array();


    # проверям логин

    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))

    {

        $err[] = "Логин может состоять только из букв английского алфавита и цифр!";

    }



    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)

    {

        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30!";

    }



    # проверяем, не сущестует ли пользователя с таким именем

    $query = mysql_query("SELECT COUNT(user_id) FROM users WHERE user_login='".mysql_real_escape_string($_POST['login'])."'");

    if(mysql_result($query, 0) > 0)

    {

        $err[] = "Пользователь с таким логином уже существует в базе данных";

    }



    # Если нет ошибок, то добавляем в БД нового пользователя

    if(count($err) == 0)

    {


        $login = trim($_POST['login']);



        # Убераем лишние пробелы и делаем двойное шифрование

        $password = md5(md5(trim($_POST['password'])));

        $secret = trim($_POST['secret']);

        $email = trim($_POST['email']);

        mysql_query("INSERT INTO users SET user_login='".$login."', user_password='".$password."', user_secret='".$secret."', user_email='".$email."'");

        header("Location: login.php"); exit();

    }

    else

    {
        print '<div style="z-index: 99999; padding: 30px; position: absolute">';
        foreach($err AS $error)

        {
            print '<div style="z-index: 99999;" class="alert alert-danger alert-dismissible " role="alert"><a onclick="closeAlert(this);" class="close" data-dismiss="alert" aria-label="close">&times;</a>' . $error . '</div>';
        }
        print ' </div>';
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
        <h2 class="form-signin-heading">Регистрация</h2>
        <div class="form-group">
            <input name="login" type="text" class="form-control" placeholder="Логин" required>
        </div>
        <div class="form-group">
            <input name="email" type="email" class="form-control" placeholder="E-mail" required>
        </div>
        <div class="form-group">
            <input name="password" type="password" class="form-control" placeholder="Ключ" required>
        </div>
        <div class="form-group">
            <input name="secret" type="password" class="form-control" placeholder="Секрет" required>
        </div>
        <div class="form-group">
            <input name="submit" class="btn btn-lg btn-primary btn-block" type="submit" value="Зарегистрироваться">
        </div>
        <div style="text-align: center" class="form-group">
            <a style="color: white; border-bottom-style: dotted; border-bottom-width: 1px;" href="login.php">Авторизация</a>
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