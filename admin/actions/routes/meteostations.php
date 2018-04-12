<?php
header('Access-Control-Allow-Origin: *');
require_once('../classes/Meteostations.php');
require_once('../../../include/DB_itit.php');
/**
 * Created by PhpStorm.
 * User: maksimbarsukov
 * Date: 31/03/2018
 * Time: 10:40
 */
$meteostations = new Meteostations($db);

if (isset($_GET['all'])) {
    print json_encode( $meteostations->get('all'), JSON_UNESCAPED_UNICODE );
}
