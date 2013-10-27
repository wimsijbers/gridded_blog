<?php
function refreshTweetDB($mysqli){
	$result = $mysqli->query('SELECT rest_data FROM ws_rest WHERE rest_name = "last_tweet_update"');
	$result = $result->fetch_object()->rest_data;
	//smaller means if older than
	if($result < date('Y-m-d H:i:s', time()-TWEETREFRESHRATE)){
		$result = $mysqli->query('SELECT rest_name, rest_data FROM ws_rest WHERE rest_name LIKE "tw_%"');
		$twitterauth = array();
		while($row = $result->fetch_object()){
			$twitterauth[$row->rest_name] = $row->rest_data;
		}
		$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    => $twitterauth['tw_consumer_key'],
		'consumer_secret' => $twitterauth['tw_consumer_secret'],
		'user_token'      => $twitterauth['tw_user_token'],
		'user_secret'     => $twitterauth['tw_user_secret'],
		));
		$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'), array('screen_name' => TWITTERUSERNAME, 'count' => TWEETCOUNT));
		$response = $tmhOAuth->response;
		$response = json_decode($response['response']);
		if($response != array()){
			$mysqli->query('TRUNCATE TABLE ws_tweets');
			var_dump($mysqli->error);
			$stmt = $mysqli->prepare('INSERT INTO ws_tweets (tweet_author, tweet_date, tweet_content) VALUES ( ? , ? , ?)');
			foreach($response as $tweet){
				$stmt->bind_param('sss', $tweet->user->name, formatTwitterDate($tweet->created_at), $tweet->text);
				$stmt->execute();
			}
		}
		//oath screws up the timezone
		date_default_timezone_set( 'Europe/Brussels' );
		$mysqli->query('UPDATE ws_rest SET rest_data = "'. date('Y-m-d H:i:s')  . '" WHERE rest_name = "last_tweet_update"');
	}
}

function formatTwitterDate($date) {
    date_default_timezone_set('UTC');
    $date = strtotime($date);
	date_default_timezone_set( 'Europe/Brussels' );
    return date('Y-m-d H:m:s', $date);
}

// function formatText($text){
	// $text = explode("\n", $text);
	// $newtext = '';
	// foreach($text as $p){
		// $newtext = $newtext . '<p>' . $p . '</p>';
	// }
	// return quicklink($newtext);
// }

function formatText($text){
	$text =Markdown($text);
	return quicklink($text);
}

function htmlsan($htmlsanitize){
    return $htmlsanitize = htmlspecialchars($htmlsanitize, ENT_QUOTES, 'UTF-8');
}

//resizes with $max the max dimension
// function smartResize($url,$max){
	// list($width_orig, $height_orig) = getimagesize($url);
	// $img = WideImage::load($url);
	// $ratio_orig = $width_orig/$height_orig;
	// if ($ratio_orig > 1) {
		// $img = $img->resizeDown($max, $max*$ratio_orig);
	// } else {
		// $img = $img->resizeDown($max/$ratio_orig, $max);
	// }
	// return $img;
// }

function quicklink($text){
	return autolink($text, 30, " class='color-flash1' target='_blank'");
}

function checkFormatted($name){
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	return (file_exists($root . '/img/formatted/orig-' . $name) 
			&& file_exists($root . '/img/formatted/feed-' . $name)
			&& file_exists($root . '/img/formatted/page-' . $name)
			);
}

function formatImage($imgurl, $targetfolder, $width, $height){
	// $img = smartResize($imgurl, 770);
	$origimg = WideImage::load($imgurl);
	$name = end(explode('/', $imgurl));
	
	$img = $origimg->resize($width, $height, 'outside');
	$img = $img->crop('center', 'center', $width, $height);
	$img->saveToFile($targetfolder . '/feed-' . $name);
	
	$img = $origimg->resize(1200,900,'inside'); 
	$img->saveToFile($targetfolder . '/fs-' . $name);

	$img = $origimg->resize(770,500,'outside'); 
	$img = $img->crop('center', 'center', 770, 500);
	$img->saveToFile($targetfolder . '/page-' . $name);
	
	copy($imgurl, $targetfolder . '/orig-' . $name);
}

// function getImgName($url){
	// $parsed = end(explode('/', $url));
	// $parsed = end(explode('orig-', $parsed));
	// $parsed = end(explode('small-', $parsed));
	// $parsed = end(explode('large-', $parsed));
	// $parsed = reset(explode('.', $parsed));
	// return $parsed;
// }

// function getUploadName($url){
	// $parsed = end(explode('/', $url));
	// $parsed = end(explode('orig-', $parsed));
	// $parsed = end(explode('small-', $parsed));
	// $parsed = end(explode('large-', $parsed));
	// return $parsed;
// }

// array pos : 0 1 2 3
// normal    : 1 2 3 4 < used for position counting
// position is the actual position, not element
function insertAfter($array, $element, $position){
	$head = array_slice($array, 0, $position);
	$tail = array_slice($array, $position);
	return array_merge($head, array($element), $tail);
}
function insertBefore($array, $element, $position){
	$head = array_slice($array, 0, $position-1);
	$tail = array_slice($array, $position-1);
	return array_merge($head, array($element), $tail);
}


function limittext($text, $limit){
	if(strlen($text) > $limit){
		return substr($text, 0, $limit+1) . '...';
	} else{
		return $text;
	}
}
?>
