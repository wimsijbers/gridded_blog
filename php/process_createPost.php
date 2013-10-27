<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/setup.php';
	include_once $root . '/php/ws.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';	
	include_once $root . '/php/wideImage/WideImage.php';
	sec_session_start();
	if(login_check($mysqli) == true) {
	set_time_limit(600);
	$post = (isset($_SESSION['post']) 	? unserialize($_SESSION['post']) : new Post());
	$msg = (isset($_SESSION['msg']) 	? $_SESSION['msg'] : EMPTYMSG); 
		if(!empty($_POST['save'])){
			$title 		=(isset($_POST['title']) 								? $_POST['title'] 	: "");
			$rows 		=(isset($_POST['rows']) 	&& $_POST['rows'] 	!= ""	? $_POST['rows'] 	: 1); 
			$columns 	=(isset($_POST['columns']) 	&& $_POST['columns']!= ""	? $_POST['columns'] : 1);
			$contentType=(isset($_POST['contentType']) 							? $_POST['contentType'] : "general");
			$islink		=(isset($_POST['islink'])								? true	: false);
			$post->setTitle(htmlsan($title));
			$post->setContentType($contentType);
			$post->setSize($rows, $columns);
			switch($post->getFormat()){
				case 'text':
					$floatTitle = (isset($_POST['floatTitle']) ? true : false);
					$post->createTextContent(htmlsan($_POST['text']), htmlsan($_POST['moretext']), $floatTitle);
					break;
				case 'vimeo':
					$vimeoid = (isset($_POST['vimeoid']) ? $_POST['vimeoid'] : '');
					$post->createContentFromArray(array('vimeoid' => $vimeoid));
					break;
				case 'photo':
					$photos = (isset($_POST['photos']) ? $_POST['photos'] : '');
					$post->createContentFromArray(array('photos' => $photos));
					$photos = preg_split('/\s+/', $photos);
					$msg = '';
					foreach($photos as $photo){
						$width = $post->getColumns() * BASEWIDTH + ($post->getColumns()-1) * SPACING;
						$height = $post->getRows() * BASEHEIGHT + ($post->getRows()-1) * SPACING;
						if(file_exists($root . '/img/upload/' . $photo) && !empty($photo)){
							formatImage($root . '/img/upload/' . $photo, $root . '/img/formatted/', $width, $height);
						} else{
							$msg = $msg . ' Photo "' . $photo . '" does not exists.';
						}
						
					}
					break;
			}
			$post->setDate(new DateTime('NOW'));
			$post->setIslink($islink);
			$_SESSION['msg'] = $msg . "post updated.";
		}
		if(!empty($_POST['setFormat'])){
			$format =(isset($_POST['format']) ? $_POST['format'] : "text");
			$post->setFormat($format);
			$_SESSION['msg'] = "Post format set!";
		}
		if(!empty($_POST['publish'])){
			if(isset($_SESSION['edit'])  && $_SESSION['edit'] != 0){
				if($post->edit($mysqli, $_SESSION['edit'])){
					$post = new Post();
					$_SESSION['edit'] = 0;
					$_SESSION['msg'] = 'Post edited!';
				}
				else{
					$_SESSION['msg'] = 'Could not edit...';
				}
			}
			elseif($post->publish($mysqli)){
				$_SESSION['msg'] =  'Post published!';
				$post = new Post();
			}
			else{
				$_SESSION['msg'] = 'Could not publish...';
			}
		}
		
		
	$_SESSION['post'] = serialize($post);
	//header('Location:../createPost.php');
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../createPost.php">';  
	}
	else {
	   echo "You are not authorized to access this page, please login. ";
	}
?>