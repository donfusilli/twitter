<?php

// tentative example of getting a tweet id and then retrieving the whole
// conversation; also stores conversation as JSON in txt file
set_time_limit(0);

// show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// example string to query Twitter API
// https://api.twitter.com/1.1/search/tweets.json?q=%23happy&result_type=recent&count=100

// require class
require_once('lib/dcTwitter.php');

// make new instance of my class
$twitter = new Twitter();

// select a tweet id
//$testId = $twitter->getTweetIds('happy')[50];
// using hardcoded tweetId in demo so we guarantee a reply; otherwise, recent tweets
// probably won't have any replies.
$emotions = array('happy', 'excited', 'great', 'blessed', 'loved', 'sad', 'tired', 'annoyed', 'sick', 'bored');

// get tweet id's for each emotion
foreach($emotions as $emotion){
	$tweetids = $twitter->getTweetIds($emotion, 25);
	foreach($tweetids as $tweetid){
		$conversationData = $twitter->getConversation($tweetid);
		$twitter->makeFile($conversationData, $emotion, $tweetid);
	}
}

// display it in browser
//print '<p>The conversation is returned as an array of tweets, with the tweet at index [0] being the original tweet</p>';
//$twitter->display($conversation);

// convert conversation to JSON and store that in a txt file
// returns fileName
//$fileName = $twitter->makeFile($conversation, $emotion, $testId);

//print "<p>Conversation written to <b>$fileName</b>.</p>";

//print "<p>The tweet id from $fileName is <b>" .$twitter->getTweetIdFromFile($fileName). "</b>.</p>";



