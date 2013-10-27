<?php
// general setup
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
date_default_timezone_set( 'Europe/Brussels' );
$postsperpage = 10;
$tweetperc = 50; // define the max percentage of tweets versus normal posts... 0 to turn off (also doesn't fetch etc)
//$agelimit = new DateTime("2013-03-31 18:39:04"); // best to set this to your oldest post
$agelimit = new DateTime("2013-06-23 21:27:25"); // best to set this to your oldest post

$allowedExts = array("gif", "jpeg", "jpg", "png", "JPG", "GIF", "PNG", "JPEG");
$allowedSize = 10000000;
//twitter setup
define("TWITTERUSERNAME", "wimsijbers");



//private page settings
define("EMPTYMSG", "Nothing to report.");

//don't touch unless you know what you are doing
$postformats = array('text');
define("TWEETREFRESHRATE", 300); // in seconds
define("TWEETCOUNT", 100);
// sizing of feed, also set in style.css postsizing
define("BASEWIDTH", 250);
define("BASEHEIGHT", 160);
define("SPACING", 10);
?>
