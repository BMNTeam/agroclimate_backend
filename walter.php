<?php
//Текстовый заголовок страницы
$title = 'Анализ климатический условий с помощью климадиаграммы H.Walter';
$heading = 'Страница анализа значений за год или период';
$sub_heading = 'Анализ проводится за период 1961г. по настоящее время';

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
                                    <form action="walter.php" method="POST">
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
                                                    <label for="basic_period">Период: </label>
                                                    <input required type="text" placeholder="например: например: 2015 или 1995-2005"
                                                           class="form-control" id="basic_period" name="meteo_period" value="<?echo $meteo_period;?>">
                                                </div> <!--period-->
                                            </div> <!-- period form-->
                                            <div class="form-group">
                                                <input type="submit" class=" btn btn-default btn-lg show-graph--button" value="Показать">
                                            </div>

                                        </div>

                                    </form> <!--GET GRAPH form-->

                                    <div class="content">
                                        <div class="col-md-12" style="position: relative;">
                                            <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период 1961г. по настоящее время.</p>
											<?php

                                            $OneYear = false;
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
                                            else if (strlen($meteo_period) == 9) {
                                                $periods = explode("-", $meteo_period);
                                                if ((int)$periods[0] > 1960 && (int)$periods[0] < ($LastYearDB + 1) && (int)$periods[1] > 1960 && (int)$periods[1] < ($LastYearDB + 1)) {

                                                    if ($meteo_id == 17) {
                                                        $sql = "select * from ClimateData_TP where Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                    } else {
                                                        $sql = "select * from ClimateData_TP where MeteostationID = \"$meteo_id\" AND Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
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

                                            $query_result = mysqli_query($Link, $sql);

                                            $CountValuesT = array();
                                            $CountValuesP = array();
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
                                                'region_name' => $Meteostations[$meteo_id],
                                                'years' => $meteo_period
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

                                            require_once('./admin/charts/index.php');
                                            createLineChartWithDoubleY($Legend1, $ValueTTr, $ValuePTr, $chart_num);



                                            ?>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
<br><br>
                                            <center><h4><b>Пример</b></h4></center>

                                            <center><img src="img/pics/walter.jpg" width="900"></center>
                                            <?}
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
<?php include_once ('./include/footer.php'); ?>
