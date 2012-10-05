<?php 

/**
 * Manages a substitution set
 * @author etessore
 * @version 1.0.0
 */
class SubstitutionTemplate{
	
	/**
	 * @var array Stores some static html
	 */
	public $static_markup;
	
	/**
	 * @var string the substitution template
	 */
	public $tpl;
	
	/**
	 * Initializes the current object with default parameters
	 */
	public function __construct(){
		
	}
	
	/**
	 * Sets the substitutions template
	 * @param strin $tpl the template
	 */
	public function set_tpl($tpl){
		$this->tpl = $tpl;
		return $this;
	}
	
	/**
	 * Set the static markup; ie: prev\next\loading divs
	 * @param string $markup html markup
	 * @return SubstitutionTemplate $this for chainability
	 */
	public function set_markup($key, $markup){
		$this->static_markup[$key] = $markup;
		return $this;
	}
	
	/**
	 * Replaces the markup in $this->tpl %tag%s with the one
	 * in the corresponding value of $this->static_markup[tag].
	 */
	public function replace_markup(){
		return str_replace(
			array_map(
				create_function('$k', 'return "%".$k."%";'),
				array_keys($this->static_markup)
			),
			array_values($this->static_markup),
			$this->tpl
		);
	}
}