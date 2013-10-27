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
		// header('Location:createPost.php');
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=createPost.php">';  
	}
	else{
		if(isset($_GET['error'])) { 
		echo 'Error Logging In!';
		}
		echo '
		<form action="./php/process_login.php" method="post" name="login_form">
		   username: <input type="text" name="username" /><br />
		   Password: <input type="password" name="password" id="password"/><br />
		   <input type="button" value="Login" onclick="formhash(this.form, this.form.password);" />
		</form>';
	}

    include './html/wrapper_bottom.html';
    
?>