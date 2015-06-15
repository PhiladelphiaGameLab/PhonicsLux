<?php

include_once('../Core/lux-functions.php');
include_once('../Core/output.php');
include_once('../Core/db.php');
include_once('../Core/auth.php');

$db = new Db();
$OUTPUT = new Output();
$collection = $db->selectCollection("Scoreboard");
$LF = new LuxFunctions();
$AUTH = new Auth();

$results = $collection->findOne(
    array(
	"user_id"=> $AUTH->getClientId()
	//"user_id"=>"1234"
	)
);
if($results===null){
	$collection->insert(
		array(
			"user_id"=> $AUTH->getClientId(),
			"lessons"=>array(),
			"cards"=>array(),
			"name"=> $AUTH->getClientName(),
			"type"=>"student",
			"time_created"=>$LF->fetch_avail("last_time_modified"),
			"last_time_modified"=>$LF->fetch_avail("last_time_modified")
			)
);
$results = $collection->findOne(
    array(
        "user_id"=> $AUTH->getClientId()
        //"user_id"=>"1234"
        )
);

$OUTPUT->success("new user profile created", $results);
}
else{
$OUTPUT->success("found user profile", $results);}
?>
