<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/setup.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';	
	include_once $root . '/php/ws.php';
	include_once $root . '/php/wideImage/WideImage.php';
	sec_session_start();
	if(login_check($mysqli) == true) {
	// echo "<pre>".print_r($_FILES,true)."</pre>";
	echo 'processing... please wait.</br></br></br>';
	$msg = (isset($_SESSION['msg']) 	? $_SESSION['msg'] : EMPTYMSG); 
	if(!empty($_POST['submit'])){
		$files=array();
		$fdata=$_FILES['files'];
		if(is_array($fdata['name'])){
		for($i=0;$i<count($fdata['name']);++$i){
				$files[]=array(
			'name'    =>$fdata['name'][$i],
			'type'  => $fdata['type'][$i],
			'tmp_name'=>$fdata['tmp_name'][$i],
			'error' => $fdata['error'][$i], 
			'size'  => $fdata['size'][$i]  
			);
		}
		}else $files[]=$fdata;
	$msg = "";
	foreach($files as $file){
	}
		$extension = end(explode('.', $file['name']));
		if ((($file['type'] == 'image/gif')
		|| ($file['type'] == 'image/jpeg')
		|| ($file['type'] == 'image/jpg')
		|| ($file['type'] == 'image/pjpeg')
		|| ($file['type'] == 'image/x-png')
		|| ($file['type'] == 'image/png'))
		&& ($file['size'] < $allowedSize)
		&& in_array($extension, $allowedExts)){
			if ($file['error'] > 0){
				echo 'Return Code: ' . $file['error'] . '<br>';
				$msg = $msg . 'Return Code: ' . $file['error'] . '<br>';
			}
			else {
				echo 'Upload: ' . $file['name'] . '<br>';
				echo 'Type: ' . $file['type'] . '<br>';
				echo 'Size: ' . ($file['size'] / 1024) . ' kB<br>';
				echo 'Temp file: ' . $file['tmp_name'] . '<br>';
				if (file_exists($root . '/img/upload/' . $file['name'])){
					echo $file['name'] . ' already exists. ';
					$msg = $msg . $file['name'] . ' already exists. ' . '<br>';
				}
				else{
					
					move_uploaded_file($file['tmp_name'],
					$root . '/img/upload/' . str_replace(' ', '_', $file['name']));
					// formatImage($root . '/img/upload/' . $file['name'], $root . '/img/formatted/');
					echo 'Stored in: ' . '/img/upload/' . $file['name'];
					$msg = $msg . 'Stored in: ' . '/img/upload/' . $file['name'] . '<br>';
				}
			}
		}
		else{
			echo 'Invalid file';
		}
	}
	// if(!empty($_POST['reformat'])){
		// $msg = '';
		// if(empty($_POST['check'])){
			// $msg = 'Nothing selected';
		// }
		// else{
			// foreach($_POST['check'] as $selected){
				// formatImage($selected, $root . '/img/formatted/');
				// $msg = basename($selected) . ' ' . $msg;
			// }
			// $msg = $msg . ' ---> reformatted.';
		// }		
	// }
	if(!empty($_POST['print'])){
		$msg = '';
		if(empty($_POST['check'])){
			$msg = 'Nothing selected';
		}
		else{
			foreach($_POST['check'] as $selected){
				$msg = basename($selected) . ' ' . $msg;
			}
		}		
	}
	$_SESSION['msg']= $msg;
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL=../Files.php">'; 
	}
	else {
	   echo "You are not authorized to access this page, please login. ";
   }
?>