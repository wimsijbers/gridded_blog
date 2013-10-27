<?php
    //include 'Mobile_Detect.php';
    //$detect = new Mobile_Detect;
    //$ismobile = $detect->isMobile();
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/lib_autolink.php';
    include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';
	sec_session_start();
	include_once $root . '/html/wrapper_top.html';
	if(login_check($mysqli) == true){
		echo '
		<form action="./php/process_register.php" method="post" name="register_form">
		   username: <input type="text" name="username" /><br />
		   Password: <input type="password" name="p"/><br/>
		   <input type="button" value="Register" onclick="formhash(this.form, this.form.p);"/>
		</form>';
	}
	else{
		echo 'Need to be logged in!';
	}
    include $root . '/html/wrapper_bottom.html';
?>