<?php
class Feed
{

	protected $posts = array();
	protected $oldestpostdate = "";
	protected $postsperpage = 10; // default 10, set in setup.php
	protected $tweetperc = 20;
	function __construct(){
	}	
	public function setup($postsperpage, $tweetperc){
		$this->postsperpage = $postsperpage;
		$this->tweetperc = $tweetperc;
	}
	public function setPosts($posts){
		$this->posts = $posts;
	}
	public function getPosts(){
		return $this->posts;
	}
	public function mergeMorePosts($mysqli, $before){
		$posts = $this->getMorePosts($mysqli, $before);
		$this->posts = array_merge($this->posts, $posts);
	}
	
	private function getPostsFromDB($mysqli, $before, $amount){
		$fetchedPosts = array();
		$result = $mysqli->prepare(	'SELECT post_id, post_date, post_rows, post_columns, post_contentType, post_format, post_title, post_content, post_islink 
									FROM ws_posts WHERE post_status = "show" AND post_date < ?  ORDER BY post_date DESC LIMIT ?');
		$result->bind_param('si', $before, $amount);
		$result->execute();
		$result->store_result();
		$result->bind_result($id, $date, $rows, $columns, $contentType, $format, $title, $content, $islink);
		while($row = $result->fetch()){
			$post = new Post();
			$post->fetchToPost($id, $date, $rows, $columns, $contentType, $format, $title, $content, $islink);
			array_push($fetchedPosts, $post);
		}
		return $fetchedPosts;
	}
	private function getTweetsFromDB($mysqli, $before, $amount){

		$tweets = array();
		$result = $mysqli->prepare(	'SELECT tweet_id, tweet_date, tweet_author, tweet_content
									FROM ws_tweets WHERE tweet_date < ? ORDER BY tweet_date DESC LIMIT ?');		
		$result->bind_param('si', $before, $amount);
		$result->execute();
		$result->store_result();
		$result->bind_result($id, $date, $author, $content);
		while($row = $result->fetch()){
			$post = new Post();
			$post->tweetFetchToPost($id, $date, $author, $content);
			array_push($tweets, $post);
		}
		return $tweets;
	}	
	
	private function mergePostsWithTweets($posts, $tweets){
		$pos = 1;
		$tweetcount = 0;
		$newposts = array();
		while(
		$pos <= count($posts)
		&& $tweetcount < $this->postsperpage*$this->tweetperc/100){
			if($tweets[$tweetcount]->getDate() > $posts[$pos-1]->getDate()){
				array_push($newposts, $tweets[$tweetcount]);
				$tweetcount++;			
			}
			else{
				array_push($newposts, $posts[$pos-1]);
				$pos++;
			}
		}
		$newposts = array_merge($newposts, array_slice($posts, $pos-1));
		return $newposts;
	}	
	
	public function getMorePosts($mysqli, $before){
		$before = ($before == "" ? date('Y-m-d H:i:s') : $before);
		$newposts = $this->getPostsFromDB($mysqli, $before, $this->postsperpage);
		if(!empty($newposts)){
			$this->oldestpostdate = $newposts[count($newposts)-1]->getDate();
		}
		if(!$this->tweetperc == 0){
			$tweets = $this->getTweetsFromDB($mysqli, $before, $this->postsperpage);
			if(!empty($tweets)){
				$newposts = $this->mergePostsWithTweets($newposts, $tweets);
			}
		}
		return $newposts;
	}
	
	public function getOldestPostDate(){
		return $this->oldestpostdate;
	}
	
	public function getHTML(){
	$html = '';
		foreach($this->posts as $post){
			$html = $html . $post->getFeedHTML();
		}
		return $html;
	}
}
?>