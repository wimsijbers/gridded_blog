<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/db_connect.php';
	// The hashed password from the form
	$password = $_POST['p'];
	$username = $_POST['username'];
	// Create a random salt
	$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
	// Create salted password (Careful not to over season)
	$password = hash('sha512', $password.$random_salt);
	// Add your insert to database script here. 
	// Make sure you use prepared statements!
	if ($insert_stmt = $mysqli->prepare("INSERT INTO ws_members (username, password, salt) VALUES (?, ?, ?)")) {    
	   if($insert_stmt->bind_param('sss', $username, $password, $random_salt) && $insert_stmt->execute()){
			echo 'registered!';
		}
	}
?>