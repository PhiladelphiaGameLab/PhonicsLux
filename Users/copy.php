<?php
include_once('../core/copy.php');
copyDocument(
        array(
                "collectionName" => "Users"
                ,"updates" => false
                ,"pubsub" => false
		,"enqueue" => false
                ,"priority" => "Low"
        )
);
?>

