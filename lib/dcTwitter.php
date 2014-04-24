<?php

/*
 * Library of utility functions built upon Adrian Crepaz's acTwitterConversation class
 * For more info, see http://adriancrepaz.com/twitter_conversions_api or https://github.com/adriancrepaz/acTwitterConversation
 * 
 * also relies on Abraham's TwitterOAuth class: https://github.com/abraham/twitteroauth
 *
 * Written by Don Carpenter
 *
 * Version: 1.0
 */
 
 
require_once('lib/twitteroauth/twitteroauth.php');
require_once('lib/twitteroauth/config.php');
require_once('lib/acTwitterConversation.php');
 
class Twitter { 

	// display content in browser in a nice format
	// useful for debugging purposes
	public function display($content){
		header('Content-Type: text/html; charset=utf-8');
		echo '<pre>' . print_r($content, true) . '</pre>';
	}
	 
	// returns an array of Tweet id's matching the given emotion
	public function getTweetIds($emotion = NULL, $count = 100){
	
		$tweetIds = array();
	
		if($emotion == NULL){
			return $tweetIds;
		}
	
		// build the TwitterOAuth object with correct credentials
		// credentials defined in config.php
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

		// query the Twitter API as user @donwcarpenter
		$content = $connection->get('search/tweets', array('q' => '%23' . $emotion, 'result_type' => 'recent', 'count' => $count));
		
		foreach($content->statuses as $status) {
			$tweetIds[] = $status->id_str;
		}
		return $tweetIds;
	}
	
	// returns text of tweet identified by $tweetId
	public function getTweetById($tweetId){
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
		$content = $connection->get('statuses/show/'.$tweetId, array('trim_user' => 'true'));
		return $content->text;
	}
	
	// return conversation data using Adrian Crepaz's class
	public function getConversationData($tweetId, $method = 'data', $conversate = CONVERSATE_AFTER){
		// Initiate the class, and fetch the conversation.
		$twitter = new acTwitterConversation();
		$conversation = $twitter->fetchConversion($tweetId, $method, $conversate);
		return $conversation;
	}

	// return conversation (actual tweets only, no metadata)
	public function getConversation($tweetId, $method = 'data', $conversate = CONVERSATE_AFTER){
		$conversation = $this->getConversationData($tweetId, $method, $conversate);
		
		
		if($conversation['error'] == true){
			return $tweets = array('error fetching tweets');
		}
		else{
			$tweets = array();
			foreach($conversation['tweets'] as $tweet){
				$tweets[] = $tweet['content'];
			}
			array_unshift($tweets, $this->getTweetById($tweetId));
			return $tweets;
		}
	}
	
	// pass getConversationData to makeFile
	public function makeFile($fileContent, $emotion, $tweetId){
		$fileContent = json_encode($fileContent);
		$fileName = $emotion. '_msg_'. $tweetId. '.txt';
		$handle = fopen($fileName, 'w');
		fwrite($handle, $fileContent);
		fclose($handle);
		return $fileName;
	}

	// extract tweet id from file name
	// expects format: emotion_msg_tweetId
	public function getTweetIdFromFile($fileName){
		$tmp = explode('_', $fileName)[2];
		return $tweetId = explode('.', $tmp)[0];
	}




	
}