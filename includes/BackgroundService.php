<?php
/**
 * Created by PhpStorm.
 * User: farouq
 * Date: 1/20/15
 * Time: 1:01 AM
 */
require_once 'UserManager.php';

ignore_user_abort(true);//if caller closes the connection (if initiating with cURL from another PHP, this allows you to end the calling PHP script without ending this one)
set_time_limit(0);


$hLock=fopen(__FILE__.".lock", "w+");
if(!flock($hLock, LOCK_EX | LOCK_NB))
    die("Already running. Exiting...");

$maxBulk = 15;
while(true)
{
    $userManager = new UserManager();
    $userManager->addFriendBulk($maxBulk);
    usleep(1000000);
}

flock($hLock, LOCK_UN);
fclose($hLock);
unlink(__FILE__.".lock");