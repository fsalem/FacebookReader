<?php
/**
 * Created by PhpStorm.
 * User: farouq
 * Date: 1/19/15
 * Time: 12:32 PM
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require_once 'InitConfiguration.php';
require_once 'DBManager.php';


class FBUtility {

    var $facebook;
    var $DBManager;

    public function __construct($facebook,$DBManager) {
        $this->facebook = $facebook;
        $this->DBManager = $DBManager;
    }

    function getBirthDate($userId){
        $url ='/'.$userId.'?fields=birthday';
        $graphObject = $this->facebook->api($url);
        $columnName='birthday';
        return isset($graphObject[$columnName])?$graphObject[$columnName]:null;
    }

    function getName($userId){
        $url ='/'.$userId.'?fields=first_name,last_name';
        $graphObject = $this->facebook->api($url);
        return $graphObject['first_name'].' '.$graphObject['last_name'];
    }

    function getCount($userId,$scope,$collectionName,$friendId,$friendName,$userName){
        $url ='/'.$userId.$scope;
        $scopeData = array();
        $scopeDataArray = array();
        $scopeData['userId']=$userId;
        if ($collectionName == 'mutualfriends'){
            $url = $url.'/'.$friendId;
            $scopeData['userName']=$userName;
            $scopeData['friendId']=$friendId;
            $scopeData['friendName']=$friendName;
        }
        $graphObject = $this->facebook->api($url);
        //prettyPrint($graphObject);
        $columnName='data';
        if (!isset($graphObject['data'])){
            if (isset($graphObject['location'])){
                $columnName = 'location';
            }elseif(isset($graphObject['education'])){
                $columnName = 'education';
            }elseif(isset($graphObject['work'])){
                $columnName = 'work';
            }
        }
        $count  = 0;
        if (isset($graphObject[$columnName])) {
            $count = count($graphObject[$columnName]);
            $scopeDataArray=$graphObject[$columnName];
            while (isset($graphObject['paging']) && isset($graphObject['paging']['next'])) {
                $url = substr($graphObject['paging']['next'], strlen("https://graph.facebook.com/v1.0/"));
                $graphObject = $this->facebook->api($url);
                $count += count($graphObject[$columnName]);
                $scopeDataArray=array_merge($scopeDataArray,$graphObject[$columnName]);
            }
        }
        $scopeData['data']=$scopeDataArray;
        $this->DBManager->addScopeCollection($collectionName,$scopeData);
        return $count;
    }

    function getFriendList(){
        $friendList = array();
        $url = '/me/friends';
        while(true) {
            $graphObject = $this->facebook->api($url);
            foreach ($graphObject['data'] as $friend) {
                $friendList[] = array('id'=>$friend['id'],'name'=>$friend['name']);
            }
            if (isset($graphObject['paging']) && isset($graphObject['paging']['next'])){
                $url = substr($graphObject['paging']['next'],strlen("https://graph.facebook.com/v1.0/"));
            }else{
                break;
            }
        }
        return $friendList;
    }

}