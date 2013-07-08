<?php 

/**
 * Contains the definition of class TwitterManager
 */


/**
 * Manages the connection with Twitter
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class TwitterManager {
	
	/**
	 * The OAuth customer key
	 * @var string
	 */
	public static $oauth_key;
	
	/**
	 * The OAuth customer secret
	 * @var string
	 */
	public static $oauth_secret;
	
	/**
	 * The OAuth token
	 * @var string
	 */
	public static $oauth_token;
	
	/**
	 * The OAuth token secret
	 * @var string
	 */
	public static $oauth_token_secret;
	
	/**
	 * The connection to Twitter APIs
	 * @var TwitterOAuth
	 */
	public static $connection;
	
	/**
	 * Some transients for cache
	 * @var array
	 */
	public static $transients = array(
		'tweets'					=>	'twitter_manager_my_tweets',
		'oauth_token'				=>	'twitter_manager_oauth_token',
		'oauth_token_secret'		=>	'twitter_manager_oauth_token_secret',
		'oauth_temp_credentials'	=>	'twitter_manager_oauth_temp_credentials'
	);
	
	/**
	 * List of WP AJAX actions used by this feature
	 * @var array
	 */
	public static $actions = array(
		'update_tweets'	=>	array(
			'action'		=>	'update_tweets',
			'callback'		=>	array(__CLASS__, 'on_ajax_update_tweets'),
		),
		'show_tweets'	=>	array(
			'action'		=>	'show_tweets',
			'callback'		=>	array(__CLASS__, 'on_ajax_show_tweets'),
		),
	);
	
	/**
	 * Enables the connection with Twitter
	 * 
	 * You need to create a new app and authorize it from your twitter account
	 * 
	 * @param string $customer_key Consumer key
	 * @param string $customer_secret Consumer secret
	 * @param string $token Access token
	 * @param string $token_secret Access token secret
	 */
	public static function enable($customer_key, $customer_secret, $token, $token_secret){
		self::$oauth_key 			=	$customer_key;
		self::$oauth_secret 		=	$customer_secret;
		self::$oauth_token 			=	$token;
		self::$oauth_token_secret 	=	$token_secret;
		
		foreach(self::$actions as $action){
			add_action('wp_ajax_'.$action['action'], $action['callback']);
			add_action('wp_ajax_nopriv_'.$action['action'], $action['callback']);
		}
		
		require_once WORDPRESS_THEME_UTILS_LIBRARIES_ABSOLUTE_PATH . 'twitteroauth/twitteroauth/twitteroauth.php';
		
		self::$connection = new TwitterOAuth(
			self::$oauth_key, 
			self::$oauth_secret, 
			self::$oauth_token, 
			self::$oauth_token_secret
		);
	}
	
	/**
	 * Completely disables the connection with Twitter
	 */
	public static function disable(){
		foreach(self::$actions as $action){
			remove_action('wp_ajax_'.$action['action'], $action['callback']);
			remove_action('wp_ajax_nopriv_'.$action['action'], $action['callback']);
		}
	}
	
	/**
	 * Updates the list of tweets
	 */
	public static function on_ajax_update_tweets(){
		$result = self::$connection->get('statuses/user_timeline');
		if(isset($result->errors)){
			wp_mail(
				get_option('admin_email'), 
				__('Error on Twitter Manager', 'wtu_framework'), 
				'<pre>'.$result.'</pre>'
			);
		}
		
		set_transient(self::$transients['tweets'], $result);

		header('Content-type: application/json');
		die(json_encode($result));
	}
	
	/**
	 * Shows the cached tweets
	 */
	public function on_ajax_show_tweets(){
		header('Content-type: application/json');
		die(json_encode(get_transient(self::$transients['tweets'])));
	}
	
	
}