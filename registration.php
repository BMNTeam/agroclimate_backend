<!DOCTYPE html>
<html lang="ru">

<head>

    <meta charset="utf-8">

    <title> АИС Агроклимат | Регистрация </title>
    <meta name="description" content="">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta property="og:image" content="path/to/image.jpg">
    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/favicon/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicon/apple-touch-icon-114x114.png">

    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#000">

    <style>
        body { opacity: 0; overflow-x: hidden; }
        html { background-color: #fff; }
    </style>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>
<header>

    <div class="header-page-name">
        <div class="overlay-image-layer">

            <div class="container">
                <div class="col-md-10 col-sm-7">
                    <div class="page-name--heading">
                        <p class="page-name--main-message">Регистрация</p>
                        <p class="page-name--description">для регистрации в системе заполните форму</p>
                    </div>
                </div>
                <div class="col-md-2 col-sm-5 hidden-xs">
                    <div class="temperature ">
                        <i id="loadingAnimation" class="fa fa-spinner fa-pulse fa-3x fa-fw padding-top-fix"></i>
                        <div class="hidden-content hidden">
                            <p class="current-temperature" >
                                <span id="temperatureValue">32</span>
                                <span id="estimateIn">℃</span>
                            </p>
                            <p class="current-temperature--city">
                                Ставрополь
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</header>

<section class="content">
    <div class="content-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="block-container clearfix last-container">
                        <?php
                        if(isset($_GET['result']) && $_GET['result'] === "ok")
                        {?>
                            <div class="container-heading content-header-color">
                                <h4>Спасибо за регистрацию!</h4>
                            </div>
                        <?}
                        else
                        {?>
                        <div class="container-heading content-header-color">
                            <h4>Обязательные поля отмечены *</h4>
                        </div>
                        <?}?>
                        <form action="submit_climate.php" method="post" name="registration_form">
                            <div class="container-elements authorization-container clearfix">
                                <?
                                if(isset($_GET['result']))
                                {
                                    if($_GET['result'] === "invalid_login")
                                    {?>
                                        <p><center>Ошибка! Вы не указали логин или он слишком короткий (минимум 4 символа)</center></p>
                                        <?
                                    }
                                    else if($_GET['result'] === "invalid_pass")
                                    {?>
                                        <p><center>Ошибка! Вы не указали пароль или он слишком короткий (минимум 4 символа)</center></p>
                                        <?
                                    }
                                    else if($_GET['result'] === "pass_not_match")
                                    {?>
                                        <p><center>Ошибка! Указанные пароль не совпадают</center></p>
                                        <?
                                    }
                                    else if($_GET['result'] === "captcha_not_match")
                                    {?>
                                        <p><center>Ошибка! Неправильно указаны проверочные слова</center></p>
                                        <?
                                    }
                                    else if($_GET['result'] === "invalid_email")
                                    {?>
                                        <p><center>Ошибка! Вы ввели неверный адрес электронной почты</center></p>
                                            <?
                                    }
                                    else if($_GET['result'] === "already_exist")
                                    {?>
                                        <p><center>Ошибка! Введенный логин или email уже используется</center></p>
                                            <?
                                    }
                                    if($_GET['result'] === "ok")
                                    {?>
                                        <p><center>Спасибо за регистрацию!</center></p>
                                        <center><a href="index.php">Войти в систему</a></center>
                                        <?
                                    }
                                }
                                if(!isset($_GET['result']) || $_GET['result'] !== "ok")
                                {
                                ?>
                                <div class="col-md-6 ">
                                    <br>
                                    <h3>Информация о пользователе</h3>
                                    <br>
                                    <div class="form-group">
                                        <label for="">Логин <span class="important-field">*</span>:</label>
                                        <input required type="text" class="form-control" id=""
                                               name="login" placeholder="например: agronom" value="<?echo $_POST['login'];?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Пароль <span class="important-field">*</span>:</label>
                                        <input required
                                               placeholder="подсказка: не используйте простой пароль"
                                               class="form-control" id="" type="password" name="pass" value="<?echo $_POST['pass'];?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Повторите пароль <span class="important-field">*</span>:</label>
                                        <input required
                                               placeholder="подсказка: не используйте простой пароль"
                                               class="form-control" id="" type="password" name="pass_again" value="<?echo $_POST['pass_again'];?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="">ФИО <span class="important-field">*</span>:</label>
                                        <input required type="text" placeholder="например: Иванов Иван Иванович"
                                               class="form-control" id="" name="fio" value="<?echo $_POST['fio'];?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="">E-mail <span class="important-field">*</span>:</label>
                                        <input required type="text" placeholder="например: test@gmail.com"
                                               class="form-control" id="" name="email" value="<?echo $_POST['email'];?>">
                                    </div>


                                </div>
                                <div class="col-md-6  ">
                                    <br>
                                    <h3>Место работы/учебы</h3>
                                    <br>
                                    <div class="form-group">
                                        <label for="">Название организации <span
                                                class="important-field">*</span>:</label>
                                        <input required type="text" placeholder="например: СНИИСХ" class="form-control"
                                               id="" name="work_title" value="<?echo $_POST['work_title'];?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Сайт организации: </label>
                                        <input type="text" placeholder="например: sniish.ru" class="form-control"
                                               id="" name="work_site" value="<?echo $_POST['work_site'];?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Цель использования <span class="important-field">*</span>:</label>
                                        <!--<input type="select" class="form-control" id="">-->
                                        <select class="form-control" name="purpose" id="">
                                            <option value="1">Научная деятельность</option>
                                            <option value="2" selected>Учебная деятельность</option>
                                            <option value="3">Преподавательская работа</option>
                                            <option value="4">Планирование производства</option>
                                            <option value="5">Расширяю кругозор</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="">Напечатайте цифрами ответ, сколько будет три плюс 5 <span
                                                class="important-field">*</span>:</label>
                                        <input type="number" class="form-control" id="" name="my_captcha" value="<?echo $_POST['my_captcha'];?>">
                                    </div>
                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="6LcIUecSAAAAALvtutL3NjjMQ0UVzoNjQgizuOCH"></div>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <br>
                                        <input type="submit" class=" btn btn-default btn-lg" value="Зарегистрироваться">
                                        <span class="pl20"> Нажимая на кнопку зарегистрироваться вы принимаете условия <a
                                                data-toggle="modal" data-target=".bs-example-modal-lg">лицензионного соглашения</a></span>
                                        <br>
                                        <br>
                                    </div>


                                </div>
                                <?}?>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<footer>
    <div class="top-footer--container">
        <div class="container">
            <br>

            <div class="row">

                <div class="col-md-4">
                    <p class="footer-info--heading">
                        Адрес:
                    </p>
                    <p class="footer-info--description">
                        356241, Ставропольский край, Шпаковский район,
                        г. Михайловск, ул. Никонова, 49
                    </p>
                </div>

                <div class="col-md-4">
                    <p class="footer-info--heading">
                        Телефон:
                    </p>
                    <p class="footer-info--description">
                        <a href="tel:88652611773"> 8-8652-611-773</a> <br>
                        <a href="tel:88655323298"> 8-865-53-2-32-98</a>
                    </p>
                </div>

                <div class="col-md-4">
                    <p class="footer-info--heading">
                        Факс:
                    </p>
                    <p class="footer-info--description">
                        <a href="tel:8655323297"> 8-655-32-32-97</a>

                    </p>
                </div>
            </div>

        </div>

    </div> <!--end top deader footer container -->

    <div class="bottom-footer--container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p class="footer-info--heading">
                        Copyright 2017 © Лаборатория ГИС-технологий СНИИСХ
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-climate">
            <h3 class="text-center">Лицензионное соглашение для использования ИАС «АГРО-КЛИМАТ»</h3>
            <div class="agreement-item">
                <br>
                <br>
                <p>
                    <b>1. Предмет соглашения </b> <br>
                    Данное лицензионное соглашение (далее «Соглашение») является официальным законным соглашением между Вами (далее "Пользователь") и ФГБНУ Ставропольский НИИСХ ФАНО России (далее "Разработчик"). Соглашение регламентирует права, обязанности и ответственность сторон, а также перечень видов лицензирования, их условия и порядок получения лицензий на право использования Информационно-аналитической системы «АГРО-КЛИМАТ» не для коммерческого использования (далее «ИАС»). Любые права, временно передаваемые Соглашением Пользователю, являются неэкслюзивными и никаким образом не могут противоречить или нарушать законные исключительные имущественные права ФГБНУ Ставропольский НИИСХ ФАНО России на ИАС и сопровождающие ее материалы. Соглашение вступает в силу с момента регистрации Пользователя в ИАС. То есть, факт регистрации Пользователя является доказательством согласия Пользователя с условиями Соглашения.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>2. Исключительные имущественные авторские права </b> <br>
                    ИАС является интеллектуальной собственностью ФГБНУ Ставропольский НИИСХ Россельхозакадемии, права которого защищены ГК РФ и международными соглашениями об авторских правах. Все права, не предоставленные явно настоящим соглашением, сохраняются за Разработчиком.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>3. Режим доступа к ИАС </b> <br>
                    Доступ к ресурсам ИАС осуществляется через официальный сайт Разработчика (http://sniish.ru). Разработчик оставляет за собой право без дополнительного уведомления Пользователя изменять URL – адрес ИАС, обновлять интерфейс и функциональность ИАС.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>4. Некоммерческая лицензия </b> <br>
                    Некоммерческая лицензия предоставляет Пользователю неисключительное право на бесплатное некоммерческое использование ИАС в учебных и научных целях. Любое коммерческое использование ИАС, т.е. предполагающее получение какой бы то ни было прямой или косвенной выгоды от использования ИАС, противоречит условиям данной лицензии и является нарушением условий Соглашения.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>5. Коммерческое использование</b> <br>
                    Для получения лицензии на коммерческого использования ИАС обращайтесь в ФГБНУ Ставропольский НИИСХ ФАНО России по адресу sniish@mail.ru. или любыми другими способами, указанными на официальном сайте Разработчика.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>6. Дополнительные ограничения использования ИАС </b> <br>
                    Пользователь не имеет право передавать данные учетной записи (имя пользователя и пароль) третьим лицам, а также использовать любую информацию, визуализируемую ИАС без соответствующего цитирования ИАС. В случае использования графиков и диаграмм, генерируемых ИАС, в учебных, научных, презентационных и иных публикациях обязательно цитирование источника (ИАС) в заголовках рисунков. Правила цитирования указаны на главной странице ИАС.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>7. Гарантии и ответственность сторон </b> <br>
                    ИАС предоставляется Пользователю по принципу "КАК ЕСТЬ" без каких либо утверждений или гарантий, явных или подразумеваемых относительно работоспособности и применимости ИАС для конечного использования. Пользователь использует ИАС на свой собственный риск, Разработчик не несет никакой ответственности перед Пользователем или любыми другими лицами (организациями) за любой причиненный ущерб, включая, но не ограничиваясь, любыми потерями данных, прямыми или косвенными убытками, упущенной выгодой, даже в случае, если такие случаи могли быть предвидены Разработчиком. В случае нарушения условий настоящего соглашения Разработчик имеет право в одностороннем порядке расторгнуть Соглашение, требовать полного и безоговорочного возмещения любых причиненных ему убытков, включая упущенную выгоду.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>8. Расторжение Соглашения </b> <br>
                    Разработчик может прекратить действие данного Соглашения без ущерба для каких-либо своих прав при использовании ИАС Пользователем с нарушением условий настоящего Соглашения. Пользователь может в любой момент прекратить действие данного Соглашения по своему усмотрению, при этом Пользователь обязуется прекратить любое использование ИАС, не допускать дальнейшее использование учетной записи (имя пользователя, пароль) третьими лицами.
                </p>
            </div>

            <div class="agreement-item">
                <br>
                <p>
                    <b>9. Изменение условий Соглашения </b> <br>
                    Разработчик оставляет за собой право одностороннего внесения любых изменений и дополнений в Соглашение, имеющих законную силу с даты их утверждения Разработчиком изменений и распространяющихся на последующие сеансы доступа к ИАС.
                </p>
            </div>


        </div>
    </div>
</div> <!--END modal-->

<link rel="stylesheet" href="css/libs.css">
<link rel="stylesheet" href="css/main.min.css">
<script src="js/scripts.min.js"></script>
</body>
</html>
