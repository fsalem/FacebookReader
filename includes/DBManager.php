<?php
/**
 * Created by PhpStorm.
 * User: farouq
 * Date: 1/19/15
 * Time: 12:33 PM
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
require_once 'InitConfiguration.php';


class DBManager
{
    var $conn;
    var $db;

    public function __construct()
    {
        // open connection to MongoDB server
        try {
            $this->conn = new Mongo('localhost');
            // access database
            $this->db = $this->conn->Trustami;
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
    }

    public function __destruct()
    {
        try {
            $this->conn->close();
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
    }

    function addUserToDB($userData)
    {
        try {
            // access collection
            $userCollection = $this->db->users;
            $userCollection->insert($userData);

        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
    }

    function addUserIdsToDB($userIds)
    {
        try {
            // access collection
            $userCollection = $this->db->userIDs;
            $userCollection->batchInsert($userIds);
            // disconnect from server
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
    }

    function addUserFriend($userFriendsArray)
    {
        try {
            $friendsCollection = $this->db->userFriends;
            $friendsCollection->insert($userFriendsArray);
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
    }

    function isUserExist($userId){
        $userCollection = $this->db->users;
        $query = array('id'=> (int)$userId);
        $count = 0;
        $cursor = $userCollection->find($query);
        foreach ($cursor as $doc) {
            $count++;
        }
        return true?$count > 0:false;
    }

    function isUserExistAsFriend($userId){
        $userCollection = $this->db->users;
        $query = array('id'=> (int)$userId, 'friends'=> array('$exists' => false));
        $count = 0;
        $cursor = $userCollection->find($query);
        foreach ($cursor as $doc) {
            $count++;
        }
        return true?$count > 0:false;
    }

    function deleteUser($userId){
        $userCollection = $this->db->users;
        $query = array('id'=> (int)$userId, 'friends'=> array('$exists' => false));
        $userCollection->remove($query);
    }

    function deleteUserID($userId,$friendId){
        $userCollection = $this->db->userIDs;
        $query = array('friendId' => $friendId,'userId'=> $userId);
        $userCollection->remove($query);
    }

    function deleteUserIDs($userId,$friendIds){
        $userCollection = $this->db->userIDs;
        $query = array('userId'=> $userId,'friendId'=> array('$in' => $friendIds));
        $result = $userCollection->remove($query);
    }

    function getUserIDsBulk($maxCount){
        $friendList = array();
        try {
            // access collection
            $userCollection = $this->db->userIDs;
            $cursor = $userCollection->find();
            $cursor->limit($maxCount);

            foreach($cursor as $result){
                $friendList[]=$result;
            }
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
        return $friendList;
    }

    function getAppUsers(){
        $appUserList = array();
        try {
            // access collection
            $appUsersCollection = $this->db->appUsers;
            $cursor = $appUsersCollection->find();

            foreach($cursor as $result){
                $appUserList[]=$result;
            }
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
        return $appUserList;
    }

    function addScopeCollection($collectionName, $scopeData){
        try {
        $scopeCollection = $this->db->$collectionName;
        $scopeCollection->insert($scopeData);
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            die('General Error: ' . $e->getMessage());
        }
    }
}