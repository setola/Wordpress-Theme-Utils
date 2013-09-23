<?php 

class TemplateChecker{
	private $include = array();
	private $exclude = array();
	
	function __construct($include, $exclude){
		$this
			->set_include($include)
			->set_exclude($exclude);
	}
	
	public function set_include($include){
		$this->include = (array)$include;
		return $this;
	}
	
	public function set_exclude($exclude = array()){
		$this->exclude = (array)$exclude;
		return $this;
	}
	
	protected function is_excluded($template_name){
		return in_array($template_name, (array)$this->exclude);
	}
	
	protected function is_included($template_name){
		return in_array($template_name, (array)$this->include);
	}
	
	public function check($template_name){
		if(empty($this->include) && empty($this->exclude)) return true;
		if($this->is_included($template_name)) return true;
		if($this->is_excluded($template_name)) return false;
		if(empty($this->include)) return true;
		return false;
	}
	
	
}