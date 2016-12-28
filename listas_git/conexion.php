<?php
  class Connection {
    protected $db;
    public function Connection(){
	    $conn = NULL;
	        try{
	            $servername = "localhost";
	            $username = "secure_login"; // Este usuario tiene grant para hacer select, insert y updates en la BD de iseflistas. No es superusuario.
	            $password = "?Breo8)F";
	            $dbname = "iseflistas";

		    if (!defined('PDO::ATTR_DRIVER_NAME')) {
                        echo 'PDO unavailable';
                    }
	
	            $conn = new PDO(
                                      "mysql:host=$servername;dbname=$dbname", 
                                      $username, 
                                      $password,
                                      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                                   );
        	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	            } catch(PDOException $e){
	                echo 'ERROR: ' . $e->getMessage();
	                }    
	            $this->db = $conn;
	    }    
	    public function getConnection(){
	        return $this->db;
	    }
            public function closeConnection() {
                $conn=null;
            }
  }
?>
