<?php
//Текстовый заголовок страницы
$title = 'Страница поиска годов-аналогов на основе анализа Евклидова расстояния';
$heading = 'Поиск годов аналогов по тепло- и влагообеспеченности';
$sub_heading = 'Анализ проводится за период 1961г. по настоящее время';

//Вложение вспомогательных файлов
include_once ('./include/header.php');
include_once ('./include/chart_func.php');
include_once ('./include/stats_func.php');
include_once ('./admin/actions/charts/app/helper-functions.php');

extract($_POST);

//массив метеостанций Ставропольского края
$Meteostations = array();
$Meteostations[1] = "Александровское";
$Meteostations[2] = "Арзгир";
$Meteostations[3] = "Благодарный";
$Meteostations[4] = "Буденновск";
$Meteostations[5] = "Георгиевск";
$Meteostations[6] = "Дивное";
$Meteostations[7] = "Зеленокумск";
$Meteostations[8] = "Изобильный";
$Meteostations[9] = "Кисловодск";
$Meteostations[10] = "Красногвардейское";
$Meteostations[11] = "Минеральные Воды";
$Meteostations[12] = "Невинномысск";
$Meteostations[13] = "Новоалександровск";
$Meteostations[14] = "Рощино";
$Meteostations[15] = "Светлоград";
$Meteostations[16] = "Ставрополь";
$Meteostations[17] = "Ставропольский край";

$MonthsTitles = getMonthsTitles(!!$_POST['agro_year']);

?>
<section class="warning-copyright">
    <div class="container">

    </div>
</section>
<section class="content">

    <div class="content-container ">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="block-container clearfix last-container">
                        <div class="container-heading content-header-color">
                            <h4><? echo $title;?></h4>
                        </div>
                        <div class="container-elements clearfix">

                            <div class="col-md-12">
                                <div class="row">
                                    <form action="analog_tp.php" method="POST">
                                        <div class="col-md-6">

                                            <div class="get-period--form">
                                                <div class="form-group">
                                                    <!--список метеостанций-->
                                                    <label for="basic_station">Выберите метеостанцию :</label>

                                                    <select class="form-control" name="meteo_id" id="basic_station">
                                                        <?
                                                        $i = 1;
                                                        for ($i=1; $i<18; $i++)
                                                        {
                                                            if(isset($meteo_id) && $meteo_id == $i)
                                                            {
                                                                echo "<option value=\"$i\" selected>$Meteostations[$i]</option>\r\n";
                                                            }
                                                            else
                                                            {
                                                                echo "<option value=\"$i\">$Meteostations[$i]</option>\r\n";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="basic_period">Год по которому будет проводится поиск аналогов: </label>
                                                    <input required type="text"
                                                           placeholder="например: 1999 В результате будут показаны годы-аналоги 1999г."
                                                           class="form-control" id="basic_period" name="meteo_period" value="<?echo $meteo_period;?>">
                                                </div> <!--period-->

                                                <div class="form-group">
                                                    <label for="forecast_value">Количество аналогов: </label>
                                                    <input required type="text" placeholder="например: 5 В результате будут показаны 5 годов-аналогов"
                                                           class="form-control" id="forecast_value"
                                                           name="analog_count" value="<?echo $analog_count;?>">
                                                </div> <!--period-->
                                            </div> <!-- period form-->


                                        </div>

                                        <div class="content">
                                            <div class="col-md-12" style="position: relative;">
                                                <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период 1961г. по сегодняшний день</p>
                                                <p>Выберите метеостанцию и год для поиска его аналога. Вы можете указать отдельные месяца года и климатические параметры (осадки и температура) по которым будет осуществлен поиск.</p>
                                                <p>Укажите количество аналогов которое будет показано на странице.</p>
                                                <br>
                                                <table height="100%" width="35%" cellpadding="5" style="margin: 0 30%" cellspacing="0" border="0">
                                                    <tr>
                                                        <td align="center" width="10%"><span class="red"><strong>Температура</strong></span></td>
                                                        <td align="center" ><span class="red"><strong>Месяц</strong></span></td>
                                                        <td align="center"><span class="blue"><strong>Осадки</strong></span></td>
                                                    </tr>

                                                    <?
                                                    $MonthsCheckedT = array("","","","","","","","","","","","");
                                                    $MonthsCheckedP = array("","","","","","","","","","","","");
                                                    $MonthsT = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                                    $MonthsP = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                                    if(!empty($MonthsTemp) || !empty($MonthsPrecip))
                                                    {

                                                        $NoTemperature = true;
                                                        $NoPrecipitation = true;

                                                        if(!empty($MonthsTemp))
                                                        {
                                                            foreach($MonthsTemp as $key=>$value)
                                                            {
                                                                $MonthsCheckedT[$value] = "checked";
                                                                $MonthsT[$value] = 1;
                                                            }
                                                            $NoTemperature = false;
                                                        }

                                                        if(!empty($MonthsPrecip))
                                                        {
                                                            $MonthsCheckedP = array("","","","","","","","","","","","");
                                                            $MonthsP = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                                            foreach($MonthsPrecip as $key=>$value)
                                                            {
                                                                $MonthsCheckedP[$value] = "checked";
                                                                $MonthsP[$value] = 1;
                                                            }
                                                            $NoPrecipitation = false;
                                                        }
                                                    }

                                                    for($i= 0; $i < 12; $i++)
                                                    {
                                                        echo "<tr>\n";
                                                        echo "<td align=\"center\"><input type=\"checkbox\" name=\"MonthsTemp[]\" value=\"$i\" $MonthsCheckedT[$i]  /></td>\n";
                                                        echo "<td align=\"center\"><span class=\"simple_text\">$MonthsTitles[$i]</span></></td>\n";
                                                        echo "<td align=\"center\"><input type=\"checkbox\" name=\"MonthsPrecip[]\" value=\"$i\" $MonthsCheckedP[$i]  /></td>\n";
                                                        echo "</tr>\n";
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td colspan="2" align="center">Сельскохозяйственный год (июль-июнь)</td>
                                                        <?
                                                        if(isset($agro_year) && $agro_year == 1)
                                                        {
                                                            echo "<td align=\"center\"><input type=\"checkbox\" class='toggle-agrarian-year' name=\"agro_year\" value=\"1\" checked></td>";
                                                        }
                                                        else
                                                        {
                                                            echo "<td align=\"center\"><input type=\"checkbox\"  class='toggle-agrarian-year' name=\"agro_year\" value=\"1\"></td>";
                                                        }?>

                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" align="center"><input class="select-al-month--button  btn btn-default btn-sm" type="button" value="выделить все"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            <input type="submit" class=" btn btn-success btn-lg show-graph--button"
                                                                   value="Показать">
                                                        </td>
                                                    </tr>
                                                    </tbody>

                                                </table>
                                            </div>

                                    </form> <!--GET GRAPH form-->

                                    <div class="content">
                                        <div class="col-md-12" style="position: relative;">

                                            <?php

                                            $Link = ConnectDB();

                                            $AgroYear = 0;
                                            if(isset($agro_year) && $agro_year == 1)
                                            {
                                                $AgroYear = 1;
                                            }

                                            //определение года последнего хранящегося в базе данных
                                            if($AgroYear == 1)
                                            {
                                                $sql = "select * from ClimateDataAgro_TP ORDER BY Year DESC";
                                            }
                                            else
                                            {
                                                $sql = "select * from ClimateData_TP ORDER BY Year DESC";
                                            }


                                            $LastYear_QR = mysqli_query($Link, $sql);
                                            //echo $sql;

                                            if($LastYear_QR->num_rows > 0)
                                            {
                                                while ($row = $LastYear_QR->fetch_assoc())
                                                {
                                                    $LastYearDB = $row["Year"];
                                                    break;
                                                }
                                            }


                                            if(isset($meteo_id) && isset($meteo_period))
                                            {
                                            //Анализируем переменную периода
                                            if (strlen($meteo_period) == 4)
                                            {
                                            if ((int)$meteo_period > 1960 && (int)$meteo_period < ($LastYearDB + 1))
                                            {
                                                if($AgroYear == 1)
                                                {
                                                    if ($meteo_id == 17) {
                                                        $sql = "select * from ClimateDataAgro_TP where Year = \"$meteo_period\" ORDER BY ID DESC";
                                                    } else {
                                                        $sql = "select * from ClimateDataAgro_TP where MeteostationID = \"$meteo_id\" AND Year = \"$meteo_period\" ORDER BY ID DESC";
                                                    }
                                                }
                                                else
                                                {
                                                    if ($meteo_id == 17) {
                                                        $sql = "select * from ClimateData_TP where Year = \"$meteo_period\" ORDER BY ID DESC";
                                                    } else {
                                                        $sql = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" AND Year = \"$meteo_period\" ORDER BY ID DESC";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                            ?>
                                            <p>Ошибка! Неправильно указан анализируемый период</p>
                                        </div>
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
<?php include_once('./include/footer.php');
exit();
}
}
else
{
    ?>
    <p>Ошибка! Неправильно указан анализируемый период</p>
    </div>
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
    <?php include_once('./include/footer.php');
    exit();
}

if(empty($MonthsT) && empty($MonthsP))
{?>
    <p>Ошибка! Не выбраны сравниваемые месяца!</p>
    </div>
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
    <?php include_once('./include/footer.php');
    exit();
}
}
else
{
    ?>
    <center><h4><b>Пример</b></h4></center>
    <center><img src="img/pics/walter.jpg" width="900"></center>

    <p>
        Cтепень близости параметров определяется на основе расчета Евклидова расстояния и ранжировано по возрастанию.
        Совпадение температуры: (число), осадков: (число). Число указывает насколько показанная диаграмма близка к исходной по климатическому параметру, при значении 1 - это лучшее совпадение из всего ряда и т.д. по убыванию.
        Если осуществляется поиск аналога по температуре и осадкам, то будет учитываться их совокупное значение.
    </p>
    <p>
    <center><strong>Евклидово расстояние = корень(сумм((x-y)*(x-y)))</strong></center><br>
    где,<br>
    x - значение температуры или осадков базового периода;<br>
    y - значение температуры или осадков анализируемого периода.<br>
    </p>
    </div>
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
    <?php include_once('./include/footer.php');
    exit();
}

$ValuesT = array();
$ValuesP = array();

//температура
$query_result = mysqli_query($Link, $sql);
$RowsCount = $query_result->num_rows;

while($row = $query_result->fetch_assoc())
{
    for($i = 0; $i < 12; $i++)
    {
        $Index = "T";
        $tmp_index = $i;
        $Index .= strval($tmp_index + 1);
        $CurValue = $row[$Index];
        $CurValue = round($CurValue, 1);
        $ValuesT[$tmp_index] += $CurValue;
    }
}
//если выбран Ставропольский край
if($meteo_id == 17)
{

    for($i = 0; $i < 12; $i++)
    {
        $ValuesT[$i] = $ValuesT[$i]/16;
    }
}

//осадки
$query_result = mysqli_query($Link, $sql);

while($row = $query_result->fetch_assoc())
{
    for($i = 0; $i < 12; $i++)
    {
        $Index = "P";
        $tmp_index = $i;
        $Index .= strval($tmp_index + 1);
        $CurValue = $row[$Index];
        $CurValue = round($CurValue, 1);
        $ValuesP[$tmp_index] += $CurValue;
    }
}

//если выбран Ставропольский край
if($meteo_id == 17)
{
    for($i = 0; $i < 12; $i++)
    {
        $ValuesP[$i] = $ValuesP[$i]/16;
    }
}

//создание диаграммы
require_once('./admin/actions/charts/index.php');
CreateWalterChart($Link, $meteo_id, $meteo_period, 0, "", $AgroYear);

//выборка данных всех метеостанций
if($AgroYear == 1)
{
    if ($meteo_id == 17) {
        $sql = "select * from ClimateDataAgro_TP ORDER BY ID ASC";
    } else {
        $sql = "select * from ClimateDataAgro_TP where MeteostationID = \"$meteo_id\" ORDER BY ID ASC";
    }
}
else
{
    if ($meteo_id == 17) {
        $sql = "select * from ClimateData_TP ORDER BY ID ASC";
    } else {
        $sql = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" ORDER BY ID ASC";
    }
}

$query_result = mysqli_query($Link, $sql);
$RowsCount = $query_result->num_rows;

$EvklidValuesT = array();
$EvklidValuesP = array();


if($meteo_id == 17)
{
    //подготовка массивов
    $SearchYearT = array();
    $SearchYearP = array();
    for($i = 1961; $i <= $LastYearDB; $i++)
    {
        if($i == $meteo_period)
        {
            continue;
        }
        for($j = 0; $j < 12; $j++)
        {
            if ($MonthsT[$j] == 1)
            {
                $SearchYearT[$i][$j+1] = 0;
            }
            if($MonthsP[$j] == 1)
            {
                $SearchYearP[$i][$j+1] = 0;
            }
        }
    }

    while($row = $query_result->fetch_assoc())
    {
        //пропускаем выбранный год
        if(strlen($meteo_period) == 4)
        {
            if($row['Year'] == $meteo_period)
            {
                continue;
            }
        }

        for($i = 0; $i < 12; $i++)
        {
            if($MonthsT[$i] == 0)
                continue;

            $Index = "T";
            $Index .= strval($i + 1);
            $CurValue = $row[$Index];
            $CurValue = round($CurValue, 1);
            $SearchYearT[$row['Year']][$Index] += $CurValue;
        }
    }

    for($i = 1961; $i <= $LastYearDB; $i++)
    {
        if($i == $meteo_period)
        {
            continue;
        }
        for($j = 0; $j < 12; $j++)
        {
            if ($MonthsT[$j] == 1)
            {
                $SearchYearT[$i][$j+1] = $SearchYearT[$i][$j+1] / 16;
                $EvklidValuesT[$i] += ($ValuesT[$i] - $SearchYearT[$i][$j+1]) * ($ValuesT[$i] - $SearchYearT[$i][$j+1]);
            }

        }
    }
    for($i = 1961; $i <= $LastYearDB; $i++)
    {
        if($NoTemperature == true)
            $EvklidValuesT[$i] = null;
        else
            $EvklidValuesT[$i] = sqrt($EvklidValuesT[$i]);
    }

    $query_result = mysqli_query($Link, $sql);
    //Осадки
    while($row = $query_result->fetch_assoc())
    {
        //пропускаем выбранный год
        if(strlen($meteo_period) == 4)
        {
            if($row['Year'] == $meteo_period)
            {
                continue;
            }
        }

        for($i = 0; $i < 12; $i++)
        {
            if($MonthsP[$i] == 0)
                continue;

            $Index = "P";
            $Index .= strval($i + 1);
            $CurValue = $row[$Index];
            $CurValue = round($CurValue, 1);
            $SearchYearP[$row['Year']][$i+1] += $CurValue;
        }
    }

    for($i = 1961; $i <= $LastYearDB; $i++)
    {
        if($i == $meteo_period)
        {
            continue;
        }
        for($j = 0; $j < 12; $j++)
        {
            if ($MonthsP[$j] == 1)
            {
                $SearchYearP[$i][$j+1] = $SearchYearP[$i][$j+1] / 16;
                $EvklidValuesP[$i] += ($ValuesP[$i] - $SearchYearP[$i][$j+1]) * ($ValuesP[$i] - $SearchYearP[$i][$j+1]);
            }

        }
    }

    for($i = 1961; $i <= $LastYearDB; $i++)
    {
        if($i == $meteo_period)
        {
            continue;
        }

        if($NoPrecipitation == true)
            $EvklidValuesP[$i] = null;
        else
            $EvklidValuesP[$i] = sqrt($EvklidValuesP[$i]);
    }

}
else
{
    //температура
    while($row = $query_result->fetch_assoc())
    {
        //пропускаем выбранный год
        if(strlen($meteo_period) == 4)
        {
            if($row['Year'] == $meteo_period)
            {
                continue;
            }
        }

        for($i = 0; $i < 12; $i++)
        {
            if($MonthsT[$i] == 0)
                continue;

            $Index = "T";
            $Index .= strval($i + 1);
            $CurValue = $row[$Index];
            $CurValue = round($CurValue, 1);
            $EvklidValuesT[$row['Year']] += ($ValuesT[$i] - $CurValue) * ($ValuesT[$i] - $CurValue);
        }
        if($NoTemperature == true)
            $EvklidValuesT[$row['Year']] = null;
        else
            $EvklidValuesT[$row['Year']] = sqrt($EvklidValuesT[$row['Year']]);
    }

    $query_result = mysqli_query($Link, $sql);
    //осадки
    while($row = $query_result->fetch_assoc())
    {
        if(strlen($meteo_period) == 4)
        {
            if($row['Year'] == $meteo_period)
            {
                continue;
            }
        }

        for($i = 0; $i < 12; $i++)
        {
            if($MonthsP[$i] == 0)
                continue;

            $Index = "P";
            $Index .= strval($i + 1);
            $CurValue = $row[$Index];
            $CurValue = round($CurValue, 1);
            $EvklidValuesP[$row['Year']] += ($ValuesP[$i] - $CurValue) * ($ValuesP[$i] - $CurValue);
        }

        if($NoPrecipitation == true)
            $EvklidValuesP[$row['Year']] = null;
        else
            $EvklidValuesP[$row['Year']] = sqrt($EvklidValuesP[$row['Year']]);
    }
}


asort($EvklidValuesT);
asort($EvklidValuesP);


$RangeT = array();
$RangeP = array();
$i = 1;

foreach($EvklidValuesT as $key=>$value)
{
    if($NoTemperature == true)
        $i = 0;

    $RangeT[$key] = $i;
    $i++;
}

$i = 1;

foreach($EvklidValuesP as $key=>$value)
{
    if($NoPrecipitation == true)
        $i = 0;

    $RangeP[$key] = $i;
    $i++;
}

ksort($RangeT);
ksort($RangeP);

$SumRanges = array();

for($i = 1960; $i < $LastYearDB; $i++)
{
    $SumRanges[$i] = $RangeT[$i] + $RangeP[$i];
}

asort($SumRanges);

if(isset($analog_count))
{
    if($analog_count == "")
    {
        $analog_count = 5;
    }
    else if($analog_count > ($LastYearDB - 1961 - 1))
    {
        $analog_count = $LastYearDB - 1961 - 1;
    }

}
else
{
    $analog_count = 5;
}

$i = 0;
$SubTitle = "";


foreach($SumRanges as $key=>$value)
{
    if($i == 0)
    {
        $i++;
        continue;
    }

    if($i >= $analog_count + 1)
        break;

    $SubTitle = "\\n Совпадение ТЕМПЕРАТУРЫ. Место в рейтинге: ";

    if($NoTemperature == false)
        $SubTitle .= $RangeT[$key] - 1;
    else
        $SubTitle .= "НЕТ";

    $SubTitle .= "\\nСовпадение ОСАДКОВ. Место в рейтинге: ";

    if($NoPrecipitation == false)
        $SubTitle .= $RangeP[$key] - 1;
    else
        $SubTitle .= "НЕТ";

    //создание диаграммы
    CreateWalterChart($Link, $meteo_id, $key, $i, $SubTitle, $AgroYear);
    echo "<br>\n";
    echo "<br>\n";


    $i++;
}?>

<center><h4><b>Пример</b></h4></center>
<center><img src="img/pics/walter.jpg" width="900"></center>

<p>
    Cтепень близости параметров определяется на основе расчета Евклидова расстояния и ранжировано по возрастанию.
    Совпадение температуры: (число), осадков: (число). Число указывает насколько показанная диаграмма близка к исходной по климатическому параметру, при значении 1 - это лучшее совпадение из всего ряда и т.д. по убыванию.
    Если осуществляется поиск аналога по температуре и осадкам, то будет учитываться их совокупное значение.
</p>
<p>
<center><strong>Евклидово расстояние = корень(сумм((x-y)*(x-y)))</strong></center><br>
где,<br>
x - значение температуры или осадков базового периода;<br>
y - значение температуры или осадков анализируемого периода.<br>
</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

</div> <!--end similar years-->
</section>
<?php include_once( './include/footer.php' );

function CreateWalterChart($Link, $MeteoID, $MeteoPeriod, $chart_num, $sub_title, $a_AgroYear)
{
    global $Meteostations;

    $tmp_ValuesT = array();
    $tmp_ValuesP = array();

    if($a_AgroYear == 1)
    {
        if ($MeteoID == 17) {
            $sql = "select * from ClimateDataAgro_TP where Year = \"$MeteoPeriod\" ORDER BY ID DESC";
        } else {
            $sql = "select * from ClimateDataAgro_TP where MeteostationID = \"$MeteoID\" AND Year = \"$MeteoPeriod\" ORDER BY ID DESC";
        }
    }
    else
    {
        if ($MeteoID == 17) {
            $sql = "select * from ClimateData_TP where Year = \"$MeteoPeriod\" ORDER BY ID DESC";
        } else {
            $sql = "select * from ClimateData_TP where MeteostationID = \"$MeteoID\" AND Year = \"$MeteoPeriod\" ORDER BY ID DESC";
        }
    }

    $query_result = mysqli_query($Link, $sql);

    $CountValuesT = array();
    $CountValuesP = array();

    $ValueT = array();
    $ValueP = array();
    //Температура
    $CurValue = 0;
    for ($i = 0; $i < 12; $i++) {
        $CountValuesT[$i] = 0;
        $CountValuesP[$i] = 0;
    }

    while ($row = $query_result->fetch_assoc()) {
        for ($i = 0; $i < 12; $i++) {
            $Index = "T";
            $Index .= strval($i + 1);
            if ($row[$Index] != "") {
                $CountValuesT[$i]++;
                $ValueT[$i] += $row[$Index];
            }
        }
    }

    for ($i = 0; $i < 12; $i++) {
        if($CountValuesT[$i] == 0)
        {
            $ValueT[$i] = -100;
        }
        else
        {
            $ValueT[$i] /= $CountValuesT[$i];
        }

        $ValueT[$i] = round($ValueT[$i], 1);
    }

    //Осадки
    $query_result = mysqli_query($Link, $sql);

    //$RowsCountT = mysqli_num_rows($query_result);
    $ChartValuesP = array();
    $CurValue = 0;

    while ($row = $query_result->fetch_assoc()) {
        for ($i = 0; $i < 12; $i++) {
            $Index = "P";
            $Index .= strval($i + 1);
            if ($row[$Index] != "") {
                $CountValuesP[$i]++;
                $ValueP[$i] += $row[$Index];
            }
        }
    }

    for ($i = 0; $i < 12; $i++) {
        if($CountValuesP[$i] == 0)
        {
            $ValueP[$i] = -1;
        }
        else
        {
            $ValueP[$i] /= $CountValuesP[$i];
        }

        $ValueP[$i] = round($ValueP[$i], 1);
    }

    $Legend1 = array(
        'region_name' => $Meteostations[$MeteoID],
        'years' => $MeteoPeriod
    );


    $ValueTTr = array();
    $ValuePTr = array();
    for($i = 0; $i < 12; $i++) {
        if ($ValueT[$i] > -50) {
            $ValueTTr[$i] = $ValueT[$i];
        }
        if($ValueP[$i] > -1){
            $ValuePTr[$i] = $ValueP[$i];
        }
    }

    require_once('./admin/actions/charts/index.php');
    createLineChartWithDoubleY($Legend1, $ValueTTr, $ValuePTr, $chart_num, !!$a_AgroYear);
}

?>
