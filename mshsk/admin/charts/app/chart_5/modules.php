<?php
/**
 * Create Column Chart diagram
 * @param $first_region array
 * @param $arr1 array first years values
 * @param $arr2 array second years values
 * @param int $chart_num
 * @param bool $isAgro
 * @return void
 */
function createLineChartWithDoubleY ( $first_region, $arr1, $arr2, $chart_num = 0, $isAgro = false)
{
    global $months;

    // Annotations work only on previous version of Google Charts !!!

    $first_region = $first_region;

    $number_of_months = 0;

    //Find the longer one (hardcoded for now needs revision)
    if (count($arr1) == count($arr2)) {
        $number_of_months = 12;
    } elseif (count($arr1) > count($arr2)) {
        $number_of_months = count($arr1);
    } else {
        $number_of_months = count($arr2);
    }

    //$_GET["chart_num"] = $chart_number;
    ?>

    <?php require(__DIR__ .'/../remap_array.php');?>
    <?php require('chart_5_html.php');?>
    <?php require('chart_5_js.php');?>


<?php

}
