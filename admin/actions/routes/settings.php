<?php
header('Access-Control-Allow-Origin: *');
require_once('../../../include/DB_itit.php');
require_once ('../classes/Settings.php');
/**
 * Created by PhpStorm.
 * User: maksimbarsukov
 * Date: 31/03/2018
 * Time: 14:41
 */
$settings = new Settings($db);

if( isset($_GET['settings']) ) {



    $result = $settings->maintenance;
    print json_encode( $result );
    return;

}

if( isset($_POST['settings']) ) {

    // TODO: finish integration
    $settings->maintenance = 2;
    $settings->save();

}


