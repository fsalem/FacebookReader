<?php

require_once 'config.php';
require 'fbPHPSDK/facebook.php';

class InitConfiguration {

    var $facebook;
    var $auth;
    var $db;
    var $login;

    public function __construct() {
       // $this->connectTODB();
        //  $this->startSession();
        $this->initFacebook();
    }

    public function initFacebook() {
        $this->facebook = new Facebook(array(
                    'appId' => FB_APP_ID,
                    'secret' => FB_APP_SEC,
                ));
    }

    public function getAccessToken(){
        if (isset($this->facebook)) {
            return $this->facebook->getAccessToken();
        }
        return null;
    }

    public function setAccessToken($accessToken){
        $this->facebook->setAccessToken($accessToken);
    }

}

?>
