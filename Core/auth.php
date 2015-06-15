<?php
include_once("lux-functions.php");
include_once("db.php");
include_once("output.php");

class Auth{

	private $client_doc;

	function __construct(){
		// test to make sure that access code is legit
		$LuxFunctions = new LuxFunctions();
		$OUTPUT = new Output(); 
		$access_token = $LuxFunctions->fetch_avail("access_token");
		$DB = new db();
		$clientInfo = $DB->selectCollection("Users");
		$this->client_doc = $clientInfo->findOne(array("access_token" => $access_token));
		if(!isset($this->client_doc)){
			$OUTPUT->error("Access Code is invalid or has Expired");
		}
	}
	
	function getClientId(){
		return $this->client_doc["_id"];
	}
	
	function getClientInfo(){
		return $this->client_doc;
	}
	
	function getClientGroups(){
		return $this->client_doc["groups"];
	}
	
	function getClientEmail(){
		return $this->client_doc["email"];
	}

	function getClientName(){
		return $this->client_doc["name"];
	}
	
	function getClientAdmin(){
		return $this->client_doc["admin"];
	}
}

class AuthLogin{

	private $client_doc;
	
	function __construct($identifier){

		$OUTPUT = new Output();
		$DB = new db();
		$clientInfo = $DB->selectCollection("ClientInfo");
		
		// find user by the identifier
		// if the user is found
		// return the users current access token if one exists
		// otherwise, create a new access token and return that
		// do other stuff
	}


}

class OAuthC{

	private $client_id;
	private $client_secret;
	private $redirect_url;
	private $grant_type;
	private $acc_token;
	private $refresh;
	private $DB;
	private $clientInfo;
	private $OUTPUT;
	private $service;

	function __construct($serv){
		$LF = new LuxFunctions();
		$this->OUTPUT = new Output();
                $this->DB = new db();
		if(isset($_GET["state"])){
	                $LF->setArray();
			if($_GET["state"] != session_id()){
			    session_destroy();
                	    session_id($_GET["state"]);
        	            session_start();
	                    $LF->setArray();
			}
		}
		$this->clientInfo = $this->DB->selectCollection("Users");
		$this->service = strtolower($serv);
		if($LF->is_avail("href")){
			$_SESSION["href"] = $LF->fetch_avail("href");
			$this->check($LF->fetch_avail("href"));
		}else{
			$this->check($LF->fetch_avail("href"));
		}
		if($LF->is_avail("host")){
			$_SESSION["host"] = $LF->fetch_avail("host");
		}else{
			
		}
	}

	function check($info){
		$this->redirect_url = $info;
		if($this->service == "custom"){
			$LF = new LuxFunctions();
			$this->username = $LF->fetch_avail("username");
			$this->password = $LF->fetch_avail("password");
			$this->findUser($this->username, $this->password);
			
			return;
		}
		if(!isset($_GET['code'])){
                        echo $this->getURL();
                }else{
                        $this->saveToDb();
                }
	}
	function findUser($user, $pass){
		// hash the password
		$passHash = md5(trim($pass) + " lux is freaking awesome!!!");
		// hash an access token
		$p = new OAuthProvider();
		$t = bin2hex($p->generateToken(32));
		// query for username && hashed password (find one)
		// update the username && hashed password with the new access token
		$output = array();
		try{
			$retval = $this->clientInfo->findAndModify(
			     array("user" => $user, "passHash" => $passHash),
			     array('$set' => array("access_token"=>$t)),
			     null,
			     array(
				"new" => true
			    )
			);
		}catch (MongoException $e) {
			echo json_encode($e);
			$output["error"] = "A Mongo Error Occured";
			echo json_encode($output);
			die();
		}
		// output either an error if no document is found or return the access_token to the user 
		if(isset($retval["access_token"])){
			$output["access_token"] = $retval["access_token"];
		}else{
			$output["error"] = "Incorrect Username or Password";
		}
		echo json_encode($output);
	}
	function getURL(){
		$LF = new LuxFunctions();
                switch($this->service){
                        case "google":
                                $state = session_id();
                                $cli_id = '1006161612314-1qct7m1r0bqt5ecb2sntrci253dv41s1.apps.googleusercontent.com';
                                $call = 'http://'. $_SERVER['HTTP_HOST'] .'/Lux/Auth/google.php';
				if($LF->is_avail("scope") && $LF->fetch_avail("scope") == "basic"){
					$scope = 'email%20profile';
					$url = "https://accounts.google.com/o/oauth2/auth?state=$state&scope=$scope&redirect_uri=$call&response_type=code&client_id=$cli_id";
				}else{
					$scope = 'email%20profile%20https://www.googleapis.com/auth/admin.directory.user';
					$url = "https://accounts.google.com/o/oauth2/auth?state=$state&scope=$scope&redirect_uri=$call&response_type=code&client_id=$cli_id&approval_prompt=force&access_type=offline";
                                }
                                //$this->redirect_url = $_GET["href"];
                                
				return $url;
                	case "facebook":
				
		}
        }


	function saveToDb(){
		$LF = new LuxFunctions();
		$this->OUTPUT = new Output();
		$code = $_GET['code'];
        	$url = 'https://accounts.google.com/o/oauth2/token';
		$params = array(
			"code" => $code,
			"client_id" => "1006161612314-1qct7m1r0bqt5ecb2sntrci253dv41s1.apps.googleusercontent.com",
			"client_secret" => "Uka8meQZbY0KMFCnQ6nYb0Tw",
			"redirect_uri" => "http://".$_SERVER["HTTP_HOST"]."/Lux/Auth/google.php",
			"grant_type" => "authorization_code"
		);
		//var_dump($params);
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		$json_response = curl_exec($curl);
		$authObj = json_decode($json_response);
		$access_tok = $authObj->{'access_token'};
        	$getUrl = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=".$access_tok;
                $getResponse = file_get_contents($getUrl);
                $get = json_decode($getResponse, true);
		//var_dump($get);

	//	$prevCheck = $this->clientInfo->findOne(array("id" => $get["id"]));

		$get["access_token"] = $access_tok;
		$results = $this->clientInfo->update(array("id" => $get["id"]), array('$set' => $get), array("upsert" => true));
	/*	if(!isset($prevCheck)){
			$get["access_token"] = $this->acc_token;
			$this->clientInfo->insert($get);
		}else{

		} */
		echo var_dump($results);
		echo "<br/><br/>redirect url: ".$this->redirect_url;
		echo "<br/><br/>Session redirect url: ".$_SESSION["href"];
		echo "<br/><br/>current url: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		//echo "<br/><br/>Location: http://". $_SERVER['HTTP_HOST']. "/" .$this->redirect_url."?access_token=".$access_tok;
		//header("Location: http://". $_SERVER['HTTP_HOST']. "/" .$this->redirect_url."?access_token=".$access_tok);

		if($LF->is_avail("host") && strpos($LF->fetch_avail("host"), "://") === false){
        	    header("Location: http://".$LF->fetch_avail("host")."/" .$this->redirect_url."?access_token=".$access_tok);
		}else if($LF->is_avail("host")){
        	    header("Location: ".$LF->fetch_avail("host")."/" .$this->redirect_url."?access_token=".$access_tok);
		}else{
		    header("Location: http://".$_SERVER["HTTP_HOST"]."/".$this->redirect_url."?access_token=".$access_tok);
		}
		die();
	}

}

?>
