<?php
/**
 * Created by PhpStorm.
 * User: farouq
 * Date: 1/19/15
 * Time: 8:13 PM
 */
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require_once 'UserManager.php';

$userManager = new UserManager();
$userManager->reCollectAppUsersData();

?>