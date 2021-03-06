<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description");
include_once('../Core/lux-functions.php');
include_once('../Core/output.php');
include_once('../Core/db.php');
include_once('../Core/auth.php');

$db = new Db();
$OUTPUT = new Output();
$collection = $db->selectCollection("Assets");
$AUTH = new Auth();
$LF = new LuxFunctions();


if($LF->is_avail("id")){
	$query = array("_id" => new MongoId($LF->fetch_avail("id")));
	$document = $collection->findOne($query);
	$OUTPUT->success("Found Document", $document);
}else if($LF->is_avail("query")){
	$query = is_array($LF->fetch_avail("query"))? $LF->fetch_avail("query") : array($LF->fetch_avail("query"));
	$documents = $collection->find($query);
	$OUTPUT->success("Found Documents", iterator_to_array(($documents)));
}else if($LF->is_avail("distinct")){
	$query = $LF->fetch_avail("distinct");
	$documents = $collection->distinct($query);
	$OUTPUT->success("Found Distinct Values", $documents);
}else if($LF->is_avail("aggregate")){
	$query = $LF->fetch_avail("aggregate");
	$documents = $collection->aggregateCursor($query);
	$OUTPUT->success("Found Aggregate Values", $documents);
}
/*if(!$LF->is_avail("noSub") || $LF->fetch_avail("noSub")){
	$db->subscribe($query, $AUTH);
}*/
die();

?>

