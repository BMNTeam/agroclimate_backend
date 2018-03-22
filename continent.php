<?php
//Текстовый заголовок страницы
$title = 'Анализ динамики континентальности климата (показатель Н.Н. Иванова)';
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

$meteo_const = array(44.72, 45.4, 45.1, 44.8, 44.15, 45.92, 44.42, 45.27, 43.9, 45.85, 44.22, 44.63, 45.5, 44.15, 45.35, 45.03);

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
                                    <form action="continent.php" method="POST">
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
                                                    <label for="temperature_forecast">Прогнозируемое значение показателя Н.Н. Иванова: </label>
                                                    <input required type="text" placeholder="например: 160"
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

                                                    $query_result_tp=mysqli_query($Link, $sql_TP);

                                                    $CountProcessedRows = 0;
                                                    $CurYear = 1959;

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

                                                        if($row_tp["T7"] != "" && $row_tp["T1"] != "")
                                                        {
                                                            $AmplitudeT = 0;
                                                            $AmplitudeT = $row_tp["T7"] - $row_tp["T1"];
                                                            //расчет континентальности климата
                                                            $ContValue = ($AmplitudeT * 100) / (0.33 * $meteo_const[$meteo_id-1]);
                                                            $CountYear++;
                                                        }

                                                        $PerYearValue[$CurYear + $CountProcessedRows] = $ContValue;
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
                                                        $CurSlideValue = round($CurSlideValue, 0);


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

                                                    $CHT_Slide = "Динамика показателя континентальности климата (Н.Н. Иванова). Метеостанция: ".$Meteostations[$meteo_id];
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
                                                <span class="red_italic_text">Ошибка среднего: <strong><?echo round($STD_Dev/(sqrt(GetCount($ResultArray))), 1);?></strong></span>
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
                                                Проведение агроклиматического районирования территории края по континентальности климата позволяет дополнить характеристику территории по тепло и влагообеспеченности такими значимыми для сельскохозяйственных культур параметрами как степень контрастности климата.
                                            </p>

                                            <p>
                                                В качестве показателя степени континентальности использована годовая амплитуда температуры, выраженная в процентах от средней для данной широты.
                                            </p>
                                            <p>
                                                Чем континентальнее климат, тем короче весенний и осенний сезоны. Короткие сезоны определяют необходимость проведения сельскохозяйственных работ в сжатые сроки. Чаще всего с усилением континентальности возрастает разрыв между продолжительностью беззаморозкового и основного вегетационного периодов. Соотношение продолжительности этих периодов указывает на степень заморозкоопасности. Меньшая заморозкоопасность характерна для территорий с положительными отклонениями. Чем больше положительные отклонения, тем благоприятнее температурные условия для произрастания плодовых и овощных культур.
                                            </p>
                                            <p>
                                                Установлено, что с увеличением континентальности заметно повышается качество зерна. Наиболее тесная связь отмечена между содержанием белка и среднегодовой амплитудой температуры воздуха, которая является необходимым условием для определения континентальности климата. Районы с амплитудами температуры воздуха от 20° до 40°C характеризуются высоким содержанием белка, в районах с амплитудой ниже 20°C пшеница низкобелковая.
                                            </p>


                                            <p>
                                                Континентальность определялась по показателю Н.Н. Иванова (1948):
                                            </p>

                                            <span class="brown_italic_text"><center><strong>К = А * 100 / 0,33 * широта</strong></center></span>
                                            <br>
                                            <span class="brown_italic_text">где,
                                            <br>
                                            А – годовая амплитуда температуры из среднемесячных значений;
                                            <br>
                                            широта – широта местности;
                                            </span>

                                            <p>
                                                По Иванову, К = 100% означает уравновешенное влияние континентов и океанов на климат, при К < 100% преобладает влияние океанов, при К > 100% - влияние континентов. Для территории России значение показателя К изменяются примерно от 100 до 300% (Природно-сельскохозяйственное районирование…, 1975).
                                            </p>

                                            <span class="header1"><center>Использованная литература</center></span>
                                            <p><i>
                                                    1. Иванов Н.Н. Ландшафтно-климатические зоны земного шара /Н.Н. Иванов. - М.-Л.: Изд. АН СССР, – 1948. – 130с.<br>

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
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Период');
                data.addColumn('number', 'Показатель');
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
                chart.draw(data, {width: 1100, height: 600, title: 'Динамика континентальности климата. Метеостанция <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние)',
                    colors:['#af6b13'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    vAxis: {title: 'Значение показателя континентальности климата Н.Н. Иванова', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
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

