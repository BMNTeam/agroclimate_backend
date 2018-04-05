<?php
header('Access-Control-Allow-Origin: *');
require_once('../classes/Decades.php');
require_once('../../../include/DB_itit.php');
$decades = new Decades($db);
if($_GET['mode'] === 'edit'){
    $year = $_GET['yearStart'];
    $meteostationId = $_GET['meteostationId'];

    $result = $decades->get($meteostationId, $year);
    print json_encode( $result );
    return;
}