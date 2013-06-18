<?php 
/**
 * Stores DebugUtils class definition
 */

/**
 * Utils to debug the code while writing it.
 * @author Emanuele 'Tex' Tessore
 * @package classes
 */
final class DebugUtils {
	
	/**
	 * @var DebugUtils singleton instance
	 */
	private static $instance = null;
	
	/**
	 * @var int the level of debug: html comments, h1 and pre, h1 pre and die.
	 */
	private $level;
	
	/**
	 * @var string the title to be printed on top of the variable dump
	 */
	private $title;
	
	/**
	 * @var SubstitutionTemplate the template for the debug section
	 */
	public $tpl;
	
	/**
	 * Enable or disable all debugs
	 * @var boolean
	 */
	public $status = false;
	
	/**
	 * Wrap the var_dump into an html comment
	 */
	const COMMENT = 1;
	
	/**
	 * Print a well visible H1 and the dump is wrapped in a pre
	 */
	const H1_PRE = 2;
	
	/**
	 * Print a well visible H1, use a pre as wrapper for the variable 
	 * dump and then stop the execution of the script
	 */
	const H1_PRE_DIE = 3;
	
	/**
	 * Initializes the default settings
	 */
	private function __construct(){
		$this->tpl = $tpl = <<<EOF
		<div class="debug">
			<h1>%title%</h1>
			<pre>%debug%</pre>
		</div>
EOF;
		$this->set_level(self::COMMENT);
		
		if(WP_DEBUG === true)
			$this->debug_deprecated();
	}
	
	/**
	 * This object is a Singleton.
	 * This method gets the instance of it.
	 * @return DebugUtils the single instance of the class
	 */
	public static function get_instance(){
      if(self::$instance == null){
         self::$instance = new self;
      }
      
      return self::$instance;
	}
	
	/**
	 * Sets the level of output.
	 * Use DebugUtils::SOFT DebugUtils::H1_PRE or DebugUtils::H1_PRE_DIE
	 * @param int $level the level
	 * @return DebugUtils for chaining
	 */
	public function set_level($level){
		$this->level = $level;
		return $this;
	}
	
	/**
	 * Retrieves the current level of echo
	 * @return the current level of echo
	 */
	public function get_level(){
		return $this->level;
	}
	
	/**
	 * Prints the dump for the given $var
	 * @param mixed $var the variable to be dumped
	 */
	public function debug($var){
		if(!$this->status) return '';
		
		$render = str_replace(
			array(	'%debug%',								'%title%'), 
			array(	var_export($var, true), 	$this->title), 
			$this->tpl
		);
		
		switch($this->level){
			default:
			case self::COMMENT:
				echo '<!-- '.$render.' -->';
				break;
				
			case self::H1_PRE:
				echo $render;
				break;
				
			case self::H1_PRE_DIE:
				die($render);
				break;
		}
		
		return $render;
	}
	
	/**
	 * Sets the title of the box
	 * @param string $title
	 * @return DebugUtils for chaining
	 */
	public function set_title($title){
		$this->title = $title;
		return $this;
	}
	
	/**
	 * Dumps the $wp_scripts global variable
	 */
	public function dump_assets(){
		global $wp_scripts;
		$this->debug($wp_scripts);
	}
	
	/**
	 * Debug the assets list on the bottom of the page
	 */
	public function debug_assets(){
		add_action('shutdown', array(&$this, 'dump_assets'));
	}
	
	/**
	 * Dumps the stack trace when a deprecated function is encountered
	 * @param string $function function name
	 * @param string $message deprecation message
	 * @param string $version first version with deprecated entry
	 * @todo: need improvements on readability
	 */
	public function dump_deprecated($function, $message, $version){
		error_log ('Deprecated Argument Detected');
		$trace = debug_backtrace();
		$this->debug($trace);
		foreach ($trace as $frame) {
			error_log (var_export ($frame, true));
		}
	}
	
	/**
	 * Enables debugging of deprecated functions
	 */
	public function debug_deprecated(){
		add_action('deprecated_function_run', array(&$this, 'dump_deprecated'), 10, 3);
	}
	
}




if(!function_exists('vd')):
/**
 * Quick and dirty way to know a variable value
 * vd stays for <b>v</b>ar_dump() and <b>d</b>ie()
 * @param mixed $var the variable to be dumped
 * @package debug
 * @version 1.0.0
 */
function vd($var){
	DebugUtils::get_instance()
		->set_level(DebugUtils::H1_PRE_DIE)
		->set_title(__('Debug'))
		->debug($var);
}
endif;

if(!function_exists('v')):
/**
 * Quick and dirty way to know a variable value
 * Usefull in a loop cause it doesn't break the execution with die
 * @param mixed $var the variable to be dumped
 * @package debug
 * @version 1.0.0
 */
function v($var){
	DebugUtils::get_instance()
		->set_level(DebugUtils::H1_PRE)
		->set_title(__('Debug'))
		->debug($var);
}
endif;

if(!function_exists('vc')):
/**
 * Quick and dirty way to know a variable value in a production enviroment
 * vc stays for <b>v</b>ar_dump() on a <b>c</b>omment
 * @param mixed $var the variable to be dumped
 * @package debug
 * @version 1.0.0
 */
function vc($var){
	DebugUtils::get_instance()
		->set_level(DebugUtils::COMMENT)
		->set_title(__('Debug'))
		->debug($var);
}
endif;

if(!function_exists('debug')):
/**
 * Quick and dirty way to know a variable value.
 * It uses the last changed mode.
 * @param mixed $var the variable to be dumped
 * @package debug
 * @version 1.0.0
 */
function debug($var){
	DebugUtils::get_instance()
		->set_title(__('Debug'))
		->debug($var);
}
endif;

if(!function_exists('debug_assets')):
/**
 * Quick and dirty way to know the assets list
 * at the end of the page
 * @package debug
 * @version 1.0.0
 */
function debug_assets(){
	DebugUtils::get_instance()
		->set_title(__('Assets'))
		->debug_assets();
}
endif;

if(!function_exists('debug_deprecated')):
/**
 * Quick and dirty way to know the stack trace of a deprecated function
 * @package debug
 * @version 1.0.0
 */
function debug_deprecated(){
	DebugUtils::get_instance()
		->set_title(__('Deprecated'))
		->debug_deprecated();
}
endif;