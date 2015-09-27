<?php
/**
 * Created by PhpStorm.
 * User: farouq
 * Date: 1/19/15
 * Time: 7:33 PM
 */
require_once 'InitConfiguration.php';
require_once 'FBUtility.php';
require_once 'DBManager.php';

class UserManager
{
    var $conf;
    var $user;
    var $utility;
    var $DBManager;

    function __construct()
    {
        $this->conf = new InitConfiguration();
        $this->user = $this->conf->facebook->getUser();
        $this->DBManager = new DBManager();
        $this->utility = new FBUtility($this->conf->facebook,$this->DBManager);

    }

    function checkUserLogin(){
        if (!$this->user) {
        $params = array(
            'scope' => FB_SCOPES,
            'redirect_uri' => REDIRECT_URL
        );

            $statusUrl = $this->conf->facebook->getLoginStatusUrl();
            $loginUrl = $this->conf->facebook->getLoginUrl($params);
            header("Location: $loginUrl");
        }
    }

    function getUserData()
    {
        $userScopes = unserialize(COUNT_SCOPES);
        $userData = array();
        $userData['id'] = (int)$this->user;
        $userData['name']=$this->utility->getName($this->user);
        $userData['birthday'] = $this->utility->getBirthDate($this->user);
        foreach ($userScopes as $curScope) {
            $collectionName='';
            if ($curScope[0] == '/') {
                $collectionName = substr($curScope, 1);
            }
            if ($curScope[0] == '?') {
                $collectionName =substr($curScope, strlen('?fields='));

            }
            $count = $this->utility->getCount($this->user, $curScope, $collectionName,-1,null,null);
            $userData[$collectionName] = $count;
        }
        $userData['isFake']=false;
        $userData['trainingSet']=array();
        return $userData;
    }

    function collectUserData(){
        $userData = $this->getUserData();
        $userData['appUser']=true;
        $userData['accessToken'] = $this->conf->getAccessToken();
        $friendList = $this->utility->getFriendList();
        $userData['friends']=count($friendList);
        $userData['friendsList']=$friendList;
        $this->DBManager->addUserToDB($userData);
        for ($i=0 ; $i<count($friendList) ; $i++){
            $friendList[$i] = array('userId' => $this->user,'userName' =>$userData['name'] , 'friendId'=> $friendList[$i]['id'],'friendName' =>$friendList[$i]['name'],'atoken' => $this->conf->getAccessToken());
        }
        $this->DBManager->addUserIdsToDB($friendList);
    }

    function addUserInfo(){
        if ($this->DBManager->isUserExist($this->user)){
            if ($this->DBManager->isUserExistAsFriend($this->user)){
                $this->DBManager->deleteUser($this->user);
                $this->collectUserData();
            }
        }else{
            $this->collectUserData();
        }
    }

    function addFriendBulk($count){
        $friendList = $this->DBManager->getUserIDsBulk($count);
        /*
        $friendIds = array();
        foreach ($friendList as $friend){
            $friendIds[]=$friend['friendId'];
        }
        $this->DBManager->deleteUserIDs($friend['userId'],$friendIds);
        */
        foreach ($friendList as $friend){
            if (!$this->DBManager->isUserExist($friend['friendId'])) {
                $this->conf->setAccessToken($friend['atoken']);
                $this->user = $friend['friendId'];
                $this->utility = new FBUtility($this->conf->facebook,$this->DBManager);
                try {
                    $userData = $this->getUserData();

                    $userData['appUser'] = false;
                    $this->DBManager->addUserToDB($userData);
                    $this->utility->getCount($friend['userId'], '/mutualfriends', 'mutualfriends', $friend['friendId'], $friend['friendName'], $friend['userName']);
                    $this->DBManager->deleteUserID($friend['userId'], $friend['friendId']);
                }catch (FacebookApiException $exp){
                    if (strpos($exp->getMessage(), 'Unsupported get request') !== false){
                        $this->DBManager->deleteUserID($friend['userId'],$friend['friendId']);
                    }else{
                        throw $exp;
                    }
                }
            }
            else{
                $this->DBManager->deleteUserID($friend['userId'],$friend['friendId']);
            }
        }
    }

    function reCollectAppUsersData(){
        $appUserList = $this->DBManager->getAppUsers();
        foreach ($appUserList as $appUser){
            if (!$this->DBManager->isUserExist($appUser['userId'])) {
                $this->conf->setAccessToken($appUser['atoken']);
                $this->user = $appUser['userId'];
                $this->utility = new FBUtility($this->conf->facebook,$this->DBManager);
                $this->collectUserData();

            }
        }
    }
}