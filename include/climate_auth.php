<?
include_once ('./include/chart_func.php');

if (isset($_POST['auth_name'])) {

    $DB_Link = ConnectDB();

    $login=mysqli_real_escape_string($DB_Link, $_POST['auth_name']);
    $tmp_pass=mysqli_real_escape_string($DB_Link, $_POST['auth_pass']);


    $tmp_pass .= $login;
    $pass = md5($tmp_pass);

    $query = "SELECT * FROM ias_users WHERE login='$login' AND pass='$pass' AND active = '1'";
    $res = mysqli_query($DB_Link, $query) or trigger_error(mysqli_error().$query);
    if ($row = mysqli_fetch_assoc($res)) {
        session_start();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['work_title'] = $row['work_title'];
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?".session_name().'='.session_id());
    }
    else
    {
        session_start();
        $_SESSION['user_id'] = -1;
        header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."");
    }

    exit;
}


if (isset($_GET['action']) AND $_GET['action']=="logout") {
    echo "session end";
    session_start();
    session_destroy();
    header("Location: http://climate.sniish.ru/");
    exit;
}

//if (isset($_REQUEST[session_name()]))

session_start();

//echo "Request : \n";
//var_dump($_REQUEST);

//echo "\n";
//echo "Cookie: \n";
//var_dump($_COOKIE);

if (isset($_SESSION['user_id']) AND ($_SESSION['ip'] == $_SERVER['REMOTE_ADDR'])) return;
else
{
include_once('head.php'); ?>
<body>
<header>

    <div class="clearfix fixed-menu">
        <div class="container">
            <div class="col-md-12">

                <div class="header-top">

                    <div class="header-left fl hidden-sm hidden-xs">

                        

                        <figure class="clearfix fl">
                            <a href="http://sniish.ru" class="not-link-style">
                                <div class="logo fl">
                                    <img src="img/sniish-logo.png" class="logo-image fl" alt="Логотип СНИИСХ">
                                    <div class="sniish_text fl">
                                        <p>Ставропольский</p>
                                        <p>НИИСХ</p>
                                    </div>
                                </div>
                            </a>
                        </figure>



                    </div> <!--header left-->

                    <div class="header-right fr">
                        <menu class="main-menu">
                            <li><a href="./index.php">Меню</a></li>
                            <li><a href="./meteostations.php" target="_blank">Метеостанции края</a></li>
                            <li><a href="#">Помощь</a></li>
                            <!--<li><a href="./include/climate_auth.php?action=logout">Выйти</a></li>-->
                        </menu>
                    </div><!--header right-->

                </div> <!--header top-->
            </div> <!--column-->
        </div> <!--container-->
    </div>
    <br><br>
    <div class="header-page-name">
        <div class="overlay-image-layer">

            <div class="container">
                <div class="col-md-10 col-sm-7">
                    <div class="page-name--heading">
                        <p class="page-name--main-message"><?php echo($heading); ?></p>
                        <p class="page-name--description"><?php echo ($sub_heading); ?></p>
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
        <div class="content-container full-screen-height">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="block-container clearfix last-container">
                            <div class="container-heading content-header-color">
                                <h4>Введите имя пользователя и пароль</h4>
                            </div>
                            <div class="container-elements authorization-container clearfix">

                                <div class="col-md-6 ">
                                    <br><br>
                                    <form method="post" name="registration_form">
                                        <?
                                        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == -1)
                                        {
                                            session_destroy();

                                            echo "<span class=\"red_italic_text\"><strong>Ошибка! Неверное имя и логин!</strong></span>\n";
                                            echo "<br>\n";
                                        }
                                        ?>
                                        <div class="input-group input-group-lg">

                                            <span class="input-group-addon" id="basic-addon1"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" name="auth_name" placeholder="Имя пользователя" aria-describedby="basic-addon1">
                                        </div>
                                        <br>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-addon" id="basic-addon2"><i class="fa fa-lock"></i></span>
                                            <input type="password" class="form-control" name="auth_pass" placeholder="Пароль" aria-describedby="basic-addon1">
                                        </div>

                                        <br>
                                        <div class="input-group input-group-lg full-width-group clearfix">
                                            <div class="row">
                                                <div class="col-xs-9">
                                                    <input type="submit" class="btn btn-default btn-lg" value="Войти">
                                                    <div class="additional-access--wrapper" >
                                                        <div class="additional-access" id="additionalAccessButton">
                                                            дополнительно
                                                        </div>
                                                        <div class="hidden" id="additionalAccessLinks" >
                                                            <a href="admin/actions/index.php">Как администратор</a> /
                                                            <a href="manager/index.php">Как менеджер</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3">
                                                    <a href="registration.php" class="fr registration-link">Зарегистрироваться</a>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <div class="col-md-6  ">
                                        <div class="clearfix">

                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </section>
    <footer>
    <?php include_once( './include/footer.php' );
}
exit;
?>


