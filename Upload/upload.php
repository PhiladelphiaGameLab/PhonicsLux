<?php
include_once('../Core/lux-functions.php');
include_once('../Core/output.php');
include_once('../Core/db.php');
include_once('../Core/auth.php');

$OUTPUT = new Output();
$AUTH = new Auth();
$LF = new LuxFunctions();

// directory that files are going into
$target_dir = "/var/www/html/uploads/";

/* 
var_dump($_FILES);
echo "Lux Functions Array: ";
var_dump($LF->getArray());
*/
$file = $LF->fetch_avail("file");
$name = $LF->fetch_avail("name");
$target_file = $target_dir.$name;

if (move_uploaded_file($file["tmp_name"], $target_file)) {
	$fileContent = file_get_contents($target_file);
	$dataUrl = 'data:' . $file["type"] . ';base64,' . base64_encode($fileContent);

	$json = json_encode(array(
	  'name' => $name,
	  'type' => $file["type"],
	  'size' => $file["size"],
	  'dataUrl' => $dataUrl,
	));

	$OUTPUT->success(0, $json, null);

} else {
	$OUTPUT->error(2, "File Upload was unsucessful for unknown reasons");
}
	

?>


