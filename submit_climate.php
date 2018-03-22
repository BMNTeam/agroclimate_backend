<?
include_once ('./include/chart_func.php');

//проверка контрольного слова теста Тьюринга
if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
{
//сокретный ключ для сайта
$secret = '6LcIUecSAAAAAIFoRXmzuQcFW3g87YzVLGBngG4V';
//получаем ответ
$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
$responseData = json_decode($verifyResponse);

}

if(isset($responseData) && $responseData->success)
{

//проверка имени пользователя
    if(isset($_REQUEST['login']) === true && strlen($_REQUEST['login']) > 3)
    {
        $login = $_REQUEST['login'];
    }
    else
    {//имя не введено
        header("Location: registration.php?result=invalid_login");
        exit();
    }
//проверка пароля

    if(isset($_REQUEST['pass']) === true && strlen($_REQUEST['pass']) > 3)
    {
        if(isset($_REQUEST['pass_again']) === true && strlen($_REQUEST['pass_again']) > 3)
        {
            if($_REQUEST['pass'] === $_REQUEST['pass_again'])
            {//пароли совпадают
                $tmp_pass = $_REQUEST['pass'];
                $tmp_pass .= $login;
                $pass = md5($tmp_pass);
            }
            else
            {//пароли не совпадают
                header("Location: registration.php?result=pass_not_match");
                exit();
            }
        }
        else
        {//неверный пароль
            header("Location: registration.php?result=invalid_pass");
            exit();
        }
    }
    else
    {//неверный пароль
        header("Location: registration.php?result=invalid_pass");
        exit();
    }

//проверка ФИО
    if(isset($_REQUEST['fio']) === true && strlen($_REQUEST['fio']) > 3 && IsSpam($_REQUEST['fio']) == false)
    {
        $fio = $_REQUEST['fio'];
    }
    else
    {
        header("Location: registration.php?result=invalid_fio");
        exit();
    }

//проверка организации
    if(isset($_REQUEST['work_title']) === true && strlen($_REQUEST['work_title']) > 3 && IsSpam($_REQUEST['work_title']) == false)
    {
        $work_title = $_REQUEST['work_title'];
    }
    else
    {
        header("Location: registration.php?result=invalid_organization");
        exit();
    }

//проверка адреса электронной почты
    $email = $_REQUEST['email'];

    if ($email ==="" || !filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        header("Location: registration.php?result=invalid_email");
        exit();
    }

//проверка цели использования
    if(isset($_REQUEST['purpose']) && $_REQUEST['purpose'] > 0 && $_REQUEST['purpose'] < 6)
    {
        $purpose = $_REQUEST['purpose'];
    }
    else
    {
        $purpose = -1;
    }

//проверка организации
    $work_site = "не указано";
    if($_REQUEST['work_site'] !== "")
    {
        $work_site = $_REQUEST['work_site'];
    }

//проверка контрольоного слова
    if($_REQUEST['my_captcha'] === "" || $_REQUEST['my_captcha'] != 8)
    {
        header("Location: registration.php?result=captcha_not_match");
        exit();
    }

//activity status
    $active = "1";

    $reg_time = time();

    $Link = ConnectDB();

    $sql= "select * from ias_users where email = \"$email\" or login = \"$login\"";
    $result = mysqli_query($Link, $sql) or trigger_error(mysqli_error($Link));


    if ($result->num_rows > 0)
    {//данное имя или электронная почта уже существует
        header("Location: registration.php?result=already_exist");
        exit();
    }

    $confirmation_code = 0;
    $sql="insert into ias_users (login, pass, fio, email, work_title, work_site, purpose, active, confirm_code, reg_time) values (\"$login\",\"$pass\",\"$fio\",\"$email\",\"$work_title\",\"$work_site\",\"$purpose\",\"$active\",\"$confirmation_code\", \"$reg_time\")";

    $result = mysqli_query($Link, $sql);

    header("Location: registration.php?result=ok");
}
else
{
    header("Location: registration.php?result=captcha_not_match");
}



function IsSpam($fp_strCommentText)
{

    if($fp_strCommentText === " ")
    {
        return 1;
    }//if($fp_strComment === " ")

    if(strlen($fp_strCommentText) > 1000)
    {
        return 1;
    }

    $tmp_strCommentFixed = strtolower($fp_strCommentText);

    if(strpos($tmp_strCommentFixed, "<h1>") !== false || strpos($tmp_strCommentFixed, "</h1>") !== false)
    {
        return 1;
    }

    if(strpos($tmp_strCommentFixed, "<h2>") !== false || strpos($tmp_strCommentFixed, "</h2>") !== false)
    {
        return 1;
    }


    if(strpos($tmp_strCommentFixed, "<h3>") !== false || strpos($tmp_strCommentFixed, "</h3>") !== false)
    {
        return 1;
    }

    if(strpos($tmp_strCommentFixed, "<a href") !== false || strpos($tmp_strCommentFixed, "</a>") !== false)
    {
        return 1;
    }

    if(strpos($tmp_strCommentFixed, "http://") !== false || strpos($tmp_strCommentFixed, "www.") !== false)
    {
        return 1;
    }

    if(strpos($tmp_strCommentFixed, "[url=") !== false || strpos($tmp_strCommentFixed, "[/url]") !== false)
    {
        return 1;
    }

    return 0;
}

?>
