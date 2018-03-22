<?php
//Текстовый заголовок страницы
$title = 'Анализ динамики биоклиматического потенциала (показатель Д.И. Шашко)';
$heading = 'Страница анализа динамики';
$sub_heading = 'Анализ динамики проводится за период 1961г. по настоящее время';

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
                                    <form action="bcp.php" method="POST">
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
                                                    <label for="temperature_forecast">Прогнозируемое значение показателя Д.И. Шашко: </label>
                                                    <input required type="text" placeholder="например: 2.8"
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
                                                        $sql_TP = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" AND Year > \"1985\" ORDER BY YEAR ASC";
                                                        $sql_GTK = "select * from ClimateData_GTK where MeteostationID = \"$meteo_id\" AND Year > \"1985\" ORDER BY YEAR ASC";
                                                        $sql_MD = "select * from ClimateData_MoistureDeficit where MeteostationID = \"$meteo_id\" AND Year > \"1985\" ORDER BY YEAR ASC";
                                                    }
                                                    //else if($meteo_id == 17)
                                                    //{

                                                    //    $sql_TP = "select * from ClimateData_TP ORDER BY YEAR ASC";
                                                    //    $sql_GTK = "select * from ClimateData_GTK ORDER BY YEAR ASC";
                                                    //    $sql_MD = "select * from ClimateData_MoistureDeficit ORDER BY YEAR ASC";
                                                    //}

                                                    $query_result_tp  = mysqli_query($Link, $sql_TP);
                                                    $query_result_gtk = mysqli_query($Link, $sql_GTK);
                                                    $query_result_md  = mysqli_query($Link, $sql_MD);

                                                    $RowsCount = $query_result_tp->num_rows;

                                                    //Результирующая часть
                                                    $SlidingValuesMaxCount = $RowsCount - $year_slide + 1;
                                                    $SlidingValuesCurCount = 0;

                                                    $CountProcessedRows = 0;
                                                    $CurYear = 1986;

                                                    $CountYear = 0;

                                                    while($row_tp=$query_result_tp->fetch_assoc())
                                                    {
                                                        if($row_tp["P1"] != "" && $row_tp["P2"] != "" && $row_tp["P3"] != "" && $row_tp["P4"] != "" && $row_tp["P5"] != "" && $row_tp["P6"] != "" && $row_tp["P7"] != "" && $row_tp["P8"] != "" && $row_tp["P9"] != "" && $row_tp["P10"] != "" && $row_tp["P11"] != "" && $row_tp["P12"] != "")
                                                        {
                                                            $YearP[$CurYear + $CountYear] = $row_tp["P1"] + $row_tp["P2"] + $row_tp["P3"] + $row_tp["P4"] + $row_tp["P5"] + $row_tp["P6"] + $row_tp["P7"] + $row_tp["P8"] + $row_tp["P9"] + $row_tp["P10"] + $row_tp["P11"] + $row_tp["P12"];
                                                            $CountYear++;
                                                            $CountProcessedRows++;
                                                        }
                                                    }

                                                    $CountYear = 0;
                                                    while($row_md=$query_result_md->fetch_assoc())
                                                    {
                                                        $YearMD[$CurYear + $CountYear] = $row_md["MD_4"] + $row_md["MD_5"] + $row_md["MD_6"] + $row_md["MD_7"] + $row_md["MD_8"] + $row_md["MD_9"] + $row_md["MD_10"];
                                                        $CountYear++;
                                                    }

                                                    //Коэффициент увлажнения
                                                    foreach($YearP as $key => $val)
                                                    {
                                                        $KY[$key] = $val / $YearMD[$key];
                                                    }

                                                    //коэффициент роста
                                                    foreach($KY as $key => $val)
                                                    {
                                                        $KR[$key] = log10(20 * $val);
                                                    }

                                                    $j = 0;
                                                    $CountYear = 0;

                                                    while($row_gtk=$query_result_gtk->fetch_assoc())
                                                    {
                                                        for($i = 0; $i < 7; $i++)
                                                        {
                                                            $Index = "T";
                                                            $Index .= strval($i + 4);
                                                            $CurValue = $row_gtk[$Index];
                                                            $CurValue = round($CurValue, 1);
                                                            $ValuesT[$CurYear + $CountYear][$i] = $CurValue;
                                                        }
                                                        $CountYear++;
                                                    }

                                                    //DAYS
                                                    $CountYear = 0;
                                                    $query_result_gtk=mysqli_query($Link, $sql_GTK);
                                                    while($row_gtk=$query_result_gtk->fetch_assoc())
                                                    {
                                                        for($i = 0; $i < 7; $i++)
                                                        {
                                                            $Index = "D";
                                                            $Index .= strval($i + 4);
                                                            $CurValue = $row_gtk[$Index];
                                                            $CurValue = round($CurValue, 1);
                                                            $ValuesD[$CurYear + $CountYear][$i] = $CurValue;
                                                        }
                                                        $CountYear++;
                                                    }

                                                    //сумма активных температур
                                                    foreach($ValuesT as $key => $val)
                                                    {
                                                        for($j = 0; $j < 7; $j++)
                                                        {
                                                            if($ValuesT[$key][$j] > 0.0 && $ValuesT[$key][$j] != "NULL")
                                                            {
                                                                $SummT[$key] += $ValuesT[$key][$j] * $ValuesD[$key][$j];
                                                            }
                                                        }
                                                    }

                                                    //биоклиматический потенциал
                                                    foreach($SummT as $key => $val)
                                                    {
                                                        $BCP[$key] = round($KR[$key] * ($val / 1000), 2);
                                                    }

                                                    foreach($BCP as $key => $val)
                                                    {
                                                        $PerYearValue[$key] = $val;

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
                                                        $ResultArray[$MyKeyArray[$key]] = $val;
                                                    }

                                                    $CHT_Slide = "Динамика биоклиматического потенциала (Д.И. Шашко). Метеостанция: ".$Meteostations[$meteo_id];
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
                                            Для рационального ведения сельскохозяйственного производства очень важны
                                            климатические параметры, которые наиболее тесно связаны с продуктивностью
                                            сельскохозяйственных культур и составляют сельскохозяйственный потенциал
                                            климата. В качестве сельскохозяйственного потенциала климата используется
                                            биоклиматический потенциал (БКП) по Д.И. Шашко (1967).
                                            Применение БКП позволяет дать сравнительную межрегиональную оценку земель,
                                            через относительные значения биоклиматического потенциала, синтезирующего
                                            влияние на биологическую продуктивность основных факторов климата – тепла и
                                            влаги. Очевидным плюсом использования БКП является возможность определить не
                                            только биологическую продуктивность, но и получить фактические значения
                                            климатически обусловленной продуктивности зерновых культур исходя из
                                            значения биоклиматического потенциала (Шашко, 1985). Биоклиматический
                                            потенциал рассчитывался по формуле:
                                            </p>
                                            <span class="violet_italic_text"><center><strong>БКП = Кр(КУ) * (сумма Так / сумма Тбаз)</strong></center></span>
                                            <br>
                                            <span class="violet_italic_text">где,
                                            <br>
                                            Кр(КУ) - коэффициент роста по годовому показателю атмосферного увлажнения;
                                            <br>
                                            сумма Так - сумма средних суточных температур воздуха за период активной вегетации;
                                            <br>
                                            сумма Тбаз - базисная сумма средних суточных температур воздуха за период активной вегетации;
                                            <br>
                                                <br>
                                                <span class="violet_italic_text"><center><strong>КУ = Ос / ДВ</strong></center></span>
                                                <br>
                                                <span class="violet_italic_text">где,
                                                <br>
                                                Ос - годовое количество осадков;
                                                <br>
                                                ДВ - сумма ежедневных дефицитов влажности воздуха.
                                                <br>
                                                    <br>
                                                    <span class="violet_italic_text"><center><strong>Кр = log10(КУ * 20)</strong></center></span>

                                                </span>

                                            <p>
                                            Градация биоклиматического потенциала и биологической продуктивности:
                                            </p>
                                            <span class="red_italic_text">БКП 1,2-1,6 <strong>пониженная продуктивность</strong> 14,0-19,5 ц/га;*</span>
                                            <br>
                                            <br>
                                            <span class="orange_italic_text">БКП 1,6-2,2 <strong>средняя продуктивность</strong> 19,6-27,6 ц/га;*</span>
                                            <br>
                                            <br>
                                            <span class="deep_green_italic_text">БКП 2,2-2,8 <strong>повышенная продуктивность</strong> 27,7-35,7 ц/га;*</span>
                                            <br>
                                            <br>
                                            <span class="blue_italic_text">БКП 2,8-3,4 <strong>повышенная продуктивность</strong> 35,8-43,7 ц/га.*</span>
                                                <br>
                                                
                                            <span class="blue_italic_text"><i>* - климатически обусловленная урожайность.</i></span>


                                            <span class="header1"><center>Использованная литература</center></span>
                                            <p><i>
                                            1. Шашко Д.И. Агроклиматические ресурсы СССР /Д.И. Шашко. Л.: Гидрометеоиздат. – 1985. – 248с.
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

if(isset($meteo_id))
{
    if(isset($year_slide))
    {
        ?>
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <script type="text/javascript">

            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawMeanChart);
            function drawMeanChart() {
                var MeanData = new google.visualization.DataTable();
                MeanData.addColumn('string', 'Период');
                MeanData.addColumn('number', 'Показатель');
                <?
                $DataSet = "MeanData.addRows([";
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
                var MeanChart = new google.visualization.AreaChart(document.getElementById('chart_div_mean'));
                MeanChart.draw(MeanData, {width: 1100, height: 600, title: 'Динамика биоклиматического потенциала. Метеостанция <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние)',
                    colors:['#9a01a0'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#9a01a0', fontSize: 14}},
                    vAxis: {title: 'Значение биоклиматического потенциала Д.И. Шашко', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#9a01a0', fontSize: 14}},
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
