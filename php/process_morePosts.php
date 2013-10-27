<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/lib_autolink.php';
    include_once $root . '/php/ws.php';
	include_once $root . '/php/class_feed.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/db_connect.php';
	include_once $root . '/php/setup.php';
	include_once $root . '/php/tmhOAuth/tmhOAuth.php';
	include_once $root . '/php/tmhOAuth/tmhUtilities.php';
	include_once $root . '/php/markdown/Markdown.php';
	
	// if(isset($_GET['before'])){
		// $before = htmlsan($_GET['before']);
	// }
	// else
	if(!isset($before)){
		if(isset($_POST['before'])){
			$before = $_POST['before'];
		}
		else{
			$before = '';
		}
	}
	$feed = new Feed();
	$feed->setup($postsperpage, $tweetperc);
	if($before != ''){
		$before = DateTime::createfromformat('Ymd-His',$before)->format('Y-m-d H:i:s');
	}
	$feed->setPosts($feed->getMorePosts($mysqli, $before));
	echo $feed->getHTML();
	// if($feed->getHTML() == ''){
		// // echo '<p>nothing to see here! (probably reached the end of the line?)</p>';
	// }
	$before = $feed->getOldestPostDate();
	if($before != '' && $before>$agelimit){
		$before = $before->format("Ymd-His");
	}
	else{
		$before = '';
	}
	include_once $root . '/html/footer.html';
	
?>