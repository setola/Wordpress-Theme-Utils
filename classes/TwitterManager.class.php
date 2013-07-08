<?php 

class TwitterManager {
	
	public static $oauth_key;
	public static $oauth_secret;
	public static $oauth_callback;
	public static $oauth_token;
	public static $oauth_token_secret;
	
	public static $connection;
	
	public static $transients = array(
		'my_tweets'					=>	'twitter_manager_my_tweets',
		'oauth_token'				=>	'twitter_manager_oauth_token',
		'oauth_token_secret'		=>	'twitter_manager_oauth_token_secret',
		'oauth_temp_credentials'	=>	'twitter_manager_oauth_temp_credentials'
	);
	
	public static function enable($customer_key, $customer_secret, $token, $token_secret){
		self::$oauth_key 			=	$customer_key;
		self::$oauth_secret 		=	$customer_secret;
		self::$oauth_token 			=	$token;
		self::$oauth_token_secret 	=	$token_secret;

		add_action('wp_ajax_show_my_tweets', array(__CLASS__, 'on_ajax_show_my_tweets'));
		add_action('wp_ajax_nopriv_show_my_tweets', array(__CLASS__, 'on_ajax_show_my_tweets'));
		
		add_action('wp_ajax_update', array(__CLASS__, 'update'));
		add_action('wp_ajax_nopriv_update', array(__CLASS__, 'update'));
		
		require_once WORDPRESS_THEME_UTILS_LIBRARIES_ABSOLUTE_PATH . 'twitteroauth/twitteroauth/twitteroauth.php';
		
		self::$connection = new TwitterOAuth(
			self::$oauth_key, 
			self::$oauth_secret, 
			self::$oauth_token, 
			self::$oauth_token_secret
		);
	}
	
	public static function disable(){}
	
	public static function update(){
		$result = self::$connection->get('statuses/user_timeline');
		
		echo '<pre>';
		var_dump($result);
		echo '</pre>';
		
		
		
		
	}
	
	public function on_ajax_show_my_tweets(){
		header('Content-type: application/json');
		die(get_transient(self::$transients['my_tweets']));
	}
	
	
}