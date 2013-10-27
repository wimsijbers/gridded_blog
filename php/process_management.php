<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/setup.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';	
	sec_session_start();
	if(login_check($mysqli) == true) {
	$msg = (isset($_SESSION['msg']) 	? $_SESSION['msg'] : EMPTYMSG); 
	if(!empty($_POST['show'])){
		$stmt = $mysqli->prepare("UPDATE ws_posts SET post_status = 'show' WHERE post_id = ?");
		// echo $mysqli->error;
		$stmt->bind_param('i', $_POST['show']);
		$stmt->execute();
		$msg = $_POST['show'].' is now shown.';
	}
	if(!empty($_POST['hide'])){
		$stmt = $mysqli->prepare("UPDATE ws_posts SET post_status = 'hide' WHERE post_id = ?");
		$stmt->bind_param('i', $_POST['hide']);
		$stmt->execute();
		$msg = $_POST['hide'].' is now hidden.';
	}
	if(!empty($_POST['islink'])){
		$stmt = $mysqli->prepare("UPDATE ws_posts SET post_islink = 1-post_islink WHERE post_id = ?");
		$stmt->bind_param('i', $_POST['islink']);
		$stmt->execute();
		$msg = $_POST['islink'].' changed link status.';
	}
	$_SESSION['msg'] = $msg;
	//header('Location:../Management.php');
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../Management.php">'; 
	}
	else {
	   echo "You are not authorized to access this page, please login. ";
   }
?>