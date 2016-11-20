<?php
header('Access-Control-Allow-Origin: *');  
header('Content-Type: application/json');
/*
Project: GrassRoutes
Built for AccHack16
Copyright 2016 Hugh Wells (http://crablab.co.uk)
*/
class sitetools{

	protected $db;

	public function connect(){
        $server = 'localhost';
        $dbName = 'trees';
        $dsn = "mysql:host=".$server.";dbname=".$dbName;
        $user = 'root';
        $pass = 'acchack16';
        //set the db property as the PDO object
        $this->db = new PDO($dsn, $user, $pass);

        return $this->db;
    }

    public function http_error($code, $message){
    	http_response_code($code);
		echo json_encode(['status'=>['code' => $code, 'message' => $message]]);
		exit;
    }

}

?>