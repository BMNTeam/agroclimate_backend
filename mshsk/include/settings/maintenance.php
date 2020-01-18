<?php
require_once(dirname(__FILE__) . '/../../../admin/actions/classes/Settings.php');

switch (true)
{
    case $settings->maintenanceMshsk && !strpos($_SERVER['REQUEST_URI'], 'under_maintenance.php'):
        header('Location: /mshsk/under_maintenance.php');
        break;
    case !$settings->maintenanceMshsk && strpos($_SERVER['REQUEST_URI'], 'under_maintenance.php'):
        header('Location: index.php'); // Redirect to index if you are on the maintenance page when maintenance mode is off
        break;
}


