<?php
	require_once( 'TwitterAPI.php' );

	$user_id = $_GET['username'];
	$json = json_decode(twitter_get("https://api.twitter.com/1.1/statuses/user_timeline.json", '?count=3&exclude_replies=1&include_rts=1&screen_name=' . $user_id));
	$tweet_id = $json[0]->id_str;
	$text = $json[0]->text;
	$link = $json[0]->entities->urls[0]->url;
	$ex_url = $json[0]->entities->urls[0]->expanded_url;
	$image = $json[0]->user->profile_image_url_https;
	$image = str_replace("_normal", "", $image);
	
	if ( (int)$_GET['type'] == 1 ) {
		header('Content-Type:image/jpeg');
		echo curl_proxy($image);
	} else {
		$temp = get_text($ex_url, $tweet_id);
		if ($temp !== "") {
			$text = $temp;
		} else {
			$text = str_replace($link, "", $text);
		}
		$arr = array('text' => $text, 'link' => $link, 'id' => $tweet_id, 'image' => $image);
		echo json_encode($arr);
	}
	
	function twitter_get($url, $getparam) {
		$settings = array( //To-do
			'oauth_access_token' => "23333",
			'oauth_access_token_secret' => "23333",
			'consumer_key' => "23333",
			'consumer_secret' => "23333"
		);
		$requestMethod = 'GET';
		
		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->setGetfield($getparam)->buildOauth($url, 'GET')->performRequest();
		return $response;
	}
	
	function curl_proxy($url){       
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_PROXY, "127.0.0.1");
		curl_setopt ($ch, CURLOPT_PROXYPORT, 8088);
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
		$result = curl_exec ($ch);
		curl_close($ch);       
		return $result;
	}
	
	function get_text($url, $id) {
		if ($url == "") return "";
		
		$xml = curl_proxy($url);
		$find_link = "https://twitter.com/KanColle_STAFF/status/";
		$find_link_end = '"';
		$link_pos_temp = stripos($xml, $find_link);
		$link_pos = strlen($find_link) + $link_pos_temp;
		$link_pos_end = stripos($xml, $find_link_end, $link_pos);
		
		$tweet_id = substr($xml, $link_pos, $link_pos_end - $link_pos);
		//echo $tweet_id;
		
		if ($id == $tweet_id) {
			$find_text = '<meta  property="og:description" content="“';
			$find_text_end = '”">';
			$text_pos_temp = strpos($xml, $find_text);
			$text_pos = strlen($find_text) + $text_pos_temp;
			$text_pos_end = strpos($xml, $find_text_end, $text_pos);
		
			$text = substr($xml, $text_pos, $text_pos_end - $text_pos);
			$text = str_replace("&#10;", "\n", $text);
			return trim($text);
		} else {
			return "";
		}
	}