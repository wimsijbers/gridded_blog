<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/lib_autolink.php';
	include_once $root . '/php/setup.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';
	include_once $root . '/php/ws.php';
	include_once $root . '/php/markdown/Markdown.php';
	sec_session_start();
	if(login_check($mysqli) == true) {
	include './html/wrapper_top.html';
	
	if(isset($_GET['edit'])){
		$post = new Post();
		$postid = filter_var($_GET['edit'], FILTER_VALIDATE_INT);
		$post->getPostFromDB($mysqli, $postid);
		$_SESSION['edit'] = $postid;
		$_SESSION['post'] = serialize($post);
	}
	elseif(isset($_SESSION['post'])){
		$post = unserialize($_SESSION['post']);
	}
	else{
		$post = new Post();
	}
	// var_dump($post);
	$msg = (isset($_SESSION['msg']) 	? $_SESSION['msg'] : EMPTYMSG);
?>
	<link rel="stylesheet" href="./css/style-private.css" type="text/css" />
	<div id="privatepage">
	
	<?php include_once $root . '/html/headerPrivatePage.html';?>
	
	<div class="boxshadow-light color-bg-vlight margin20padding20">
	<?php
		echo $msg;
		$_SESSION['msg'] = EMPTYMSG;
	?>
	</div>
		<div id="creation" class="boxshadow-light color-bg-vlight margin20padding20">
			<h2>Post</h2>
			
			<ul>
				<form action="./php/process_createPost.php" method="post">
				<li>
					<label for="format">Format</label>
					<select name="format">
						<option <?php echo ($post->getFormat() == 'text' ? 'selected': ''); ?> value="text">text</option>
						<option <?php echo ($post->getFormat() == 'vimeo' ? 'selected': ''); ?> value="vimeo">vimeo</option>
						<option <?php echo ($post->getFormat() == 'photo' ? 'selected': ''); ?> value="photo">photo</option>
					</select>
					<input type="submit" value="set format" name="setFormat"/>
				</li>
				</form>
				<form action="./php/process_createPost.php" method="post">
				<li>
					<label for="title">Title</label>
					<input type="text" name="title" value="<?php echo $post->getTitle()?>"/>
				</li>
				<li>
					<label for="size">Rows</label>
					<input type="number" min="1" max="2" name="rows" value="<?php echo htmlsan($post->getRows())?>"/>
				</li>
				<li>
					<label for="size">Columns</label>
					<input type="number" min="1" max="2" name="columns" value="<?php echo htmlsan($post->getColumns())?>"/>
				</li>	
				<li>
					<label for="contentType">Content</label>
					<select name="contentType">
						<option <?php echo ($post->getContentType() == 'general' ? 'selected': ''); ?> value="general">general</option>
						<option <?php echo ($post->getContentType() == 'code' ? 'selected': ''); ?> value="code">code</option>
						<option <?php echo ($post->getContentType() == 'climbs' ? 'selected': ''); ?> value="climbs">climbs</option>
						<option <?php echo ($post->getContentType() == 'hacks' ? 'selected': ''); ?> value="hacks">hacks</option>
					</select>
				</li>
				<li>
					<label for="islink">Link</label>
					<input type="checkbox" name="islink" value="islink" <?php echo ($post->getIslink() == true ? 'checked': ''); ?>/>
				</li>
			</ul>
			<h2>Content</h2>
						<ul>
			<?php
			switch($post->getFormat()){
				case 'text':
					echo '
							<li>
								<label for="floatTitle">floating title</label>
								<input type="checkbox" name="floatTitle" value="floatTitle" ', ($post->getContentNode("floatTitle") == true ? "checked": "") ,'/>
							</li>
							<li>
								<textarea rows="10" name="text">', $post->getContentNode("text") ,'</textarea>
							</li>
							<li>
								<textarea rows="10" name="moretext">', $post->getContentNode("moretext") ,'</textarea>
							</li>
						';
					break;
				case 'vimeo':
					echo '
							<li>
								<label for="vimeonumber">Vimeo video number</label>
								<input type="text" name="vimeoid" value="',$post->getContentNode("vimeoid"),'"/>
							</li>
					';
				break;
				case 'photo':
					echo '
							<li>
								<label for="photos">Photos</label>
								<input type="text" name="photos" value="',$post->getContentNode("photos"),'"/>
							</li>
					';	
				break;
			}
			?>
							<li>
								<input type="submit" value="save" name="save"/>
							</li>
						</ul>
			</form>
		</div>
		
		<?php echo $post->getFeedHTML();?>
		<div id="manager" class="boxshadow-light color-bg-vlight margin20padding20">
		<h2>Options</h2>
		<?php 
			if(isset($_SESSION['edit']) && $_SESSION['edit'] != 0){
				$value = $_SESSION['edit'];
				$buttonname = 'edit';
			}
			else{
				$value = 'publish';
				$buttonname = 'publish';
			}
			echo '<form action="./php/process_createPost.php" method="post"><button type="submit" value="',$value,'" name="publish" ' . ($post->checkValidity() ? "" : "disabled") .'>',$buttonname,'</button></form>';
		?>
		</div>
	</div>
<?php
	include './html/wrapper_bottom.html';
	} 
	else {
	   echo 'You are not authorized to access this page, please login. ';
	}
	
	
?>