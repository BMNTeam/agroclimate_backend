<?php
//Текстовый заголовок страницы
$title = 'Анализ отклонения температуры воздуха по отдельным годам или периодам';
$heading = 'Страница анализа отклонения температуры воздуха';
$sub_heading = 'Анализ динамики проводится за период с 1961г. по настоящее время';

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
$Meteostations[17] = "Ставропольский край";
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
                            <h4><?echo $title;?></h4>
                        </div>
                        <div class="container-elements clearfix">

                            <div class="col-md-12">
                                <div class="row">
                                    <form action="analyze_t.php" method="POST">
                                        <div class="col-md-6">
                                            <!--форма для базового периода--->
                                            <h3 class="get-data-form--heading">Базовый период</h3>

                                            <div class="get-period--form">
                                                <div class="form-group">
                                                    <!--список метеостанций-->
                                                    <label for="basic_station">Выберите метеостанцию :</label>
                                                    <select class="form-control" name="meteo_t1" id="basic_station">
                                                        <?
                                                        $i = 1;
                                                        for ($i=1; $i<18; $i++)
                                                        {
                                                            if(isset($meteo_t1) && $meteo_t1 == $i)
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
                                                    <label for="basic_period">Период: </label>
                                                    <input required type="text" placeholder="например: 2000 или 1995-2005"
                                                           class="form-control" id="basic_period" name="period_t1" value="<? echo $period_t1;?>">
                                                </div> <!--period-->
                                            </div> <!-- period form-->
                                            <div class="form-group">
                                                <input type="submit" class=" btn btn-default btn-lg show-graph--button" value="Показать">
                                            </div>

                                        </div>
                                        <!--форма для анализируемого периода--->
                                        <div class="col-md-6">
                                            <h3 class="get-data-form--heading">Анализируемый период</h3>

                                            <div class="get-period--form">
                                                <div class="form-group">
                                                    <!--список метеостанций-->
                                                    <label for="basic_station">Выберите метеостанцию :</label>
                                                    <select class="form-control" name="meteo_t2" id="basic_station">
                                                        <?
                                                        $i = 1;
                                                        for ($i=1; $i<18; $i++)
                                                        {
                                                            if(isset($meteo_t2) && $meteo_t2 == $i)
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
                                                    <label for="analyzing_period">Период: </label>
                                                    <input required type="text" placeholder="например: 2000 или 1995-2005"
                                                           class="form-control" id="analyzing_period" name="period_t2" value="<? echo $period_t2;?>">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="content">
                                        <div class="col-md-12" style="position: relative;">

                                            <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период 1961г. по настоящее время</p>
                                            <?php

                                            if(isset($meteo_t1) && isset($period_t1) && isset($meteo_t2) && isset($period_t2))
                                            {
                                            $Link = ConnectDB();

                                            //определение года последнего хранящегося в базе данных
                                            $sql = "select * from ClimateData_TP ORDER BY Year DESC";
                                            $LastYear_QR = mysqli_query($Link, $sql);
                                            //echo $sql;

                                            if ($LastYear_QR->num_rows > 0) {
                                                while ($row = $LastYear_QR->fetch_assoc()) {
                                                    $LastYearDB = $row["Year"];
                                                    break;
                                                }
                                            }

                                            //ПАРАМЕТРЫ АНАЛИЗИРУЕМОГО ПЕРИОДА
                                            //Анализируем переменную периода
                                            if (strlen($period_t2) == 4)
                                            {
                                            if ((int)$period_t2 > 1960 && (int)$period_t2 < ($LastYearDB + 1))
                                            {
                                                if ($meteo_t2 == 17) {
                                                    $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where Year = \"$period_t2\" ORDER BY ID DESC";
                                                } else {
                                                    $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where MeteostationID = \"$meteo_t2\" AND Year = \"$period_t2\" ORDER BY ID DESC";
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
                                            elseif (strlen($period_t2) == 9) {
                                                $periods = explode("-", $period_t2);
                                                if ((int)$periods[0] > 1960 && (int)$periods[0] < ($LastYearDB + 1) && (int)$periods[1] > 1960 && (int)$periods[1] < ($LastYearDB + 1)) {
                                                    if ($meteo_t2 == 17) {
                                                        $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                    } else {
                                                        $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where MeteostationID = \"$meteo_t2\" AND Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                    }
                                                } else {
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
                                            } else {
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

                                            $Link = ConnectDB();
                                            $query_result = mysqli_query($Link, $sql);
                                            $CurValue = 0;
                                            $CountValuesAnalyze = array();
                                            $CountValuesBase = array();

                                            $AnalyzeValue = array();
                                            $BaseValue = array();

                                            for ($i = 0; $i < 12; $i++) {
                                                $CountValuesBase[$i] = 0;
                                                $CountValuesAnalyze[$i] = 0;
                                            }

                                            while ($row = $query_result->fetch_assoc()) {
                                                for ($i = 0; $i < 12; $i++) {
                                                    $Index = "T";
                                                    $Index .= strval($i + 1);
                                                    if ($row[$Index] != "") {
                                                        $CountValuesAnalyze[$i]++;
                                                        $AnalyzeValue[$i] += $row[$Index];
                                                    }
                                                }
                                            }

                                            for ($i = 0; $i < 12; $i++) {
                                                if ($CountValuesAnalyze[$i] == 0) {
                                                    $AnalyzeValue[$i] = -100;
                                                } else {
                                                    $AnalyzeValue[$i] /= $CountValuesAnalyze[$i];
                                                }
                                                $AnalyzeValue[$i] = round($AnalyzeValue[$i], 1);
                                            }

                                            //ПАРАМЕТРЫ БАЗОВОГО ПЕРИОДА
                                            //Анализируем переменную периода
                                            if (strlen($period_t1) == 4) {
                                                if ((int)$period_t1 > 1960 && (int)$period_t1 < ($LastYearDB + 1)) {
                                                    if ($meteo_t1 == 17) {
                                                        $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where Year = \"$period_t1\" ORDER BY ID DESC";
                                                    } else {
                                                        $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where MeteostationID = \"$meteo_t1\" AND Year = \"$period_t1\" ORDER BY ID DESC";
                                                    }
                                                } else {
                                                    ?>
                                                    <p>Ошибка! Неправильно указан базовый период</p>
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
                                            } elseif (strlen($period_t1) == 9) {
                                                $periods = explode("-", $period_t1);
                                                if ((int)$periods[0] > 1960 && (int)$periods[0] < ($LastYearDB + 1) && (int)$periods[1] > 1960 && (int)$periods[1] < ($LastYearDB + 1)) {
                                                    if ($meteo_t1 == 17) {
                                                        $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                    } else {
                                                        $sql = "select T1,T2,T3,T4,T5,T6,T7,T8,T9,T10,T11,T12 from ClimateData_TP where MeteostationID = \"$meteo_t1\" AND Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                    }
                                                } else {
                                                    ?>
                                                    <p>Ошибка! Неправильно указан базовый период</p>
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
                                            } else {
                                                ?>
                                                <p>Ошибка! Неправильно указан базовый период</p>
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

                                            $query_result = mysqli_query($Link, $sql);
                                            $CurValue = 0;

                                            for ($i = 0; $i < 12; $i++) {
                                                $CountValuesBase[$i] = 0;
                                                $CountValuesAnalyze[$i] = 0;
                                            }

                                            while ($row = $query_result->fetch_assoc()) {
                                                for ($i = 0; $i < 12; $i++) {
                                                    $Index = "T";
                                                    $Index .= strval($i + 1);
                                                    if ($row[$Index] != "") {
                                                        $CountValuesBase[$i]++;
                                                        $BaseValue[$i] += $row[$Index];
                                                    }
                                                }
                                            }

                                            for ($i = 0; $i < 12; $i++) {
                                                if ($CountValuesBase[$i] == 0) {
                                                    $BaseValue[$i] = -100;
                                                } else {
                                                    $BaseValue[$i] /= $CountValuesBase[$i];
                                                }
                                                $BaseValue[$i] = round($BaseValue[$i], 1);
                                            }

                                            $Legend1 = array(
                                                'region_name' => $Meteostations[$meteo_t1],
                                                'years' => $period_t1
                                            );

                                            $Legend2 = array(
                                                'region_name' => $Meteostations[$meteo_t2],
                                                'years' => $period_t2
                                            );

                                            $BaseValueTr = array();
                                            $AnalyzeValueTr = array();
                                            for($i = 0; $i < 12; $i++) {

                                                if ($BaseValue[$i] > -100) {
                                                    $BaseValueTr[$i] = $BaseValue[$i];
                                                }
                                                if($AnalyzeValue[$i] > -100){
                                                    $AnalyzeValueTr[$i] = $AnalyzeValue[$i];
                                                }
                                            }


                                            require_once('./admin/actions/charts/index.php');
                                            createLineChartForTemperature($Legend1, $Legend2, $BaseValueTr, $AnalyzeValueTr);
                                            }?>
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
<?php include_once ('./include/footer.php'); ?>
