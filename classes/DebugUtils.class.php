<?php 

/**
 * Utils to debug the code while writing it.
 * @author Emanuele 'Tex' Tessore
 */
class DebugUtils {
	
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
	public function __construct(){
		$this->tpl = $tpl = <<<EOF
		<div class="debug">
			<h1>%title%</h1>
			<pre>%debug%</pre>
		</div>
EOF;
		$this->set_level(self::COMMENT);
	}
	
	/**
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
				
				break;
				
			case self::H1_PRE_DIE:
				die($render);
				break;
		}
		
		return $render;
	}
	
}




if(!function_exists('vd')):
/**
 * Quick and dirty way to know a variable value
 * vd stand for <strong>v</strong>ar_dump() and <strong>d</storng>ie()
 * @param mixed $var the variable to be dumped
 */
function vd($var){
	DebugUtils::get_instance()
		->set_level(DebugUtils::H1_PRE_DIE)
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
		->debug($var);
}
endif;

if(!function_exists('vc')):
/**
 * Quick and dirty way to know a variable value in a production enviroment
 * vc stand for <strong>v</strong>ar_dump() on a <strong>c</storng>omment
 * @param mixed $var the variable to be dumped
 */
function vc($var){
	DebugUtils::get_instance()
		->set_level(DebugUtils::COMMENT)
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
	DebugUtils::get_instance()->debug($var);
}
endif;