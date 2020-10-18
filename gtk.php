<?php
//Текстовый заголовок страницы
$title = 'Анализ гидротермического коэффициента (показатель Г.Т. Селянинова)';
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

$ValueTitles = array('апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь');


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
                                    <form action="gtk.php" method="POST">
                                        <div class="col-md-6">

                                            <div class="get-period--form">
                                                <div class="form-group">
                                                    <!--список метеостанций-->
                                                    <label for="basic_station">Выберите метеостанцию :</label>

                                                    <select class="form-control" name="meteo_gtk" id="basic_station">
                                                        <?
                                                        $i = 1;
                                                        for ($i=1; $i<18; $i++)
                                                        {
                                                            if(isset($meteo_gtk) && $meteo_gtk == $i)
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
                                                    <label for="basic_period">Год или период: </label>
                                                    <input required type="text" placeholder="например: 2015 или 1995-2005"
                                                           class="form-control" id="basic_period" name="period_gtk" value="<?echo $period_gtk; ?>">
                                                </div> <!--period-->
                                            </div> <!-- period form-->

                                            <div class="form-group">
                                                <input type="submit" class=" btn btn-default btn-lg show-graph--button" value="Показать">
                                            </div>

                                        </div>

                                    </form> <!--GET GRAPH form-->
                                    <div class="content">
                                        <div class="col-md-12" style="position: relative;">

                                            <p>В базе данных содержится информация по всем действующим метеостанциям Ставропольского края за период 1961г. по настоящее время</p>
                                            <?php
                                            $OneYear = false;
                                            $Link = ConnectDB();

                                            //определение года последнего хранящегося в базе данных
                                            $sql = "select * from ClimateData_GTK ORDER BY Year DESC";
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


                                            if(isset($meteo_gtk) && isset($period_gtk))
                                            {
                                                if (strlen($period_gtk) == 4)
                                                {
                                                    if ((int)$period_gtk > 1960 && (int)$period_gtk < ($LastYearDB + 1))
                                                    {
                                                        if ($meteo_gtk == 17)
                                                        {
                                                            $sql = "select * from ClimateData_GTK where Year = \"$period_gtk\" ORDER BY ID DESC";
                                                        } else
                                                        {
                                                            $sql = "select * from ClimateData_GTK where MeteostationID = \"$meteo_gtk\" AND Year = \"$period_gtk\" ORDER BY ID DESC";
                                                        }

                                                        $OneYear = true;

                                                    } else {
                                                        ?><p>Ошибка! Неправильно указан анализируемый период</p>
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
                                                        <?php include_once ('./include/footer.php');
                                                        exit();
                                                    }

                                                } else if (strlen($period_gtk) == 9)
                                                {
                                                    $periods = explode("-", $period_gtk);
                                                    if ((int)$periods[0] > 1960 && (int)$periods[0] < ($LastYearDB + 1) && (int)$periods[1] > 1960 && (int)$periods[1] < ($LastYearDB + 1))
                                                    {
                                                        if ($meteo_gtk == 17)
                                                        {
                                                            $sql = "select * from ClimateData_GTK where Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                        } else
                                                        {
                                                            $sql = "select * from ClimateData_GTK where MeteostationID = \"$meteo_gtk\" AND Year BETWEEN \"$periods[0]\" AND \"$periods[1]\" ORDER BY ID DESC";
                                                        }
                                                    } else {?>
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
                                                        <?php include_once ('./include/footer.php');
                                                        exit();
                                                    }
                                                }
                                                else
                                                {
                                                    ?><p>Ошибка! Неправильно указан анализируемый период</p>
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
                                                            <?php include_once ('./include/footer.php');
                                                            exit();
                                                }

                                                $query_result = mysqli_query($Link, $sql);

                                                $RowsCount = $query_result->num_rows;
                                                //$ChartValues = array();
                                                $CurValue = 0;
                                                //for ($i = 0; $i < 12; $i++) {
                                                //    $ChartValues[$i] = 0;
                                                //}

                                                $j = 0;
                                                //температура
                                                while ($row = $query_result->fetch_assoc())
                                                {
                                                    for ($i = 0; $i < 7; $i++)
                                                    {
                                                        $Index = "T";
                                                        $Index .= strval($i + 4);
                                                        $CurValue = $row[$Index];
                                                        $CurValue = round($CurValue, 1);
                                                        $ValuesT[$j][$i] = $CurValue;
                                                        
                                                    }
                                                    $j++;
                                                }

                                                //осадки
                                                $tmp_p = array();
                                                $j = 0;
                                                $query_result = mysqli_query($Link, $sql);
                                                while ($row = $query_result->fetch_assoc())
                                                {
                                                    for ($i = 0; $i < 7; $i++)
                                                    {
                                                        $Index = "P";
                                                        $Index .= strval($i + 4);
                                                        $CurValue = $row[$Index];
                                                        $CurValue = round($CurValue, 1);
                                                        $ValuesP[$j][$i] = $CurValue;
                                                    }
                                                    $j++;
                                                }

                                                //дни
                                                $j = 0;
                                                $query_result = mysqli_query($Link, $sql);
                                                while ($row = $query_result->fetch_assoc())
                                                {
                                                    for ($i = 0; $i < 7; $i++)
                                                    {
                                                        $Index = "D";
                                                        $Index .= strval($i + 4);
                                                        $CurValue = $row[$Index];
                                                        $CurValue = round($CurValue, 1);
                                                        $ValuesD[$j][$i] = $CurValue;
                                                    }
                                                    $j++;
                                                }

                                                //РАСЧЕТ ГТК ЗА КАЖДЫЙ МЕСЯЦ
                                                $IgnoredMonths = 0;
                                                for ($i = 0; $i < $RowsCount; $i++)
                                                {
                                                    for ($j = 0; $j < 7; $j++)
                                                    {
                                                        if ($ValuesT[$i][$j] >= 10.0 && $ValuesT[$i][$j] != "NULL")
                                                        {
                                                            $GTKCurValue = ($ValuesP[$i][$j] * 10) / ($ValuesT[$i][$j] * $ValuesD[$i][$j]);
                                                            $GTK[$j] += $GTKCurValue;
                                                            $SumT[$i] += $ValuesT[$i][$j] * $ValuesD[$i][$j];
                                                        }

                                                        if ($ValuesT[$i][$j] == "NULL" && $OneYear == true)
                                                        {
                                                            $GTK[$j] = -1;
                                                        }
                                                    }
                                                }

                                                $SummTResultValue = 0;

                                                for ($i = 0; $i < $RowsCount; $i++)
                                                {
                                                    $SummTResultValue += $SumT[$i];
                                                }

                                                $SummTResultValue /= $RowsCount;
                                                $SummTResultValue = round($SummTResultValue, 0);

                                                //gtk 4-6, 7-10, 4-10
                                                $ValueT_4_6 = array();
                                                $ValueP_4_6 = array();
                                                $ValueT_7_10 = array();
                                                $ValueP_7_10 = array();
                                                $ValueT_4_10 = array();
                                                $ValueP_4_10 = array();
                                                
                                                for ($i = 0; $i < $RowsCount; $i++)
                                                {
                                                   
                                                    for ($j = 0; $j < 7; $j++)
                                                    {
                                                        if ($ValuesT[$i][$j] > 0.0 && $ValuesT[$i][$j] != "NULL")
                                                        {
                                                            if ($j >= 0 && $j < 3)
                                                            {
                                                                $ValueT_4_6[$i] += ($ValuesT[$i][$j] * $ValuesD[$i][$j]);
                                                                $ValueP_4_6[$i] += $ValuesP[$i][$j];
                                                            } else
                                                                {
                                                                $ValueT_7_10[$i] += ($ValuesT[$i][$j] * $ValuesD[$i][$j]);
                                                                $ValueP_7_10[$i] += $ValuesP[$i][$j];
                                                            }
                                                        }
                                                        
                                                    }
                                                }

                                                for ($i = 0; $i < $RowsCount; $i++) {

                                                    $ValueT_4_10[$i] = $ValueT_4_6[$i] + $ValueT_7_10[$i];
                                                    $ValueP_4_10[$i] = $ValueP_4_6[$i] + $ValueP_7_10[$i];
                                                }
                                                
                                               

                                                for ($i = 0; $i < $RowsCount; $i++)
                                                {
                                                    if ($ValueT_4_6[$i] > 0)
                                                    {
                                                        
                                                        $GTK[7] += ($ValueP_4_6[$i]) / (0.1 * ($ValueT_4_6[$i]));
                                                    } else
                                                        {
                                                        $GTK[7] = 0;
                                                    }


                                                    if ($ValueT_7_10[$i] > 0)
                                                    {
                                                        $GTK[8] += ($ValueP_7_10[$i]) / (0.1 * ($ValueT_7_10[$i]));
                                                    } else
                                                        {
                                                        $GTK[8] = 0;
                                                    }

                                                    if ($ValueT_4_10[$i] > 0)
                                                    {
                                                        
                                                        $GTK[9] += ($ValueP_4_10[$i]) / (0.1 * ($ValueT_4_10[$i]));
                                                    } else
                                                        {
                                                        $GTK[9] = 0;
                                                    }
                                                }

                                                for ($i = 0; $i < 10; $i++)
                                                {
                                                    $GTK[$i] /= $RowsCount;
                                                    $GTK[$i] = round($GTK[$i], 2);
                                                    if ($GTK[$i] > 4.0)
                                                    {
                                                        $GTK[$i] = 4.0;
                                                    }

                                                    //$ChartValues[$i] = ($GTK[$i] * 100.0) / 4.0;
                                                    //echo $GTK[$i];
                                                }

                                                        //Условные обозначения
                                                        $Legend = array(
                                                            'region_name' => $Meteostations[$meteo_gtk],
                                                            'years' => $period_gtk
                                                        );

                                                        for ($i = 0; $i < count($GTK) + 1; $i++)
                                                        {
                                                            if($GTK[$i] < 0)
                                                            {
                                                                $GTK[$i] = 0;
                                                            }
                                                        }

                                                        require_once('./admin/actions/charts/index.php');
                                                        createSpecialColumnChart($Legend, $GTK, $SummTResultValue);
                                            }
                                            ?>
                                            <p>
                                                Единым комплексным показателем, характеризующим тепло и влагообеспеченность территории в вегетационный период, является гидротермический коэффициент (ГТК), Г.Т. Селянинова (1928г.), который рассчитывается по формуле:</p>

                                            <span class="deep_green_italic_text"><center><strong>ГТК = сумм(P) / (0.1 * сумм(T > 10 градусов Цельсия)</strong></center></span>
                                            <br>
                                            <span class="deep_green_italic_text">где,
                                            <br>
                                            сумм(P) – сумма осадков за теплый период, мм;
                                            <br>
                                            сумм(T > 10 градусов С) – сумма температур выше 10 градусов Цельсия за этот же период.
                                            </span>
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
