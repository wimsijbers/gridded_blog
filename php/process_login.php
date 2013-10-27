<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/db_connect.php';
	include_once $root . '/php/login_functions.php';
	sec_session_start(); // Our custom secure way of starting a php session. 
	if(isset($_POST['username'], $_POST['p'])) { 
	   $username = $_POST['username'];
	   $password = $_POST['p']; // The hashed password.
	   if(login($username, $password, $mysqli) == true) {
		  // Login success
		  echo 'Success: You have been logged in!';
		  echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../createPost.php">';  
		  // header('Location: ../createPost.php');
		  // header('Location: ../test.php');
	   } else {
		  // Login failed
		  echo 'login failed';
	   }
	} else { 
	   // The correct POST variables were not sent to this page.
	   echo 'Invalid Request';
	}
?>