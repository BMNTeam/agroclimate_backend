<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
//header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
require_once('../classes/Decades.php');
require_once('../classes/Gtk.php');
require_once('../../../include/DB_itit.php');

$decades = new Decades($db);


if($_GET['mode'] === 'edit'){
    $year = $_GET['yearStart'];
    $meteostationId = $_GET['meteostationId'];

    $result = $decades->get($meteostationId, $year);
    print json_encode( $result );
    return;
}


if($_POST) {
    //Convert to usual JSON
    $json = file_get_contents('php://input');
    $post = json_decode($json);


    if(!empty($post->save) ) {
        $values = $decades->addNull($post->save);
        $decades->set($values);
        return;
    }
}
$gtk = new Gtk($db);
$gtk->set($_POST);