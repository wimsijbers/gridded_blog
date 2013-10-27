<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';	
	include_once $root . '/php/ws.php';
	include_once $root . '/php/setup.php';
	include_once $root . '/php/tmhOAuth/tmhOAuth.php';
	include_once $root . '/php/tmhOAuth/tmhUtilities.php';
	include_once $root . '/php/wideImage/WideImage.php';
	
	sec_session_start();
	if(login_check($mysqli) == true) {
		echo getImgName('/test/large-1asdfasd.jpg');
	}
	else {
	   echo "You are not authorized to access this page, please login. ";
	}
?>