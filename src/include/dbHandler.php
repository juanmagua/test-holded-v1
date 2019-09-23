<?php

class dbHandler {

    private $con;
    private $col;

    function __construct() {
        require_once dirname(__FILE__) . '/dbConnect.php';
        $db = new dbConnect();
        //Connect to database
        $this->con = $db->connect();
    }

    public function getUser($username) {

        try {

            $filter = array('username' => $username);
            $options = array(
                'limit' => 1
            );
            

            $query = new MongoDB\Driver\Query($filter, $options);

            $users = $this->con->executeQuery("test.users", $query);
            
            foreach($users as $user){
                return $user;
            }

            return NULL;
        } catch (MongoDB\Driver\Exception\Exception $e) {

            return $e->getMessage();
        }
    }

    public function validatePassword($password, $password_hash) {
        
        if (password_verify($password, $password_hash )) {

            return true;
        }

        return false;
    }

    public function updateUser($user_id, $token, $token_expire) {

        try {

            $bulk = new MongoDB\Driver\BulkWrite;

            //Create array document
            $document = array(
                "token" => $token,
                "token_expire" => $token_expire,
            );

            $_id = new MongoDB\BSON\ObjectID($user_id);

            $bulk->update(['_id' => $_id], ['$set' => $document]);

            $this->con->executeBulkWrite('test.users', $bulk);

            return true;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            var_dump($e->getMessage());
            die;
            return false;
        }
    }

    //Get All 
    public function getAllWidgets() {

        try {

            $query = new MongoDB\Driver\Query([]);

            $rows = $this->con->executeQuery("test.widgets", $query);

            return $rows;
        } catch (MongoDB\Driver\Exception\Exception $e) {

            return $e->getMessage();
        }
    }

    /**
     * Insert
     * 
     */
    public function insertWidget($title, $color, $width, $height) {

        try {



            $bulk = new MongoDB\Driver\BulkWrite;

            //Create array document
            $document = array(
                "_id" => new MongoDB\BSON\ObjectID,
                "title" => $title,
                "color" => $color,
                "width" => $width,
                "height" => $height
            );

            $bulk->insert($document);

            $result = $this->con->executeBulkWrite('test.widgets', $bulk);

            return (string) $document['_id'];
        } catch (MongoDB\Driver\Exception\Exception $e) {
            return false;
        }
    }

    /**
     * Update
     */
    public function updateWidget($id, $title, $color, $width, $height) {

        try {

            $bulk = new MongoDB\Driver\BulkWrite;

            //Create array document
            $document = array(
                //"_id" => $id,
                "title" => $title,
                "color" => $color,
                "width" => $width,
                "height" => $height
            );

            $_id = new MongoDB\BSON\ObjectID($id);

            $bulk->update(['_id' => $_id], ['$set' => $document]);

            $this->con->executeBulkWrite('test.widgets', $bulk);

            return true;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            var_dump($e->getMessage());
            die;
            return false;
        }
    }

    /*
     * Delete
     */

    public function removeWidget($id) {


        try {

            $bulk = new MongoDB\Driver\BulkWrite;

            $bulk->delete(['_id' => $id]);

            $this->con->executeBulkWrite('test.widgets', $bulk);

            return REMOVE_SUCCESS;
        } catch (MongoDB\Driver\Exception\Exception $e) {
            return REMOVE_FAILED;
        }
    }

}

?>
