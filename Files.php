<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/setup.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';
	include_once $root . '/php/ws.php';
	sec_session_start();
	if(login_check($mysqli) == true) {
	include './html/wrapper_top.html';
	
	if(isset($_POST[''])){
	}
	else{
	}
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
		<form action="./php/process_files.php" method="post" enctype="multipart/form-data">
		<div class="boxshadow-light color-bg-vlight margin20padding20">
		<ul>
			<li>
				<input id="fileupload" type="file" name="files[]" multiple="multiple" />
			</li>
			<li>
				<input type="submit" name="submit" value="upload" />
			</li>
		</ul>
		</div>
		</form>
		<div class="boxshadow-light color-bg-vlight margin20padding20">
		<form action="./php/process_files.php" method="post">
			<table>
			<tr class="color-bg-dark color-vlight">
				<td>date</td>
				<td>filename</td>
				<td>size</td>
				<td>formatted</td>
				<td></td>
			</tr>
			<?php 
				$exts = '';
				foreach($allowedExts as $ext){
					$exts = $ext . ',' . $exts;
				}
				$exts = substr_replace($exts ,"",-1);
				// $files = glob('./img/formatted/' . 'orig-*.{' . $exts . '}', GLOB_BRACE);
				$files = glob($root . '/img/upload/' . '*.{' . $exts . '}', GLOB_BRACE);
				foreach($files as $file){
					echo '<tr>';
						echo '<td>' . date('Y-m-d H:i:s' ,filemtime($file)) . '</td>';
						echo '<td>' . basename($file) . '</td>';
						$mbsize = round((filesize($file) / 1048576), 2);
						echo '<td>' . $mbsize . 'MB</td>';
						echo '<td>' . (checkFormatted(basename($file)) ? 'yes' : 'no') . '</td>';
						echo '<td><input type="checkbox" value="',$file,'" name="check[]"></input></td>';
					echo '</tr>';
				}
			?>
			</table>
		<ul>
			<li>with selected: <input type="submit" name="print" value="print" /></li>
		</ul>
		</form>
		</div>
	</div>
<?php
	include './html/wrapper_bottom.html';
	} 
	else {
	   echo 'You are not authorized to access this page, please login. ';
	}
	
	
?>