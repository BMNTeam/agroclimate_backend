<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 17.01.2017
 * Time: 13:09
 */
//TODO: fix view after saving decades data and getting another information
// Include directory path
$INCLUDE_ROOT = dirname(__FILE__);

//require_once('DB_itit.php');
require_once($INCLUDE_ROOT.'/../admin/classes/average.php');
require_once ($INCLUDE_ROOT.'/../admin/classes/Decades.php');
require_once ($INCLUDE_ROOT.'/../admin/classes/TP.php');


//On form loading query ---- maybe it's just for test purposes)))
$selectMeteostations = $db->query("SELECT * FROM ClimateData_meteostation_id");
$resultMeteostations = ($selectMeteostations->fetchAll(PDO::FETCH_ASSOC));


//Edit data
$isEditable = isset($_GET['edit']);

//GET variables
$selectRegion = $_GET['select'];
$selectStartYear = $_GET['start_year'];
$selectEndYear = $_GET['end_year'];
$year_to_edit = $_GET['year_to_edit'];


/**
 *
 * Function to get data from agriclimate database
 * @param $db PDO Connection
 * @param string $isEditable if checkbox checked
 * @param string $selectRegion  which staticon had been selected
 * @param sting $selectStartYear
 * @param string $selectEndYear
 * @param string $year_to_edit if user want to chenge data
 *
 * @return associative array with found data
 *
 */
//Select conditionals
function getDataFromDatabase ( $db, $isEditable, $selectRegion, $selectStartYear, $selectEndYear, $year_to_edit  ){
    $selectString = '';

    if( $selectEndYear == '' && ! $isEditable) // Just get year to edit
    {
        $selectString = "SELECT * FROM ClimateData_TP WHERE MeteostationID=? AND Year = ?";
    }
    elseif($selectEndYear != '' && ! $isEditable) // If selected 2 year catch both
    {
        $selectString = "SELECT * FROM ClimateData_TP WHERE MeteostationID=? AND Year BETWEEN ? AND ?";
    }
    elseif( ! empty( $year_to_edit ) && $isEditable )
    {
        $selectString = "SELECT * FROM ClimateData_TP WHERE MeteostationID=? AND Year = ?";

    }elseif( empty( $year_to_edit) ) {/*always in the end*/

        $selectString = "SELECT * FROM ClimateData_TP WHERE MeteostationID=? AND Year = ?";
    }



//Prepare query
    $dataToAnalyse = $db-> prepare( $selectString );

//Secure send variables
    $dataToAnalyse->bindValue(1, $selectRegion, PDO::PARAM_INT);
    if ( $isEditable )
    {
        $dataToAnalyse->bindValue(2, $year_to_edit, PDO::PARAM_INT);
    }else
    {
        $dataToAnalyse->bindValue(2, $selectStartYear, PDO::PARAM_INT);
    }
    $dataToAnalyse->bindValue(3, $selectEndYear, PDO::PARAM_INT);


//Execute query
    $dataToAnalyse->execute();
    print_r($dataToAnalyse);
//Get results al associative array
    return $resultDataToAnalyse = $dataToAnalyse->fetchAll(PDO::FETCH_ASSOC);
}

$resultDataToAnalyse = getDataFromDatabase( $db, $isEditable, $selectRegion, $selectStartYear, $selectEndYear, $year_to_edit);

/*** End queries section ****/


/**
 *
 * Post section
 *
*/

if(!empty($_POST) && empty($_POST['T1_1']) ){

$tp = new TP($db);
$tp->set($_POST);

//Get results al associative array
$postRegion = $_POST['MeteostationID'];
$postYear = $_POST['year_to_edit'];

//Show changed data
$resultDataToAnalyse = getDataFromDatabase( $db, $isEditable, $postRegion, $postYear, $selectEndYear, $postYear);



} elseif (!empty($_POST) && !empty($_POST['T1_1']) ) // POST with decades
{
    $decades = new Decades($db);
    $decades->set($_POST);

    //Get results al associative array
    $postRegion = $_POST['select'];
    $postYear = $_POST['year_to_edit'];

    //$year_to_edit = null;

//Show changed data
    $resultDataToAnalyse = getDataFromDatabase( $db, $isEditable, $postRegion, $postYear, $selectEndYear, $postYear);
}

/**
 *
 * Custom zones block
 *
*/
switch ($_GET['select']){
    case 'region':
        $average = new Average(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16), $selectStartYear,$selectEndYear, $db);
        $resultDataToAnalyse = $average->get_average();
        break;
    case 'first_zone':
        $average = new Average(array(2,6,14), $selectStartYear, $selectEndYear, $db);
        $resultDataToAnalyse = $average->get_average();
        break;
    case 'second_zone':
        $average = new Average(array(1,3,4,7,15), $selectStartYear, $selectEndYear, $db);
        $resultDataToAnalyse = $average->get_average();
        break;
    case 'third_zone':
        $average = new Average(array(8,10,12,13,16), $selectStartYear, $selectEndYear, $db);
        $resultDataToAnalyse = $average->get_average();
        break;
    case 'fourth_zone':
        $average = new Average(array(5,9,11), $selectStartYear,     $selectEndYear, $db);
        $resultDataToAnalyse = $average->get_average();
        break;
}

/*************** end custom zones *********************************/




//Test section
//print_r($_POST);
//print_r($resultDataToAnalyse);
//print_r($updatedData);