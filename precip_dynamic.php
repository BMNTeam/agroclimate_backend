<?php
//Текстовый заголовок страницы
$title = 'Анализ динамики осадков';
$heading = 'Страница анализа динамики осадков';
$sub_heading = 'Анализ динамики проводится за период 1961-2016гг.';

//Вложение вспомогательных файлов
include_once ('./include/climate_auth.php');
include_once ('./include/header.php');
include_once ('./include/chart_func.php');
include_once ('./include/stats_func.php');

extract($_POST);

//Массив месяцев
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
                                    <form action="precip_dynamic.php" method="POST">
                                        <div class="col-md-12">

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
                                                    <!--параметры для анализа динамики-->
                                                    <label for="basic_station">Выберите месяц :</label>
                                                    <table class="table--dynamic" height="100%" width="100%" cellpadding="5" cellspacing="0" border="0">
                                                        <tbody><tr>

                                                            <!--обработка логики системы-->

                                                            <?php
                                                            if(empty($MonthsDM))
                                                            {
                                                                $MonthsChecked = array("","","","","","","","","","","","");
                                                                $AnalyzeMonths = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                                            }
                                                            else
                                                            {
                                                                $MonthsChecked = array("","","","","","","","","","","","");
                                                                $AnalyzeMonths = array(0,0,0,0,0,0,0,0,0,0,0,0);

                                                                if(!empty($MonthsDM))
                                                                {
                                                                    foreach($MonthsDM as $key=>$value)
                                                                    {
                                                                        $MonthsChecked[$value] = "checked";
                                                                        $AnalyzeMonths[$value] = 1;
                                                                    }
                                                                }
                                                            }

                                                            //выбор месяца

                                                            for($i=0; $i<12; $i++)
                                                            {
                                                                echo "<td align=\"center\"><p>$MonthsTitles[$i]</p></td>\r\n";
                                                            }
                                                            ?>
                                                            <td align="center"><p>Выделить/Снять выделение</td>
                                                        </tr><tr>
                                                            <?php
                                                            for($i=0; $i<12; $i++)
                                                            {
                                                                echo "<td align=\"center\"><input type=\"checkbox\" name=\"MonthsDM[]\" value=\"$i\" $MonthsChecked[$i]></td>\r\n";
                                                            }
                                                            ?>
                                                            <td align="center"><input class="select-al-month--button  btn btn-default btn-sm" class="" type="button" value="выделить все"></td>
                                                        </tr></tbody></table>
                                                </div>

                                                <div class="form-group">
                                                    <!--выбор периода-->
                                                    <label for="basic_period">Период по которому будут рассчитаны скользящие значения: </label>
                                                    <input required type="text" placeholder="например: 10. В результате будет построен график 10 летних скользящих значений."
                                                           class="form-control" id="basic_period" name="year_slide" value="<? echo $year_slide;?>">
                                                </div>

                                                <div class="form-group">
                                                    <!--прогнозируемое значение-->
                                                    <label for="temperature_forecast">Прогнозируемое значение количества осадков: </label>
                                                    <input required type="text" placeholder="например: 500"
                                                           class="form-control" id="temperature_forecast" name="prediction_value" value="<? echo $prediction_value;?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" class=" btn btn-default btn-lg show-graph--button" value="Показать">
                                            </div>

                                        </div>


                                    </form> <!--GET GRAPH form-->
                                    <hr>
                                    <div class="content">
                                        <div class="col-md-12">
                                            <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период с 1961г. по 2016г.</p>

                                            <?php
                                            //обработка логики системы
                                            //определение количества месяцев для обработки
                                            $Count_Months = 0;
                                            for($i = 0; $i < 12; $i++)
                                            {
                                                if($AnalyzeMonths[$i] == 1)
                                                {
                                                    $Count_Months++;
                                                }
                                            }

                                            //обработка ошибок: не указаны входные параметры
                                            if((isset($year_slide) && $Count_Months == 0) || (isset($year_slide) && $year_slide < 1))
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
                                                        $sql_TP = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" and Year > \"1960\" ORDER BY YEAR ASC";
                                                    }
                                                    else if($meteo_id == 17)
                                                    {
                                                        $sql_TP = "select * from ClimateData_TP ORDER BY YEAR ASC";
                                                    }

                                                    $query_result_tp = mysqli_query($Link, $sql_TP);

                                                    //количество строк в запросе
                                                    $RowsCount = mysqli_num_rows($query_result_tp);


                                                    $CountProcessedRows = 0;
                                                    $CurYear = 1960;


                                                    while($row_tp = mysqli_fetch_array($query_result_tp))
                                                    {
                                                        $SummP = 0;
                                                        for($k = 0; $k < 12; $k++)
                                                        {
                                                            if($AnalyzeMonths[$k] == 1)
                                                            {
                                                                $Index = "P";
                                                                $Index .= strval($k + 1);
                                                                $CurValue = $row_tp[$Index];

                                                                if($CurValue == '')
                                                                {
                                                                    $SummP = -100;
                                                                    break;
                                                                }

                                                                $CurValue = round($CurValue, 1);
                                                                $SummP += $CurValue;
                                                            }
                                                        }

                                                        if($SummP != -100)
                                                        {
                                                            $CountProcessedRows++;
                                                            $PerYearValue[$CurYear + $CountProcessedRows] = $SummP; //массив ежегодных данных по температуре
                                                        }
                                                    }

                                                    krsort($PerYearValue);//сортировка массива

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
                                                        $CurSlideValue = round($CurSlideValue, 1);

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

                                                    ksort($MySlideArray);//сортировка массива
                                                    ksort($MyKeyArray);//сортировка массива

                                                    foreach($MySlideArray as $key => $val)
                                                    {
                                                        $ResultArray[$MyKeyArray[$key]] = $val;
                                                    }

                                                    $CHT_Slide = "Динамика осадков. Метеостанция: ".$meteo_title[$Selected_Meteo_ID - 1];
                                                    $CHT_Slide = win_utf8($CHT_Slide);
                                                }
                                            }

                                            if(isset($year_slide) && $Count_Months > 0)
                                            {

                                                $Mean = Mean($ResultArray);
                                                $STD_Dev = STD_Deviation($ResultArray);

                                            ?>

                                            <!--график для отображения данных о количестве осадков-->
                                            <div id="chart_div_main"></div>

                                            <!--статистические данные-->
                                            <h3>Статистика</h3>
                                            <br>
                                            <span class="red_italic_text">Среднее арифметическое: <strong><?echo Mean($ResultArray);?></strong></span>
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
                                                <?
                                            }
                                            ?>

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

<!--нижняя часть страницы-->
<?php include_once ('./include/footer.php');

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
                data.addColumn('number', 'Осадки');
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

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div_main'));
                chart.draw(data, {width: 1200, height: 700, title: 'Динамика осадков. Метеостанция: <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние значения)',
                    colors:['#0f1ed9'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 16}},
                    vAxis: {title: 'Количество осадков, мм.', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 16}},
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

