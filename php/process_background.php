<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
    include_once $root . '/php/ws.php';
	// include_once $root . '/php/class_feed.php';
	// include_once $root . '/php/class_post.php';
	include_once $root . '/php/db_connect.php';
	include_once $root . '/php/setup.php';
	include_once $root . '/php/tmhOAuth/tmhOAuth.php';
	include_once $root . '/php/tmhOAuth/tmhUtilities.php';

	refreshTweetDB($mysqli);
?>