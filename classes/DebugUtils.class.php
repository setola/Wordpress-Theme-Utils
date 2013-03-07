<?php 
/**
 * Stores DebugUtils class definition
 */

/**
 * Utils to debug the code while writing it.
 * @author Emanuele 'Tex' Tessore
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
	 * @var unknown_type the template for the debug section
	 */
	public $template;
	
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
		if(!WORDPRESS_THEME_UTILS_DEBUG) return '';
		
		$render = str_replace(
			array(	'%debug%',								'%title%'), 
			array(	var_export($var, true), 	$this->title), 
			$this->tpl
		);
		
		switch($this->level){
			default:
			case self::COMMENT:
				$render = '<!-- '.$render.' -->';
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
	
}




if(!function_exists('vd')):
/**
 * Quick and dirty way to know a variable value
 * vd stand for <b>v</b>ar_dump() and <b>d</b>ie()
 * @param mixed $var the variable to be dumped
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
 * vc stand for <b>v</b>ar_dump() on a <b>c</b>omment
 * @param mixed $var the variable to be dumped
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
 */
function debug($var){
	DebugUtils::get_instance()
		->set_title(__('Debug'))
		->debug($var);
}
endif;