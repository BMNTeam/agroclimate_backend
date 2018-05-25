<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
require_once('../../../include/DB_itit.php');
require_once ('../classes/Settings.php');
/**
 * Created by PhpStorm.
 * User: maksimbarsukov
 * Date: 31/03/2018
 * Time: 14:41
 */

if( isset($_GET['all']) ) {

    $result =  $settings;
    print json_encode( $result );
    return;

}

if( isset($_POST) ) {
    $post = json_decode(file_get_contents('php://input'), true);
    $maintenance = $post['settings']['maintenance'];
    print_r($maintenance);
    $settings->maintenance = (int)$maintenance;
    $settings->save();


}


