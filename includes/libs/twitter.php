<?php
/* 
	File: twitter.php
	
	Description:
		The following class is for using twitter.com
		
		This class was based off the work of Antonio Lupetti. Original Work can be found at:
			http://woork.blogspot.com/2007/10/twitter-send-message-from-php-page.html
	
	Contributing Author( s): 
		Antonio Lupetti < antonio.lupetti@gmail.com >
		Scott Sloan < scott@aimclear.com > 
		
	Date: January 4th, 2008
	License: Creative Commons
	
*/
class twitter {
	
	private $user;
	private $pass;
	private $ch;
	private $twitterHost = "http://twitter.com/";
	
	public function __construct($username, $passwd) {
		$this->user = $username;
		$this->pass = $passwd;
		
		/* Create and setup  the curl Session */
		$this->ch = curl_init();
	
		curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_USERPWD, "$this->user:$this->pass");
		curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($this->ch, CURLOPT_POST, 1);
	}
	
	public function __destruct() {
		/*clean Up */
		curl_close($this->ch);
	}

	public function setStatus($stat) {
	
		if(strlen($stat) < 1)
			return false; 
		
		/*Set the host information for the curl session */
		$this->twitterHost .= "statuses/update.xml?status=". urlencode(stripslashes(urldecode($stat)));
		
		curl_setopt($this->ch, CURLOPT_URL, $this->twitterHost);
		
		/* Execute and get the http code to see if it was succesfull or not*/
		$result = curl_exec($this->ch);	
		$resultArray = curl_getinfo($this->ch);
				
		if ($resultArray['http_code'] == 200) ;
			return true;
			
		return false;
	}
}

//// A simple function using Curl to post (GET) to Twitter
//// Kosso : March 14 2007
//
//function postToTwitter($username,$password,$message){
//
//    $host = "http://twitter.com/statuses/update.xml?status=".urlencode(stripslashes(urldecode($message)));
//
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $host);
//    curl_setopt($ch, CURLOPT_VERBOSE, 1);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
//    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
//    curl_setopt($ch, CURLOPT_POST, 1);
//
//    $result = curl_exec($ch);
//    // Look at the returned header
//    $resultArray = curl_getinfo($ch);
//
//    curl_close($ch);
//
//    if($resultArray['http_code'] == "200"){
//         $twitter_status='Your message has been sended! <a href="http://twitter.com/'.$username.'">See your profile</a>';
//    } else {
//         $twitter_status="Error posting to Twitter. Retry";
//    }
//	return $result;
//}
?>
