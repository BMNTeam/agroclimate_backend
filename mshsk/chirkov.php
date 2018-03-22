<?php
//Текстовый заголовок страницы
$title = 'Анализ динамики агроклиматических условий возделывания кукурузы (показатель Ю.И. Чиркова)';
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

<section class="content">

    <div class="content-container ">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="block-container clearfix last-container">
                        <div class="container-heading content-header-color">
                            <h4><? echo $title; ?></h4>
                        </div>
                        <div class="container-elements clearfix">

                            <div class="col-md-12">
                                <div class="row">
                                    <form action="chirkov.php" method="POST">
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
                                                    <label for="temperature_forecast">Прогнозируемое значение показателя Ю.И. Чиркова: </label>
                                                    <input required type="text" placeholder="например: 0.7"
                                                           class="form-control" id="temperature_forecast" name="prediction_value" value="<? echo $prediction_value;?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" class=" btn btn-default btn-lg show-graph--button"
                                                       value="Показать">
                                            </div>

                                        </div>

                                    </form> <!--GET GRAPH form-->
                                    <hr>
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
                                                    while($row_tp = mysqli_fetch_array($query_result_tp))
                                                    {
                                                        if($RowsCount > ($CountYear + 1))
                                                        {
                                                            $PrevP[$CountYear] = $row_tp["P11"] + $row_tp["P12"];
                                                            $CountYear++;
                                                        }
                                                        else
                                                        {
                                                            break;
                                                        }
                                                    }

                                                    $query_result_tp = mysqli_query($Link, $sql_TP);
                                                    $CountYear = 0;
                                                    $IsFirstYear = true;
                                                    while($row_tp = mysqli_fetch_array($query_result_tp))
                                                    {

                                                        if($IsFirstYear == true)
                                                        {
                                                            $IsFirstYear = false;
                                                            continue;
                                                        }

                                                        $CountProcessedRows++;

                                                        if($row_tp["P5"] != "" && $row_tp["P6"] != "" && $PrevP[$CountYear] != "" && $row_tp["T5"] != "" && $row_tp["T6"] != "")
                                                        {
                                                            $PrevP[$CountYear] += $row_tp["P1"];
                                                            $PrevP[$CountYear] += $row_tp["P2"];
                                                            $PrevP[$CountYear] += $row_tp["P3"];
                                                            $PrevP[$CountYear] += $row_tp["P4"];

                                                            $CurP = 0;
                                                            $CurP += $row_tp["P5"];
                                                            $CurP += $row_tp["P6"];
                                                            $CurP += $row_tp["P7"];
                                                            $CurP += $row_tp["P8"];

                                                            $ChirValue = (0.4 * ($PrevP[$CountYear]) + ($CurP)) / (0.18 * (($row_tp["T3"] * 31) + ($row_tp["T4"] * 30) + ($row_tp["T5"] * 31) + ($row_tp["T6"] * 30) + ($row_tp["T7"] * 31) + ($row_tp["T8"] * 31)));
                                                            $CountYear++;
                                                        }

                                                        $PerYearValue[$CurYear + $CountProcessedRows] = $ChirValue;
                                                        $MayAugustPValue[$CurYear + $CountProcessedRows] = $CurP;
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
                                                        $CurSlideValue = round($CurSlideValue, 3);


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

                                                    $CHT_Slide = "Динамика агроклиматических условий возделывания кукурузы. Метеостанция: ".$Meteostations[$meteo_id];
                                                    $CHT_Slide = win_utf8($CHT_Slide);


                                                    krsort($MayAugustPValue);

                                                    $i = 0;
                                                    foreach($MayAugustPValue as $key => $val)
                                                    {
                                                        $MyPreSlideValArrayP[$i] = $val;
                                                        $MyPreSlideKeyArrayP[$i] = $key;
                                                        $i++;
                                                    }

                                                    $BeginYearSlideP = 0;
                                                    for($i = 0; $i < $CountProcessedRows - $year_slide + 1; $i++)
                                                    {
                                                        $CurSlideValueP = 0;
                                                        for($j = 0; $j < $year_slide; $j++)
                                                        {
                                                            $CurSlideValueP += $MyPreSlideValArrayP[$BeginYearSlideP + $j];

                                                        }
                                                        $CurSlideValueP /= $year_slide;
                                                        $CurSlideValueP = round($CurSlideValueP, 3);


                                                        if($year_slide > 1)
                                                        {
                                                            $CurKeyValueP = $MyPreSlideKeyArrayP[$BeginYearSlideP + $year_slide - 1]."-".$MyPreSlideKeyArrayP[$BeginYearSlideP];
                                                        }
                                                        else
                                                        {
                                                            $CurKeyValueP = $MyPreSlideKeyArrayP[$BeginYearSlideP];
                                                        }

                                                        $MySlideArrayP[$MyPreSlideKeyArrayP[$BeginYearSlideP]] = $CurSlideValueP;
                                                        $MyKeyArrayP[$MyPreSlideKeyArrayP[$BeginYearSlideP]] = $CurKeyValueP;
                                                        $BeginYearSlideP++;
                                                    }

                                                    ksort($MySlideArrayP);
                                                    ksort($MyKeyArrayP);

                                                    foreach($MySlideArrayP as $key => $val)
                                                    {
                                                        $ResultArrayP[$MyKeyArrayP[$key]] = $val;
                                                    }


                                                    $CHT_SlideP = "Динамика осадков май-август: ".$Meteostations[$meteo_id];
                                                    $CHT_SlideP = win_utf8($CHT_Slide);

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
                                                <span class="red_italic_text">Ошибка среднего: <strong><?echo round($STD_Dev/(sqrt(GetCount($ResultArray))), 3);?></strong></span>
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
                                                Для оценки степени благоприятности современных агроклиматических условий для возделывания кукурузы на зерно был использован коэффициент увлажнения Ю.И. Чиркова (1969).
                                            </p>
                                            <p>
                                                Поздние пропашные культуры теплолюбивые. Нижним пределом возделывания кукурузы является сумма активных температур (> 10°С) в 2400 – 2600°С.
                                            </p>
                                            <p>
                                                Поскольку сумма активных температур на территории Ставропольского края превышает значение 3000°С то тепло не является лимитирующим фактором возделывания этой культуры. Ограничивающим фактором для успешного возделывания кукурузы на Ставрополье является недостаток влаги.
                                            </p>
                                            <p>
                                                Кукуруза активно возделывается на территории Ставропольского края в связи с чем, районирование территории по условиям ее возделывания представляет важный практический интерес.
                                            </p>

                                            <p>
                                                В качестве показателя для оценки условий влагообеспеченности кукурузы был использован коэффициент Ю.И. Чиркова (1969):
                                            </p>

                                            <span class="deep_green_italic_text"><center>К = (0,5 * R1 + R2) / (0,18 * сумма(Tв.с.))</center></span>
                                            <br>
                                            <span class="deep_green_italic_text">где,
                                            <br>
                                            R1 – осадки осенне-зимнего периода (ноябрь - апрель);
                                            <br>
                                            R2 – осадки за период вегетации кукурузы (май - август);
                                            <br>
                                            Сумма(Tв.с.) – сумма положительных температур за период вегетации кукурузы (май - август).
                                            </span>

                                            <p>
                                                Ранжирование условий влагообеспеченности по коэффициенту Чиркова осуществлено по следующей шкале:
                                            </p>
                                            <span class="red_italic_text">K < 0,4 - плохие условия увлажнения (балл 1); </span>
                                            <br>
                                            <span class="orange_italic_text">K = 0,4 - 0,6 - неудовлетворительные условия (балл 2);</span>
                                            <br>
                                            <span class="bronze_italic_text">K = 0,6 – 0,8 - неудовлетворительные условия (балл 3);</span>
                                            <br>
                                            <span class="deep_green_italic_text">K = 0,8 – 1,0 - хорошие условия (балл 4)</span>
                                            <br>
                                            <span class="blue_italic_text">K > 1,0 - оптимальные условия (балл 5)</span>

                                            <p>
                                                При оценке влагообеспеченности 3 балла необходимы эпизодические поливы, при оценке 1 и 2 балла орошение является залогом получения высоких урожаев. Иногда по зоне с оценкой увлажнения 4 балла полив также дает положительный эффект.
                                            </p>
                                            </td>
                                            </tr>


                                            <tr valign="top">
                                                <td align="center" valign="top">
                                                    <p>
                                                        В качестве границы производственной целесообразности возделывания кукурузы на зерно используется изогиета суммы осадков за май – август (200мм.). Значение суммы осадков менее 200мм. свидетельствует о большом риске возделывания кукурузы на этой территории.
                                                    </p>

                                                    <!--график для отображения данных-->
                                                    <center><div id="chart_div_meanP"></div></center>
                                                    <br>
                                                    <span class="header1"><center>Использованная литература</center></span>
                                                    <p><i>
                                                            1. Чирков Ю.И. Агрометеорологические условия и продуктивность кукурузы /Ю.И. Чирков. – Л.: Гидрометеоиздат. – 1969. – 251 с.
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
//if(isset($year_slide) && $year_slide > 1)
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
                MeanChart.draw(MeanData, {width: 1100, height: 600, title: 'Динамика агроклиматических условий возделывания кукурузы Ю.И. Чиркова. Метеостанция <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние)',
                    colors:['#de7e0d'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    vAxis: {title: 'Значение показателя Ю.И. Чиркова', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    pointSize: 6,
                    legend: "bottom",
                    curveType: "function"
                });
            }


            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawMeanChartP);
            function drawMeanChartP() {
                var MeanDataP = new google.visualization.DataTable();
                MeanDataP.addColumn('string', 'Период');
                MeanDataP.addColumn('number', 'Количество осадков');
                <?
                $DataSetP = "MeanDataP.addRows([";
                $i = 0;
                foreach($ResultArrayP as $key=>$value)
                {

                    $i++;
                    if($value != -100)
                    {

                        $DataSetP .= "['";
                        $DataSetP .= $key;
                        $DataSetP .= "', ";
                        $DataSetP .= $value;
                        $DataSetP .= "]";
                    }

                    if($i < ($CountProcessedRows - $year_slide + 1))
                    {
                        $DataSetP .= ",";
                    }
                    else
                    {
                        $DataSetP .= "]);";
                        break;
                    }
                }
                echo $DataSetP;
                ?>
                var MeanChartP = new google.visualization.AreaChart(document.getElementById('chart_div_meanP'));
                MeanChartP.draw(MeanDataP, {width: 1100, height: 600, title: 'Динамика осадков за май-август. Метеостанция <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние)',
                    colors:['#0f1ed9'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    vAxis: {title: 'Количество осадков за май-август, мм.', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
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