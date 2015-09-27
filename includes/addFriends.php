<?php
/**
 * Created by PhpStorm.
 * User: farouq
 * Date: 1/19/15
 * Time: 8:13 PM
 */
require_once 'UserManager.php';

if (isset($argv) && isset($argv[1])){
    $userManager = new UserManager();
    $userManager->addFriendBulk($argv[1]);
}
?>