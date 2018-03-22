
<?php
//Текстовый заголовок страницы
$title = 'Анализ динамики агроклиматических условий возделывания озимой пшеницы (показатель Е.С. Улановой)';
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
                                    <form action="ylanova.php" method="POST">
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
                                                    <label for="temperature_forecast">Прогнозируемое значение показателя Е.С. Улановой: </label>
                                                    <input required type="text" placeholder="например: 15"
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
                                                            $PrevP[$CountYear] = $row_tp["P10"] + $row_tp["P11"] + $row_tp["P12"];
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

                                                        if($row_tp["P4"] != "" && $row_tp["P5"] != "" && $row_tp["P6"] != "" && $PrevP[$CountYear] != "" && $row_tp["T4"] != "" && $row_tp["T5"] != "" && $row_tp["T6"] != "")
                                                        {
                                                            $PrevP[$CountYear] += $row_tp["P1"];
                                                            $PrevP[$CountYear] += $row_tp["P2"];
                                                            $PrevP[$CountYear] += $row_tp["P3"];
                                                            $YlValue = (0.5 * ($PrevP[$CountYear]) + ($row_tp["P4"] + $row_tp["P5"] + $row_tp["P6"])) / (0.01 * (($row_tp["T4"] * 30) + ($row_tp["T5"] * 31) + ($row_tp["T6"] * 30)));
                                                            $CountYear++;
                                                        }
                                                    $PerYearValue[$CurYear + $CountProcessedRows] = $YlValue;

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

                                                    $CHT_Slide = "Динамика показателя Е.С. Улановой. Метеостанция: ".$Meteostations[$meteo_id];
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
                                                Согласно  методике,  разработанной  Е.С.  Улановой (1973),  условия весенне-летнего  периода  формируют  продуктивность  озимой  пшеницы  и представлены  двумя  инерционными  факторами:  весенним  запасом продуктивной влаги в метровом слое почвы посевов пшеницы и состоянием культуры во время возобновления весенней вегетации. </p>
                                            <p>
                                                Как  показали исследования Л.И.  Желнаковой (1992)  существует достоверная  связь (r = 0,95)  между  этим  показателем  Улановой  и продуктивностью озимой пшеницы по непаровым предшественникам. В связи с тем, что длинные ряды наблюдений за запасами продуктивной влаги в почве существует не на всех метеостанциях края, в качестве  показателя  косвенно  определяющего  весенние  влагозапасы, использовалась  сумма осенне-зимних осадков за период (ноябрь–март) с коэффициентом 0,5.
                                            </p>
                                            <p>
                                                Эта модификация показателя была апробирована автором  при районировании агроклиматических условий формирования биологической продуктивности озимой пшеницы в зоне черноземов и каштановых почв (Уланова, 1975).
                                            </p>
                                            <p>
                                                Рассчитывалась по следующей формуле (Уланова, 1973):
                                            </p>

                                            <span class="deep_green_italic_text"><center><strong>К = (0,5 R(X-III) + Rв.с.) / (0,01 * сумма(Tв.с.))</strong></center></span>
                                            <br>
                                            <span class="deep_green_italic_text">где,
                                            <br>
                                            R(X-III) – сумма осадков с октября по март;
                                            <br>
                                            Rв.с. – сумма осадков от возобновления вегетации озимой пшеницы весной до восковой спелости;
                                            <br>
                                            Rв.с. – сумма осадков от возобновления вегетации озимой пшеницы весной до восковой спелости;
                                            <br>
                                            Сумма(Tв.с.) – сумма среднесуточных активных температур (выше 5 градусов) от возобновления весенней вегетации до восковой спелости.
                                            </span>

                                            <p>
                                                На основе связи условий влагообеспеченности весенне-летнего периода с  урожайностью  озимой  пшеницы  Е.С.  Улановой (1975)  предложена следующая  градация  условий  формирования  продуктивности  озимой пшеницы:
                                            </p>
                                            <span class="red_italic_text">K < 12 – неблагоприятные, плохие условия;</span>
                                            <br>
                                            <span class="orange_italic_text">К от 12 до 18 – недостаточно благоприятные условия;</span>
                                            <br>
                                            <span class="deep_green_italic_text">К от 18 до 23 – удовлетворительные условия;</span>
                                            <br>
                                            <span class="blue_italic_text">К > 23 – хорошие условия.</span>
                                            <br>
                                            <br>
                                            <span class="header1"><center>Использованная литература</center></span>
                                            <p><i>
                                                    1. Уланова  Е.С.  Агрометеорологические  условия  и  урожайность  озимой пшеницы / Е.С. Уланова. - Л.: Гидрометеоиздат. – 1975 – 302с.<br>
                                                    2. Уланова  Е.С.  Методика  агроклиматического  районирования  условий формирования  урожайности  озимой  пшеницы  в  черноземной  зоне  в весенне-летний  период /Е.С.  Уланова //  Труды  ГМЦ  СССР. 1973. – Вып.111. – С. 65–69. <br>
                                                    3. Желнакова  Л.И.  Оптимизация  использования  почвенно-климатических ресурсов  Центрального  Предкавказья  для  производства  зерна  озимой пшеницы  с  помощью  чистых  паров:  Автореф.  дис. ...  канд.  с-х.  наук /Л.И. Желнакова. – Ставрополь. – 1992. – 25 с.
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
                chart.draw(data, {width: 1100, height: 600, title: 'Динамика агроклиматических условий возделывания озимой пшеницы. Метеостанция: <? echo $Meteostations[$meteo_id];?> (скользящие <? echo $year_slide;?> летние)',
                    colors:['#197e05'],
                    hAxis: {title: 'Период', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
                    vAxis: {title: 'Значение показателя Е.С. Улановой', titleTextStyle: {color: '#000000'}, slantedTextAngle: 90, textStyle: {color: '#a9a9a9', fontSize: 14}},
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
