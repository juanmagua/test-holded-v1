<?php
class dbConnect {
  private $con;
  
  function __construct() {}

  function connect() {
    include_once dirname(__FILE__) . '/config.php';

    try {
      //connect to Mongo with default setting
      $this->con = new MongoDB\Driver\Manager("mongodb://localhost:27017");
	  
	     }
    catch (MongoConnectionException $e) {
      echo "Cannot Connect to MongoDB";
    }

    return $this->con;

  }
}
?>
