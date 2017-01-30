<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class mysqlDbPDO extends PDO {
           
    private
        $stmt,
        $dbLink;
    public  
        $records=array();

    public function __construct($host,$port,$dbname,$username,$password){
     	try{
     		$this->dbLink = new PDO(
                                    'mysql:host='.$host.';port='.$port.';dbname='.$dbname,
                                    $username, 
                                    $password, 
                                    array( 
                                        PDO::ATTR_PERSISTENT => false ,
                                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                                        )
                                    );
 
         register_shutdown_function( array( $this, 'shutDown' ) );
        }catch (Exception $e){
          printMsg('Failed to connect to database server');
         	var_dump($e->getMessage());
        }       
    }
    
    public function shutDown(){
        $this->dbLink=null;
        unset($this->dbLink);
        $this->stmt=null;
    }
    
    public function getData(){
        return $this->records=$this->stmt->fetch(PDO::FETCH_OBJ);
    }
    
    public function numRows($query=''){ 
        if(isset($this->stmt)){
        	return $this->stmt->rowCount();
        }else{
        	return 0;	
        } 
    }
    
    public function query($sql){
        $this->stmt=$this->dbLink->query($sql);
    }
}

class mysqlDbI {
           
    private
        $stmt,
        $dbLink;
    public  
        $records=array();

    public function __construct($host,$port,$dbname,$username,$password){
        try{
          $this->dbLink = new mysqli($host, $username, $password, $dbname,(int)$port);
          register_shutdown_function( array( $this, 'shutDown' ) );
        }catch (Exception $e){
          printMsg('Failed to connect to database server');
          var_dump($e->getMessage());
          printMsg('bb');
        }       
    }
    
    public function shutDown(){
        $this->stmt->close();
        $this->dbLink->close();
        $this->dbLink=null;
        unset($this->dbLink);
        $this->stmt=null;
    }
    
    public function getData(){
        return $this->records=$this->stmt->fetch_object();
    }
    
    public function numRows($query=''){ 
        if(isset($this->stmt)){
          return $this->stmt->num_rows;
        }else{
          return 0; 
        } 
    }
    
    public function query($sql){
        $this->stmt=$this->dbLink->query($sql);
    }
        
}

printMsg('<p>Connecting to database...</p>');
global $wpdb;

if(property_exists($wpdb, 'hyper_servers') && count($wpdb->hyper_servers['global']['write'])>0 ){
  $hyperServers=array_pop($wpdb->hyper_servers['global']['write']);
  $credentials=array_pop($hyperServers);
  $tmp=explode(':',$credentials['host']);
  $host=$tmp[0];
  $port=(!isset($tmp[1]))?'3306':$tmp[1];
  $dbname=$credentials['name'];
  $username=$credentials['user'];
  $password=$credentials['password'];

}else{
  $tmp=explode(':',DB_HOST);
  $host=$tmp[0];
  $port=(!isset($tmp[1]))?'3306':$tmp[1];
  $dbname=DB_NAME;
  $username=DB_USER;
  $password=DB_PASSWORD;
}

if(defined('PDO::ATTR_DRIVER_NAME')){
 	$db = new mysqlDbPDO($host,$port,$dbname,$username,$password);
 	$db2 = new mysqlDbPDO($host,$port,$dbname,$username,$password);
 }elseif(function_exists('mysqli_connect')){
	printMsg('mysqli available');

  $db = new mysqlDbI($host,$port,$dbname,$username,$password);
  $db2 = new mysqlDbI($host,$port,$dbname,$username,$password);
}else{
  printMsg('no possible ways to connect to mysql');
}


?>