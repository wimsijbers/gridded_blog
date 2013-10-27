<?php
	define("HOST", "localhost"); // The host you want to connect to.
	define("USER", "dbuser"); // The database username.
	define("PASSWORD", "password"); // The database password. 
	define("DATABASE", "mysqldb"); // The database name.
	$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
?>