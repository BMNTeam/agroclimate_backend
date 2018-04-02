<?php
header('Access-Control-Allow-Origin: *');
require_once('../classes/TP.php');
require_once('../../../include/DB_itit.php');
require_once('../controllers/custom_zones.php');
/**
 * Created by PhpStorm.
 * User: maksimbarsukov
 * Date: 31/03/2018
 * Time: 14:41
 */
$tp = new TP($db);

if( $_GET['mode'] === 'single') {

    $year = $_GET['yearStart'];
    $meteostationId = $_GET['meteostationId'];

    $query = "WHERE Year=$year AND MeteostationID=$meteostationId";
    $result = $tp->get($query);
    print json_encode( $result );
    return;

}

if( $_GET['mode'] === 'plural') {

    $year_start = $_GET['yearStart'];
    $year_end = $_GET['yearEnd'];
    $meteostationId = $_GET['meteostationId'];

    $query = "WHERE Year BETWEEN $year_start AND $year_end  AND MeteostationID=$meteostationId";
    $result = $tp->get($query);
    print json_encode( $result );
    return;

}

if( $_GET['mode'] === 'custom')
{
    $zone = $_GET['meteostationId'];
    $year_start = $_GET['yearStart'];
    $year_end = $_GET['yearEnd'];

    print json_encode(getCustomZonesInfo($zone, $year_start, $year_end, $db));
    return;
}