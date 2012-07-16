<?php

class Creare_Latesttweet_Block_Latesttweet extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	
	public function isEnabled()
	{
		if(Mage::getStoreConfig('latesttweet/latesttweet/active')){
			return true;
		} else { 
			return false;
		}
	}
	
	public function getTwitterId()
	{
		$twitterid = Mage::getStoreConfig('latesttweet/latesttweet/twitterid');
		return $twitterid;
	}
	
	public function getNoFollow()
	{
		$nofollow = Mage::getStoreConfig('latesttweet/latesttweet/usenofollow');
		return $nofollow;
	}
	
	public function getNumberOfTweets()
	{		
		$numberoftweets = Mage::getStoreConfig('latesttweet/latesttweet/numberoftweets');
		return $numberoftweets;
	}
	
	public function getTagPref()
	{
		$tagpref = Mage::getStoreConfig('latesttweet/latesttweet/showlinks');
		return $tagpref;	
	}
	
	public function getNewWindow()
	{
		$tagpref = Mage::getStoreConfig('latesttweet/latesttweet/opennew');
		return $tagpref;	
	}
	
	private function getTweetData($tweetxml)
	{	
		
  		$xmlDoc = new DOMDocument();
  		$xmlDoc->load($tweetxml);

  		$x = $xmlDoc->getElementsByTagName("entry"); // get all entries
  		$tweets = array();
  		foreach($x as $item){
   			$tweet = array();

   			if($item->childNodes->length) {
    			foreach($item->childNodes as $i){
     				$tweet[$i->nodeName] = $i->nodeValue;
    			}
   			}
    		$tweets[] = $tweet;
  		}
				
		return $tweets;
	}
	
	private function cleanTwitterName($twitterid)
	{
		$test = substr($twitterid,0,1);
		
		if($test == "@"){
			$twitterid = substr($twitterid,1);	
		}
		
		return $twitterid;
		
	}
	
	private function changeLink($string, $tags=true, $nofollow=true, $newwindow=true)
	{
		if(!$tags){
			$string = strip_tags($string);
		}
		if($nofollow){
			$string = str_replace('<a ','<a rel="nofollow"', $string);	
		}
		if($newwindow){
			$string = str_replace('<a ','<a target="_blank"', $string);	
		}
  		return $string;
 	}
	
	private function getTweetArray($tweets)
	{
		
		$data = array();
		
		if(empty($tweets)) {
			return false;
		}
  		for($i=0;$i<count($tweets);$i++){
			$tweettag = $tweets[$i];
 	  		/********************** Getting Times (Hours/Minutes/Days) */
		   	$tweetdate = $tweettag["published"];
		   	$tweet = $tweettag["content"];
		   	$timedate = explode("T",$tweetdate);
		   	$date = $timedate[0];
		   	$time = substr($timedate[1],0, -1);
		   	$tweettime = (strtotime($date." ".$time))+3600; // This is the value of the time difference - UK + 1 hours (3600 seconds)
		   	$nowtime = time();
		   	$timeago = ($nowtime-$tweettime);
		   	$thehours = floor($timeago/3600);
		   	$theminutes = floor($timeago/60);
		   	$thedays = floor($timeago/86400);
  			/********************* Checking the times and returning correct value */
		   	if($theminutes < 60){
				if($theminutes < 1){
					$timemessage =  "Less than 1 minute ago";
				} else if($theminutes == 1) {
				 	$timemessage = $theminutes." minute ago";
				} else {
				 	$timemessage = $theminutes." minutes ago";
				}
			} else if($theminutes > 60 && $thedays < 1){
				 if($thehours == 1){
				 	$timemessage = $thehours." hour ago";
				 } else {
				 	$timemessage = $thehours." hours ago";
				 }
			} else {
				 if($thedays == 1){
				 	$timemessage = $thedays." day ago";
				 } else {
				 	$timemessage = $thedays." days ago";
				 }
			}			
			$data[$i]["tweet"] = $this->changeLink($tweet, $this->getTagPref(), $this->getNoFollow(), $this->getNewWindow());
			$data[$i]["time"] = $timemessage;
		}
		   
		return $data;	
	}	

 	public function getLatestTweets()
	{
		
		$twitterid = $this->getTwitterId();
		$not = $this->getNumberOfTweets();		
		
		if (!$twitterid){
			return false;
		}
		
		$twitterid = $this->cleanTwitterName($twitterid);		
		$tweetxml = "http://search.twitter.com/search.atom?q=from:" . $twitterid . "&rpp=" . $not . "";		
		$tweets = $this->getTweetData($tweetxml);		
		return($this->getTweetArray($tweets));		
  		
	}
}