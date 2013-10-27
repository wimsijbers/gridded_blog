<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once $root . '/php/setup.php';
	include_once $root . '/php/class_post.php';
	include_once $root . '/php/login_functions.php';
	include_once $root . '/php/db_connect.php';		
	sec_session_start();
	if(login_check($mysqli) == true) {
	include $root . '/html/wrapper_top.html';
	$result = $mysqli->query('SELECT post_id, post_date, post_rows, post_columns, post_contentType, post_title, post_status, post_islink FROM ws_posts ORDER BY post_date DESC');
	$msg = (isset($_SESSION['msg']) 	? $_SESSION['msg'] : EMPTYMSG);
	$_SESSION['postids'] = Array();
?>
	<link rel="stylesheet" href="./css/style-private.css" type="text/css" />
	<div id="privatepage">
	
	<?php include_once $root . '/html/headerPrivatePage.html';?>
	
		<div class="color-bg-vlight boxshadow-light margin20padding20">
		<?php 	
			echo $msg;
			$_SESSION['msg'] = EMPTYMSG;
		?>
		</div>
		<div class='color-bg-vlight boxshadow-light margin20padding20'>
		<table>
		<tr class="color-bg-dark color-vlight">
			<td>date</td>
			<td>title</td>
			<td>rows</td>
			<td>columns</td>
			<td>content</td>
			<td>status</td>
			<td>link</td>
			<td colspan="2">options</td>
		</tr>
			<?php
					while($row = $result->fetch_object()){
						$_SESSION['postids'][] = $row->post_id;
						echo '<tr>';
							echo '<td>', $row->post_date, '</td>';
							echo '<td id="title"><a href="index.php?postid=',$row->post_id,'" target="_blank" class="color-flash1">', $row->post_title, '</a></td>';
							echo '<td>', $row->post_rows, '</td>';
							echo '<td>', $row->post_columns, '</td>';
							echo '<td>', $row->post_contentType, '</td>';
							echo '<td>', $row->post_status, '</td>';
							echo '<td><form action="./php/process_management.php" method="post">
							<input type="checkbox" onClick="submit();" value="',$row->post_id,'" name="islink" ',($row->post_islink ? "checked": ""),'></input>
							<input type="hidden" value="',$row->post_id,'" name="islink"></input>
							</form></td>';
							$showhide = ($row->post_status == "hide" ? "show" : "hide");
							echo '<td><form action="./php/process_management.php" method="post"><button type="submit" value="',$row->post_id,'" name="',$showhide,'">',$showhide,'</button></form></td>';
							echo '<td><a href="createPost.php?edit=',$row->post_id,'" class="color-flash1">edit</a></td>';
						echo '</tr>';
					}
			?>
		</table>
		</div>
	</div>
	
	
<?php
	


	include $root . '/html/wrapper_bottom.html';
	} 
	else {
	   echo 'You are not authorized to access this page, please login. ';
	}
	
	
?>