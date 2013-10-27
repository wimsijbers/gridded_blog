<?php
class Post
{
	protected $size = array(1 , 1); // rows, columns
	protected $title = "";
	protected $date; // should be datetime format
	protected $content = ""; // must be in xml format > easily edited and easy to HTML
	protected $format = "text"; // implemented: text, tweet
	protected $contentType = "general"; 
	protected $id = 0;
	protected $islink = false;

	
	function __construct(){
		$date = new DateTime();
	}
	public function isEmpty(){
		if(empty($this->size) && empty($this->title) && empty($this->date) && empty($this->modules) && empty($this->partitioning)){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	public function getSize(){
		return $this->size;
	}
	public function getContentType(){
		return $this->contentType;
	}
	public function setContentType($contentType){
		$this->contentType = $contentType;
	}
	public function getFormat(){
		return $this->format;
	}
	public function setFormat($format){
		$this->format = $format;
	}
	public function getRows(){
		return $this->size[0];
	}
	public function getColumns(){
		return $this->size[1];
	}
	public function setSize($rows, $columns){
		$this->size = array($rows, $columns);
	}
	
	public function getTitle(){
		return $this->title;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	
	public function getDate(){
		return $this->date;
	}
	public function setDate($date){
		// $this->date = new DateTime($date);
		$this->date = $date;
	}
	
	public function setContent($content){
		$this->content = $content;
	}
	public function getContent(){
		return $this->content;
	}
	public function setIslink($islink){
		$this->islink = $islink;
	}
	public function getIslink(){
		return $this->islink;
	}
	
	public function checkValidity(){
		if($this->title == "" || $this->content == ""){
			return FALSE;
		}
		else{
			$contentXML = new SimpleXMLElement($this->content);
			foreach($contentXML->children() as $name => $data){				
				if($data == ''){
					return FALSE;
				}
			}
		}
		return TRUE;
	}
	
	public function publish($mysqli){
		if($this->checkValidity()){
			$stmt = $mysqli->prepare("INSERT INTO ws_posts 	(post_author, post_date, post_rows, post_columns, post_contentType, post_format, post_title, post_content, post_status, post_islink)
											VALUES 			(?,				? ,			?,            ?,			?,   			?,			?,			?,			'hide', ?)");
			$stmt->bind_param('ssiisssss', $_SESSION['user_id'], date('Y-m-d H:i:s')  , $this->getRows(), $this->getColumns(), $this->contentType, $this->format, $this->title, $this->content, $this->islink);
			if($stmt->execute()){
				return TRUE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	public function edit($mysqli, $postid){
		if($this->checkValidity()){
			$stmt = $mysqli->prepare(	"UPDATE ws_posts 
										SET post_author = ?, post_rows = ?, post_columns = ?, post_contentType = ?, post_format = ?, post_title = ? , post_content = ?, post_status = 'hide', post_islink = ?
										WHERE post_id = ?");
			$stmt->bind_param('siisssssi', $_SESSION['user_id'], $this->getRows(), $this->getColumns(), $this->contentType, $this->format, $this->title, $this->content, $this->islink, $postid);
			if($stmt->execute()){
				return TRUE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	public function getPostFromDB($mysqli, $postid){
		$result = $mysqli->prepare(	'SELECT post_date, post_rows, post_columns, post_contentType, post_format, post_title, post_content, post_islink FROM ws_posts WHERE post_id = ?');
		$result->bind_param('i', $postid);
		$result->execute();
		$result->store_result();
		$result->bind_result($date, $rows, $columns, $contentType, $format, $title, $content, $islink);
		$result = $result->fetch();
		$this->fetchToPost($postid, $date, $rows, $columns, $contentType, $format, $title, $content, $islink);
	}
	
	public function fetchToPost($id, $date, $rows, $columns, $contentType, $format, $title, $content, $islink){
		$this->title = $title;
		$this->setDate(new DateTime($date));
		$this->size = array($rows, $columns);
		$this->contentType = $contentType;
		$this->format = $format;
		$this->content =$content;
		$this->id = $id;
		$this->islink = $islink;
	}
	public function tweetFetchToPost($id, $date, $author, $content){
		$this->title = 'tweet';
		$this->setDate(new DateTime($date));
		$this->size = array(1,1);
		$this->contentType = 'general';
		$this->format = 'tweet';
		$this->createTweetContent($content, $author);
		$this->id = $id;
		$this->islink = false;
	}
	
	public function getSmartDate(){
			$smartdate = $this->date->format('D, M j');
			$diff = time() - $this->date->format('U');
			if($diff < 3600){
				$smartdate = round($diff/60) . ' minutes ago';
				if(round($diff/60) <= 1){
					$smartdate = '1 minute ago';
				}
			}
			else if($diff < 86400){
				$smartdate = round($diff/3600) . ' hours ago';
				if(round($diff/3600) <= 1){
					$smartdate = '1 hour ago';
				}
			} else{
				$smartdate = 'on ' . $smartdate;
			}
			return $smartdate;
	}
	
	public function getFeedHTML(){
		// if($this->islink && $this->format != 'tweet'){
			// return '<article id="' . $this->format . $this->id.'" class="color-bg-vlight islink column'.$this->size[1].' row'.$this->size[0].' '. $this->format .'">'. $this->getArticleHTML() . '</article>' ;
		// } else{
			return '<article id="' . $this->format . $this->id.'" class="color-bg-vlight column'.$this->size[1].' row'.$this->size[0].' '. $this->format .'">'. $this->getArticleFeedHTML() . '</article>' ;
		// };
	}
	
	public function getContentNode($node){
		if($this->content != ''){
			$contentXML = new SimpleXMLElement($this->content);
			return $contentXML->$node;
		}
		else{
			return '';
		}
	}
	
	private function getFeedImageHTML(){
		// $prefix = ($this->id == 0 ? 'orig-' : 'feed-');
		$photoString = $this->getContentNode('photos');
		$photos = preg_split('/\s+/', $photoString);
		if(empty($photos)){
			return '';
		}
		else{	
			$middle = '<a id="photo" href="./img/formatted/fs-' . $photos[0].'"><img src="./img/formatted/feed-' . $photos[0] . '" alt="photo"/></a><div id="photolist" class="hidden">'.$photoString.'</div><div id="photodate" class="hidden">'.$this->getSmartDate().'</div>';
			if(sizeof($photos) > 1){
				$middlePages = '<h4 id="imgnr" class="color-bg-flash3 color-vlight invarticlehover tag padding-10-20">1 of '.sizeof($photos).'</h4>';
			} 
			else{
				$middlePages = '';
			}
			$left = '<a href="" class="articlehover row'. $this->size[0].' color-vdark ws_img_navprev"><div class="padding-10-20 color-bg-vdark color-vlight ws_img_navprev">prev</div></a>';
			$right = '<a href="" class="articlehover row'. $this->size[0].' color-vdark ws_img_navnext"><div class="padding-10-20 color-bg-vdark color-vlight ws_img_navnext">next</div></a>';
			return $middle . $middlePages . $left . $right;
		}
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	// the switch cases and the functions below must be changed if you want to add more formats //////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	private function getPostArea(){
		return $this->size[0]*$this->size[1];
	}
	//content is in xml
	private function getArticleFeedHTML(){
		if($this->content != ''){
			$contentXML = new SimpleXMLElement($this->content);
			switch ($this->format){
				case 'text':
					
					$addtoheaderclass = ($contentXML->floatTitle == 1 ? ' floatTitle' : '');
					if($this->islink){
						$contentHTML = formatText($contentXML->text . ' | <a class="color-flash1" href="index.php?postid='.$this->id. '">more</a>' ) . '<h4 class="articlehover"> Posted '. $this->getSmartDate() .'</h4>';
						$articleHTML = '<h3 class="color-vlight'. $addtoheaderclass .'"><a class="color-flash1" href="index.php?postid='.$this->id.'">' . $this->title . '</a></h3>' . $contentHTML;
					} else{
						$contentHTML = formatText($contentXML->text) . '<h4 class="articlehover"> Posted '. $this->getSmartDate() .'</h4>';
						$articleHTML = '<h3 class="color-vdark'. $addtoheaderclass .'">' . $this->title . '</h3>' . $contentHTML;
					}
					break;
				case 'tweet':
					$contentHTML = '<p class="tweet color-flash2">' . quicklink($contentXML->text) . '</p>';
					$articleHTML = '<h4 class="color-vvdark articlehover">' .'Tweeted '. $this->getSmartDate() . '</h4>' . $contentHTML;
					break;
				case 'vimeo':
					$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $contentXML->vimeoid .'.php'));
					$thumb = $hash[0]['thumbnail_large'];
					$contentHTML = '<img class="column'.$this->size[1].' row'.$this->size[0].'" src="' . $thumb . '" alt="vimeo video thumb"></img>';
					// $contentHTML = '<h4 class="fullwidth color-vvdark color-bg-vlight articlehover">' .'Posted '. $this->getSmartDate() . '</h4>' . $contentHTML;
					$contentHTML = $contentHTML . '<h4 class="color-vlight color-bg-flash3 invarticlehover tag padding-10-20">video</h4>';
					$articleHTML = $contentHTML . '<h3 class="abstitle column'.$this->size[1].' articlehover padding-0"><a class="padding-10-20 linkstyle1 color-flash1 color-bg-vdark" href="index.php?postid='.$this->id.'">' . $this->title . '</a></h3>';
				break;
				case 'photo':
					// $contentHTML =  $this->getFeedImageHTML() . '<h4 class="articlehover"> Posted ' . $this->getSmartDate() .'</h4>';
					$contentHTML =  $this->getFeedImageHTML();
					if($this->islink){
						$articleHTML = $contentHTML . '<h3 class="abstitle column' .$this->size[1]. ' articlehover padding-0"><a class="linkstyle1 padding-10-20 color-flash1 color-bg-vdark" href="index.php?postid='.$this->id.'">' . $this->title . '</a></h3>';
					} else{
						$articleHTML = $contentHTML . '<h3 class="abstitle column' .$this->size[1]. ' padding-10-20 delayedhide articlehover color-bg-vdark color-vlight">' . $this->title . '</h3>';
					}
				break;
				default:
					$articleHTML = '';
			}
			return $articleHTML;
		}
		else{
			return '';
		}
	}
	public function getPageHTML(){
		if($this->content != ''){
			$contentXML = new SimpleXMLElement($this->content);
			switch ($this->format){
				case 'text':
					$contentHTML = formatText($contentXML->text) . formatText($contentXML->moretext) . '<h4 class="page"> Posted '. $this->getSmartDate() .'</h4>';
					$articleHTML = '<h3 class="page">' . $this->title . '</h3>' . $contentHTML;
					$articleHTML = '<article id="' . $this->format . $this->id.'" class="page color-bg-vlight column3 ' . $this->format .'">'. $articleHTML . '</article>' ;
					break;
				case 'vimeo':
					$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $contentXML->vimeoid .'.php'));
					$descr = $hash[0]['description'];
					$contentHTML = '<h4 class="page"> Posted '. $this->getSmartDate() .'</h4>';
					$contentHTML = '<p>' . quicklink($descr) . '</p>' . $contentHTML;
					$contentHTML = '<h3 class="padding-10-20 page">' . $this->title . '</h3>' . $contentHTML;
					$contentHTML = '<iframe id="vid" class="row2" src="http://player.vimeo.com/video/' . $contentXML->vimeoid . '?badge=0&amp;title=0&amp;byline=0&amp;portrait=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>' . $contentHTML;
					$articleHTML = '<article id="' . $this->format . $this->id.'" class="page color-bg-vlight column3 ' . $this->format .'">'. $contentHTML . '</article>';
					break;
				case 'photo':
					$photoString = $this->getContentNode('photos');
					$photos = preg_split('/\s+/', $photoString);
					$contentHTML = '<div id="photolist" class="hidden">'.$photoString.'</div><div id="photodate" class="hidden">'.$this->getSmartDate().'</div>';
					foreach($photos as $photo){
						$contentHTML = $contentHTML . '<a id="photo" href="./img/formatted/fs-' . $photo .'"><img alt="photo" class="page padding-10bottom" src="./img/formatted/page-' . $photo . '"></img></a>';
					}
					$contentHTML = $contentHTML . '<h4 class="page"> Posted '. $this->getSmartDate() .'</h4>';
					$articleHTML = '<h3 class="page padding-10-20">' . $this->title . '</h3>' . $contentHTML;
					$articleHTML = '<article id="' . $this->format . $this->id.'" class="page color-bg-vlight column3 ' . $this->format .'">'. $articleHTML . '</article>' ;
					break;
				default:
					$articleHTML = '';
			}
			return $articleHTML;
		}
		else{
			return '';
		}
	}	
	
	public function createContent($content){
		$doc = new DomDocument('1.0');
		$root = $doc->createElement('post', $content);
		$root = $doc->appendChild($root);
		$this->content = $doc->saveXML();
	}
	
	public function createContentFromArray($array){
		$doc = new DomDocument('1.0');
		$root = $doc->createElement('post');
		$root = $doc->appendChild($root);
		foreach($array as $name => $content){
			$text = $doc->createElement($name, $content);
			$text = $root->appendChild($text);
		}
		$this->content = $doc->saveXML();
	}
	
	public function createTextContent($text, $moretext, $floatTitle){
		$doc = new DomDocument('1.0');
		$root = $doc->createElement('post');
		$root = $doc->appendChild($root);
		$text = $doc->createElement('text', $text);
		$text = $root->appendChild($text);
		if($moretext != ''){
			$moretext = $doc->createElement('moretext', $moretext);
			$moretext = $root->appendChild($moretext);
		}
		$floatTitle = ($floatTitle ? 1 : 0);
		$floattitle = $doc->createElement('floatTitle', $floatTitle);
		$floattitle = $root->appendChild($floattitle);
		$this->content = $doc->saveXML();
	}
	
	public function createTweetContent($content, $author){
		$doc = new DomDocument('1.0');
		$root = $doc->createElement('post');
		$root = $doc->appendChild($root);
		$text = $doc->createElement('text', $content);
		$text = $root->appendChild($text);
		$author = $doc->createElement('author', $author);
		$author = $root->appendChild($author);
		$this->content = $doc->saveXML();
	}
}
?>
