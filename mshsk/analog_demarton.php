<?php
//Текстовый заголовок страницы
$title = 'Страница поиска годов-аналогов на основе анализа Евклидова расстояния';
$heading = 'Поиск годов аналогов по индексу аридности ДеМартонна';
$sub_heading = 'Анализ проводится за период 1961г. по настоящее время';

//Вложение вспомогательных файлов
//include_once ('./include/climate_auth.php');
include_once ('./include/header.php');
include_once ('./include/chart_func.php');
include_once ('./include/stats_func.php');

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

$MothsTitles = array();
$MonthsTitles[0] = "Январь";
$MonthsTitles[1] = "Февраль";
$MonthsTitles[2] = "Март";
$MonthsTitles[3] = "Апрель";
$MonthsTitles[4] = "Май";
$MonthsTitles[5] = "Июнь";
$MonthsTitles[6] = "Июль";
$MonthsTitles[7] = "Август";
$MonthsTitles[8] = "Сентябрь";
$MonthsTitles[9] = "Октябрь";
$MonthsTitles[10] = "Ноябрь";
$MonthsTitles[11] = "Декабрь";

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
                                    <form action="analog_demarton.php" method="POST">
                                        <div class="col-md-6">

                                            <div class="get-period--form">
                                                <div class="form-group">
                                                    <label for="basic_station">Выберите метеостанцию :</label>
                                                    <!--<input type="select" class="form-control" id="usr">-->
                                                    <select class="form-control" name="meteo_id" id="basic_station">
                                                        <?
                                                        $i = 1;
                                                        for ($i=1; $i<17; $i++)
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
                                                </div> <!--Metastatic-->

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
                                            <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период 1961-2009гг.</p>
                                            <p>Выберите метеостанцию и год для поиска его аналога на основании расчета индекса аридности Де Мартонна. Вы можете указать отдельные месяцы года по которым будет осуществлен поиск <strong>аналогов.</strong></p>
                                            <p>Укажите количество аналогов которое будет показано на странице.</p>

                                            <table height="100%" width="35%" cellpadding="5" style="margin: 0 30%" cellspacing="0" border="0">
                                                <th>
                                                    <td align="center" colspan="2"><span class="header3">Месяц</span></td>
                                                </th>

                                                <?
                                                $MonthsChecked = array("","","","","","","","","","","","");
                                                $AnalyzeMonths = array();

                                                if(!empty($MonthsDM))
                                                {
                                                    foreach($MonthsDM as $key=>$value)
                                                    {
                                                        $MonthsChecked[$value] = "checked";
                                                        $AnalyzeMonths[$value] = 1;
                                                    }
                                                }

                                                for($i= 0; $i < 12; $i++)
                                                {
                                                    echo "<tr>\n";
                                                    echo "<td align=\"center\"><span class=\"simple_text\">$MonthsTitles[$i]</span></></td>\n";
                                                    echo "<td align=\"center\"><input type=\"checkbox\" name=\"MonthsDM[]\" value=\"$i\" $MonthsChecked[$i]  /></td>\n";
                                                    echo "</tr>\n";
                                                }
                                                ?>

                                                <tr>
                                                    <td colspan="4" align="center"><input class="select-al-month--button  btn btn-default btn-sm" type="button" value="выделить все"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <input type="submit" class=" btn btn-success btn-lg show-graph--button"
                                                               value="Показать">
                                                    </td>
                                                </tr>
                                            </table>

                                        </div>

                                    </form> <!--GET GRAPH form-->

                                    <div class="content">
                                        <div class="col-md-12" style="position: relative;">

                                            <?php

                                            $Link = ConnectDB();

                                            //определение года последнего хранящегося в базе данных
                                            $sql = "select * from ClimateData_TP ORDER BY Year DESC";
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
                                                        if ($meteo_id == 17) {
                                                            $sql = "select * from ClimateData_TP where Year = \"$meteo_period\" ORDER BY ID DESC";
                                                        } else {
                                                            $sql = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" AND Year = \"$meteo_period\" ORDER BY ID DESC";
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

                                                if(empty($AnalyzeMonths))
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

                                            $SummPBase = 0;
                                            $MeanTBase = 0;
                                            $CountAnalyzeMonths = 0;
                                            $DM_Base = 0;

                                            //Рассчитываем индекс аридности
                                            for($i = 0; $i < 12; $i++)
                                            {
                                                if($AnalyzeMonths[$i] == 0)
                                                    continue;

                                                $SummPBase += $ValuesP[$i];
                                                $MeanTBase += $ValuesT[$i];
                                                $CountAnalyzeMonths++;
                                            }

                                            $DM_Base = ($SummPBase / 10) / (($MeanTBase / $CountAnalyzeMonths)  + 10);

                                            $SubTitle .= "\\n Индекс аридности ДеМартонна: ";
                                            $SubTitle .= $DM_Base;

                                            //создание диаграммы
                                            require_once('./admin/charts/index.php');
                                            CreateWalterChart($Link, $meteo_id, $meteo_period, 0);

                                            //выборка данных всех метеостанций
                                            $sql = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" ORDER BY YEAR ASC";
                                            $query_result = mysqli_query($Link, $sql);
                                            $RowsCount = $query_result->num_rows;

                                            $EvklidValues = array();

                                            //ТЕМПЕРАТУРА
                                            $AnalyzeMeanT = array();
                                            while($row = $query_result->fetch_assoc())
                                            {
                                                if(strlen($meteo_period) == 4)
                                                {
                                                    if($row[Year] == $meteo_period)
                                                    {
                                                        continue;
                                                    }
                                                }

                                                for($i = 0; $i < 12; $i++)
                                                {
                                                    if($AnalyzeMonths[$i] == 0)
                                                        continue;

                                                    $Index = "T";
                                                    $Index .= strval($i + 1);
                                                    $CurValue = $row[$Index];
                                                    $CurValue = round($CurValue, 1);
                                                    $AnalyzeValuesT[$row[Year]][$i] = $CurValue;
                                                    $AnalyzeMeanT[$row[Year]] += $AnalyzeValuesT[$row[Year]][$i];
                                                }
                                                $AnalyzeMeanT[$row[Year]] /= $CountAnalyzeMonths;
                                            }

                                            //ОСАДКИ
                                            $query_result = mysqli_query($Link, $sql);
                                            $AnalyzeSummP = array();
                                            $DM_Analyze = array();

                                            while($row = $query_result->fetch_assoc())
                                            {
                                                if(strlen($meteo_period) == 4)
                                                {
                                                    if($row[Year] == $meteo_period)
                                                    {
                                                        continue;
                                                    }
                                                }

                                                for($i = 0; $i < 12; $i++)
                                                {
                                                    if($AnalyzeMonths[$i] == 0)
                                                    {
                                                        $AnalyzeValuesP[$row[Year]][$i] = 0.0;
                                                        continue;
                                                    }

                                                    $Index = "P";
                                                    $Index .= strval($i + 1);
                                                    $CurValue = $row[$Index];
                                                    $CurValue = round($CurValue, 1);
                                                    $AnalyzeValuesP[$row[Year]][$i] = $CurValue;
                                                    $AnalyzeSummP[$row[Year]] += $AnalyzeValuesP[$row[Year]][$i];
                                                }
                                                $DM_Analyze[$row[Year]] += ($AnalyzeSummP[$row[Year]] / 10) / ($AnalyzeMeanT[$row[Year]] + 10);
                                            }



                                            foreach($DM_Analyze as $key=>$value)
                                            {
                                                $EvklidValues[$key] = ($DM_Analyze[$key] - $DM_Base) * ($DM_Analyze[$key] - $DM_Base);
                                                $EvklidValues[$key] = sqrt($EvklidValues[$key]);
                                            }

                                            asort($EvklidValues);



                                            $Range = array();
                                            $i = 1;

                                            foreach($EvklidValues as $key=>$value)
                                            {
                                                $Range[$key] = $i;
                                                $i++;
                                            }

                                            //построение диаграмм годов-аналогов
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


                                            foreach($EvklidValues as $key=>$value)
                                            {

                                                if($i >= $analog_count)
                                                    break;

                                                $SubTitle = "\\n Место в рейтинге: ";
                                                $SubTitle .= $Range[$key];
                                                $SubTitle .= ".      Индекс аридности ДеМартонна: ";
                                                $SubTitle .= $DM_Analyze[$key];

                                                //создание диаграммы
                                                CreateWalterChart($Link, $meteo_id, $key, $i+1, $SubTitle);
                                                echo "<br>\n";
                                                echo "<br>\n";


                                                $i++;
                                            }
?>

                                            <center><h4><b>Пример</b></h4></center>
                                            <center><img src="img/pics/walter.jpg" width="900"></center>
                                            <center><h4><b>Методический подход</b></h4></center>
                                            <p>
                                                Поиск будет оусществлен по индексу аридности ДеМартонна:<br>
                                                <center><strong>Индекс аридности = сумм(P / 10) / (T + 10 градусов Цельсия)</strong></center><br>
                                                где,</><br>
                                                сумм(P) – сумма осадков за период, мм;</><br>
                                                T - средняя температура (градусов Цельсия) за период.</><br>
                                            </p>

                                            <p>
                                                Cтепень близости параметров определяется на основе расчета Евклидова расстояния и ранжировано по возрастанию.
                                                Совпадение температуры: (число), осадков: (число). Число указывает насколько показанная диаграмма близка к исходной по климатическому параметру, при значении 1 - это лучшее совпадение из всего ряда и т.д. по убыванию.
                                                Если осуществляется поиск аналога по температуре и осадкам, то будет учитываться их совокупное значение.
                                            </p>
                                            <p>
                                            <center><strong>Евклидово расстояние = корень(сумм((x-y)*(x-y)))</strong></center><br>
                                            где,</><br>
                                            x - значение температуры или осадков базового периода;</><br>
                                            y - значение температуры или осадков анализируемого периода.</><br>
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

    </div> <!--end similar years-->


</section>
<?php include_once( './include/footer.php' );


function CreateWalterChart($Link, $MeteoID, $MeteoPeriod, $chart_num, $sub_title)
{
    global $Meteostations;

    $tmp_ValuesT = array();
    $tmp_ValuesP = array();

    if ($MeteoPeriod == null) {
        $sql = "select * from ClimateData_TP where Year = \"$MeteoPeriod\" ORDER BY ID DESC";
    } else {
        $sql = "select * from ClimateData_TP where MeteostationID = \"$MeteoID\" AND Year = \"$MeteoPeriod\" ORDER BY ID DESC";
    }

    //температура
    $query_result = mysqli_query($Link, $sql);

    while($row = $query_result->fetch_assoc())
    {
        for($i = 0; $i < 12; $i++)
        {
            $Index = "T";
            $tmp_index = $i;
            $Index .= strval($tmp_index + 1);
            $CurValue = $row[$Index];

            if($CurValue == null){
                break;
            }
            $CurValue = round($CurValue, 1);
            $tmp_ValuesT[$tmp_index] = $CurValue;

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

            if($CurValue == null){
                break;
            }
            $CurValue = round($CurValue, 1);
            $tmp_ValuesP[$tmp_index] = $CurValue;
        }
    }

    $Legend1 = array(
        'region_name' => $Meteostations[$MeteoID],
        'years' => $MeteoPeriod,
        'desc' => $sub_title

    );

    $number_of_months = 0;
    if (count($tmp_ValuesT) == count($tmp_ValuesP)) {
        $number_of_months = 12;
    } elseif (count($tmp_ValuesT) > count($tmp_ValuesP)) {
        $number_of_months = count($tmp_ValuesT);
    } else {
        $number_of_months = count($tmp_ValuesP);
    }

    $ValueTTr = array();
    $ValuePTr = array();
    for($i = 0; $i < $number_of_months; $i++)
    {
        if ($tmp_ValuesT[$i] > -50) {
            $ValueTTr[$i] = $tmp_ValuesT[$i];
        }
        if($tmp_ValuesP[$i] > -1){
            $ValuePTr[$i] = $tmp_ValuesP[$i];
        }
    }
    createLineChartWithDoubleY($Legend1, $ValueTTr, $ValuePTr, $chart_num);
}
?>
