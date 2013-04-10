<?php 
/**
 * Stores the definition for class SubstitutionTemplate
 */

/**
 * Manages a string substitution set
 * @author etessore
 * @version 1.0.0
 * @package classes
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
	 * @param string $tpl the template
	 */
	public function set_tpl($tpl){
		$this->tpl = $tpl;
		return $this;
	}
	
	/**
	 * Set the static markup; ie: prev\next\loading divs
	 * @param string|array $key the searches to be substituted
	 * @param string|array $markup html markups
	 * @return SubstitutionTemplate $this for chainability
	 * @throws Exception if $key and $markup have different number of elements
	 */
	public function set_markup($key, $markup){
		$key = (array) $key;
		$markup = (array) $markup;
		
		if(count($markup) == 1 && count($key) > 1){
			foreach($key as $k => $v){
				$this->static_markup[$v] = $markup[0];
			}
		} elseif(count($markup) != count($key)){
			throw new Exception('$key and $markup have different number of elements'); 
		} else {
			foreach($key as $k => $v){
				$this->static_markup[$v] = $markup[$k];
			}
		}
		
		//$this->static_markup[$key] = $markup;
		return $this;
	}
	
	/**
	 * Bulk set the key:markup pairs
	 * @param array $ass_array an associative array of keys and markups
	 */
	public function set_multi_markup($ass_array){
		$ass_array = (array) $ass_array;
		$this->set_markup(array_keys($ass_array), array_values($ass_array));
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