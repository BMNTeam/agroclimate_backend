
<?php
//Текстовый заголовок страницы
$title = 'Анализ динамики климатически обусловленного количества чистых паров (% к площади посева озимой пшеницы)';
$heading = 'Страница анализа динамики';
$sub_heading = 'Анализ динамики проводится за период 1961г. по настоящее время';

//Вложение вспомогательных файлов
include_once ('./include/climate_auth.php');
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
?>

<!--заголовок страницы-->

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
                            <h4><?echo $title;?></h4>
                        </div>
                        <div class="container-elements clearfix">

                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Форма для отправки исходных данных -->
                                    <form action="clear_steam.php" method="POST">
                                        <div class="col-md-6">

                                            <div class="get-period--form">
                                                <div class="form-group">
                                                    <!--список метеостанций-->
                                                    <label for="basic_station">Выберите метеостанцию :</label>

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
                                                </div>

                                                <div class="form-group">
                                                    <!--выбор периода-->
                                                    <label for="basic_period">Период по которому будут рассчитаны скользящие значения: </label>
                                                    <input required type="text" placeholder="например: 10. Будет построен график 10 летних скользящих значений."
                                                           class="form-control" id="basic_period" name="year_slide" value="<? echo $year_slide;?>">
                                                </div>

                                                <div class="form-group">
                                                    <!--прогнозируемое значение-->
                                                    <label for="temperature_forecast">Прогнозируемое значение % климатически обусловленного количества чистых паров: </label>
                                                    <input required type="text" placeholder="например: 60"
                                                           class="form-control" id="temperature_forecast" name="prediction_value" value="<? echo $prediction_value;?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" class=" btn btn-default btn-lg show-graph--button" value="Показать">
                                            </div>

                                        </div>
                                    </form>
                                    <div class="content">
                                        <div class="col-md-12" style="position: relative;">
                                            <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период с 1961г. по настоящее время</p>
                                            <?php
                                            //обработка логики системы

                                            //обработка ошибок: не указаны входные параметры
                                            if(isset($year_slide) && $year_slide < 1)
                                            {
                                                echo "<strong>Ошибка! Неверно указан период или не выбраны анализируемые месяцы.</strong>\n";
                                                echo "<br>\n";
                                            }

                                            if(isset($meteo_id))
                                            {
                                                $Link = ConnectDB();

                                                if(isset($year_slide) && $year_slide != "")
                                                {
                                                    if($meteo_id < 17)
                                                    {
                                                        $sql_TP = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" ORDER BY YEAR ASC";
                                                    }
                                                    else if($meteo_id == 17)
                                                    {
                                                        $sql_TP = "select * from ClimateData_TP ORDER BY YEAR ASC";
                                                    }


                                                    $query_result_tp = mysqli_query($Link, $sql_TP);

                                                    $RowsCount = mysqli_num_rows($query_result_tp);

                                                    //Результирующая часть
                                                    $SlidingValuesMaxCount = $RowsCount - $year_slide + 1;
                                                    $SlidingValuesCurCount = 0;


                                                    $CountProcessedRows = 0;
                                                    $CurYear = 1960;

                                                    $CountYear = 0;
                                                    while($row_tp=mysqli_fetch_array($query_result_tp))
                                                    {
                                                        if($RowsCount > ($CountYear + 1))
                                                        {
                                                            $PrevP[$CountYear] = $row_tp["P8"] + $row_tp["P9"] + $row_tp["P10"] + $row_tp["P11"] + $row_tp["P12"];
                                                            $CountYear++;
                                                        }
                                                        else
                                                        {
                                                            break;
                                                        }
                                                    }

                                                    $query_result_tp=mysqli_query($Link, $sql_TP);
                                                    $CountYear = 0;
                                                    $IsFirstYear = true;
                                                    while($row_tp=mysqli_fetch_array($query_result_tp))
                                                    {

                                                        if($IsFirstYear == true)
                                                        {
                                                            $IsFirstYear = false;
                                                            continue;
                                                        }

                                                        $CountProcessedRows++;

                                                        if($row_tp["P1"] != "" && $row_tp["P2"] != "" && $row_tp["P3"] != "" && $row_tp["P4"] != "" && $row_tp["P5"] != "" && $PrevP[$CountYear] != "")
                                                        {
                                                            $PrevP[$CountYear] += $row_tp["P1"];
                                                            $PrevP[$CountYear] += $row_tp["P2"];
                                                            $PrevP[$CountYear] += $row_tp["P3"];
                                                            $PrevP[$CountYear] += $row_tp["P4"];
                                                            $PrevP[$CountYear] += $row_tp["P5"];

                                                            if($PrevP[$CountYear] > 430)
                                                            {
                                                                $PrevP[$CountYear] = 430;
                                                            }
                                                            elseif ($PrevP[$CountYear] < 250)
                                                            {
                                                                $PrevP[$CountYear] = 250;
                                                            }

                                                            $SvisukValue = (0.0022*($PrevP[$CountYear]*$PrevP[$CountYear]))-(2.0601*$PrevP[$CountYear])+476.2;

                                                            if($SvisukValue > 100)
                                                            {
                                                                $SvisukValue = 100;
                                                            }
                                                            elseif($SvisukValue < 0)
                                                            {
                                                                $SvisukValue = 0;
                                                            }

                                                            $SvisukValue = round($SvisukValue, 0);

                                                            $CountYear++;


                                                        }
                                                    $PerYearValue[$CurYear + $CountProcessedRows] = $SvisukValue;

                                                    }

                                                    krsort($PerYearValue);

                                                    $i = 0;
                                                    foreach($PerYearValue as $key => $val)
                                                    {
                                                        $MyPreSlideValArray[$i] = $val;
                                                        $MyPreSlideKeyArray[$i] = $key;
                                                        $i++;
                                                    }

                                                    $BeginYearSlide = 0;
                                                    for($i = 0; $i < $CountProcessedRows - $year_slide + 1; $i++)
                                                    {
                                                        $CurSlideValue = 0;
                                                        for($j = 0; $j < $year_slide; $j++)
                                                        {
                                                            $CurSlideValue += $MyPreSlideValArray[$BeginYearSlide + $j];
                                                        }
                                                        $CurSlideValue /= $year_slide;
                                                        $CurSlideValue = round($CurSlideValue, 2);


                                                        if($year_slide > 1)
                                                        {
                                                            $CurKeyValue = $MyPreSlideKeyArray[$BeginYearSlide + $year_slide - 1]."-".$MyPreSlideKeyArray[$BeginYearSlide];
                                                        }
                                                        else
                                                        {
                                                            $CurKeyValue = $MyPreSlideKeyArray[$BeginYearSlide];
                                                        }

                                                        $MySlideArray[$MyPreSlideKeyArray[$BeginYearSlide]] = $CurSlideValue;
                                                        $MyKeyArray[$MyPreSlideKeyArray[$BeginYearSlide]] = $CurKeyValue;
                                                        $BeginYearSlide++;
                                                    }

                                                    ksort($MySlideArray);
                                                    ksort($MyKeyArray);

                                                    foreach($MySlideArray as $key => $val)
                                                    {
                                                        $ResultArray[$MyKeyArray[$key]] = round($val, 1);
                                                    }

                                                    $CHT_Slide = "Динамика климатически обусловленного количества чистых паров. Метеостанция: ".$Meteostations[$meteo_id];
                                                    $CHT_Slide = win_utf8($CHT_Slide);
                                                }
                                            }

                                            if(isset($year_slide))
                                            {

                                                $Mean = Mean($ResultArray);
                                                $STD_Dev = STD_Deviation($ResultArray);

                                                ?>

                                                <!--график для отображения данных-->
                                                <center><div id="chart_div_mean"></div></center>

                                                <!--статистические данные-->
                                                <h3>Статистика</h3>
                                                <br>
                                                <span class="red_italic_text">Среднее арифметическое: <strong><?echo $Mean;?></strong></span>
                                                <br>
                                                <span class="red_italic_text">Ошибка среднего: <strong><?echo round($STD_Dev/(sqrt(GetCount($ResultArray))), 2);?></strong></span>
                                                <br>
                                                <span class="red_italic_text">Стандартное отклонение: <strong><?echo $STD_Dev;?></strong></span>
                                                <br>
                                                <span class="red_italic_text">Дисперсия: <strong><?echo STD_Deviation($ResultArray, true);?></strong></span>
                                                <br>
                                                <span class="red_italic_text">Коэффициент вариации: <strong><?echo round(($STD_Dev/$Mean) * 100, 1) ."%";?></strong></span>
                                                <br>
                                                <span class="red_italic_text">Коэффициент корреляции (год-осадки): <strong><?echo Correlation($ResultArray);?></strong></span>

                                                <br>
                                                <br>
                                                <span class="red_italic_text">Уравнение регрессии: <strong><?echo Regression($ResultArray);?></strong></span>
                                                <br>
                                                <span class="red_italic_text">х - номер года; 1961 год - х = 1; 2012 год - х = 52.</span>
                                                <br>
                                                <?php
                                            }

                                            if(isset($prediction_value) && is_numeric($prediction_value))
                                            {
                                                $Prob = CountProb($Mean, $STD_Dev, $prediction_value);
                                                if ($Prob == -9999)
                                                {
                                                    $Prob = 0;
                                                }
                                                ?>

                                                <br>
                                                <span class="red_italic_text">Вероятность значения <strong><? echo $prediction_value; ?></strong> или выше составляет: <strong><? echo $Prob . "% "; ?></strong></span>

                                                <br>
                                                <span class="red_italic_text">Вероятность значения ниже <strong><? echo $prediction_value; ?></strong> составляет: <strong><? echo 100 - $Prob . "% "; ?></strong></span>
                                                <br>
                                                <br>
                                                <?
                                            }
                                            ?>

                                            <p>
                                                Чистый пар является одним из лучших предшественником для озимой пшеницы в засушливых зонах. Разработанная в прошлом столетиии система "сухого земледелия" Ставропольского края в крайне засушливойи засушливой зонах опиралась на чистые пары как средство повышения и стабилизации производства зерна.
                                            </p>

                                            <p>
                                                В основу углубленной климатической оптимизации чистых паров по районам края положена методика И.В. Свисюка (1989г.). Который в своих работах показал высокую корреляционную связь (r=0,83) между количеством чистых паров в процентах от площади посева озимой пшеницы и величиной осадков за период с августа по май месяц. Эта зависимость определяет уравнением регрессии :
                                            </p>


                                            <span class="deep_green_italic_text"><center><strong>Y = 0,0022*(X*X) - 2,0601*X + 476,2</strong></center></span>
                                            <br>

                                            <span class="deep_green_italic_text">

                                            где,
                                                X - количество осадков за период май-август;<br>
                                                Y - необходимая площадь чистых паров, % к посевной площади озимой пшеницы.
                                            </span>
                                            <br>
                                            <br>

                                            <span class="header1"><center>Использованная литература</center></span>
                                            <p><i>
                                                    1. Система земледелия нового поколения Ставропольского края: монография /В.В. Кулинцев, Е.И. Годунова, Л.И. Желнакова и др. - Ставрополь: АГРУС Ставропольского гос. аграрного ун-та, 2013. - 520с.<br>
                                                    2. Свисюк И.В. Погода, интенсивная технология и урожай озимой пшеницы: монография /И.В. Свисюк - Ленинград: Гидрометеоиздат, 1989. - 226с. <br>
                                            </i></p>
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


//функция построения линейного графика
if(isset($meteo_id))
{
    if(isset($year_slide))
    {
        ?>
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Период');
                data.addColumn('number', '% чистых паров от площади посева озимой пшеницы');
                <?
                $DataSet = "data.addRows([";
                $i = 0;
                foreach($ResultArray as $key=>$value)
                {

                    $i++;
                    if($value != -100)
                    {

                        $DataSet .= "['";
                        $DataSet .= $key;
                        $DataSet .= "', ";
                        $DataSet .= $value;
                        $DataSet .= "]";
                    }

                    if($i < ($CountProcessedRows - $year_slide + 1))
                    {
                        $DataSet .= ",";
                    }
                    else
                    {
                        $DataSet .= "]);";
                        break;
                    }
                }
                echo $DataSet;

                ?>

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div_mean'));
                chart.draw(data, {width: 1100, height: 600, title: 'Динамика климатически обусловленного количества чистых паров. Метеостанция: <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние)',
                    colors:['#1e1e1e'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    vAxis: {title: '% чистых паров от площади посева озимой пшеницы', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    pointSize: 6,
                    legend: "bottom",
                    curveType: "function"
                });
            }

        </script>
        <?
    }
}
?>
