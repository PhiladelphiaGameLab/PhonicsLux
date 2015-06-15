<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description");
include_once('../Core/lux-functions.php');
include_once('../Core/output.php');
include_once('../Core/db.php');
include_once('../Core/auth.php');

$db = new Db();
$OUTPUT = new Output();
$collection = $db->selectCollection("APIs");
$LF = new LuxFunctions();

$service = $collection->findOne(array("service"=>$LF->fetch_avail("service")));

if(isset($service)){
	$service["key_name"];
	$base = $service["base_url"];
	$call = $LF->fetch_avail("call");	
	//$LF->fetch_avail("reqType");
	//$LF->fetch_avail("params");
	// need to process variables into either a POST or GET request
	if(substr($call, 0, 1) === "/"){
		$call = substr($call, 1);
	}
	if(substr($base, -1) === "/"){
		$base = substr($base, 0,-1);
	}
	if(strpos($call, "?") != FALSE){
		$document = $base."/".$call."&".$service["key_name"]."=".$service["key"];
		if(isset($service["postparams"])){
			$document = $document.$service["postparams"];
		}
		echo file_get_contents($document);
		//$OUTPUT->sucess("Found", 
	}else{
		$document = $base."/".$call."?".$service["key_name"]."=".$service["key"]);
		if(isset($service["postparams"])){
			 $document = $document.$service["postparams"];
		}
		echo file_get_contents($document);
	//	$OUTPUT->sucess("Found",
	}
}else{
	$OUTPUT->error("Service Could not be found");
}

		
