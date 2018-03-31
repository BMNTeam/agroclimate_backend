<?php
header('Access-Control-Allow-Origin: *');
require_once('../classes/TP.php');
require_once('../../../include/DB_itit.php');
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