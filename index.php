<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once 'includes/UserManager.php';

function prettyPrint($obj) {
    echo '<pre>';
    print_r($obj);
    echo '</pre>';
}
$userManager = new UserManager();
$userManager->checkUserLogin();
$userManager->addUserInfo();
echo 'Thanks a lot. Your data is collected.';
?>

