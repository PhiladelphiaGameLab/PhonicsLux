<?php
// Authorization file that Completes OAuth based soley on creating a link from a javascript http request and placing it on the page
// Call this file and it first returns the Link, and then the redirect URI links to this page

include_once('../Core/auth.php');
//session_start();

if(isset($_GET["error"]) && $_GET["error"] == "access_denied"){
	header("location: http://".$_SERVER["HTTP_HOST"]."/");
}

if(!isset($_GET["state"])){
	session_start();
}

//if(!isset($_SESSION["redir"])){
//	$_SESSION["redir"] = $_GET["href"];
//}
//var_dump($_SESSION["redir"]);
$OAuth = new OAuthC("google");

//$OAuth->check($_SESSION["redir"]);

?>
