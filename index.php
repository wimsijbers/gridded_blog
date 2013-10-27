<?php
    //include 'Mobile_Detect.php';
    //$detect = new Mobile_Detect;
    //$ismobile = $detect->isMobile();
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/lib_autolink.php';
    include_once $root . '/php/ws.php';
	include_once $root . '/php/class_feed.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/db_connect.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/setup.php';
	include_once $root . '/php/tmhOAuth/tmhOAuth.php';
	include_once $root . '/php/tmhOAuth/tmhUtilities.php';
	include_once $root . '/php/markdown/Markdown.php';
	sec_session_start();
	include_once $root . '/html/wrapper_top.html';
	//include_once $root . '/php/google.php';
	// if(login_check($mysqli) == true){
		// include_once $root . '/php/randomBgImg.php';
		echo '<div id="wrapper">';
		include_once $root . '/html/header.html';
		$postid = htmlsan(isset($_GET['postid']) 	? $_GET['postid'] : '');
		$before = htmlsan(isset($_GET['before'])		? $_GET['before'] : '');
		if($postid == ''){
			echo '<div id="content" class="feed">';
			include $root . '/php/process_morePosts.php';
			echo '</div>';
		}
		else{
			echo '<div id="content" class="page">';
			$postid = filter_var($postid, FILTER_VALIDATE_INT);
			$post = new Post();
			$post->getPostFromDB($mysqli, $postid);
			echo $post->getPageHTML();
		}
		echo '</div>';

		include_once $root . '/html/wrapper_bottom.html';  
	// }
	// else{
		// echo '<p>The site is temporarely offline... check back soon !</p>';
	// }
?>