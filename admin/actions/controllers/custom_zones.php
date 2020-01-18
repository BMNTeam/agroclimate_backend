<?
require_once('../classes/Average.php');

function getCustomZonesInfo($zone, $year_start, $year_end, $db) {
    $resultDataToAnalyse = array();
    switch ($zone){
        case 'region':
            $average = new Average(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16), $year_start, $year_end, $db);
            $resultDataToAnalyse = $average->get_average();
            break;
        case 'first_zone':
            $average = new Average(array(2,6,14), $year_start, $year_end, $db);
            $resultDataToAnalyse = $average->get_average();
            break;
        case 'second_zone':
            $average = new Average(array(1,3,4,7,15), $year_start, $year_end, $db);
            $resultDataToAnalyse = $average->get_average();
            break;
        case 'third_zone':
            $average = new Average(array(8,10,12,13,16), $year_start, $year_end, $db);
            $resultDataToAnalyse = $average->get_average();
            break;
        case 'fourth_zone':
            $average = new Average(array(5,9,11), $year_start, $year_end, $db);
            $resultDataToAnalyse = $average->get_average();
            break;
    }

    $mapped_result = array();

    //Need to get rid of wrapper| ex: ['1999' => { Year: 1991...}]
    foreach ($resultDataToAnalyse as $wrapper => $payload) {
        array_push($mapped_result, $payload);
    }
    return $mapped_result;
}