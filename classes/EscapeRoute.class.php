<?php 
/**
 * Manages the navigation links on a 404 page
 * @author etessore
 * @version 1.0.1
 * 
 * Changelog
 * 1.0.1
 * 	moved from ThemeHelpers to HtmlHelper class
 */
class EscapeRoute{
	public $links = array();
	public $tpl;
	public $templates;
	public $list_separator = '';
	
	/**
	 * Initializes the object to the default values
	 */
	public function __construct(){
		$tpl = <<<EOF
		<h1>%title%</h1>
		<h2>%subtitle%</h2>
		<p>%desc%</p>
		%list%
EOF;
		$this->templates = new SubstitutionTemplate();
		$this->templates
			->set_tpl($tpl)
			->set_markup('title', __('The page was not found', 'theme'))
			->set_markup('subtitle', __('I\'m sorry but the page you were looking for is not available', 'theme'))
			->set_markup('desc', __('Maybe one of these links will be usefull', 'theme'));
		
		$this
			->set_template($tpl)
			->set_separator(' - ')
			->add_link('javascript:history.back();', __('Back', 'theme'))
			->add_link(home_url(), __('Homepage', 'theme'))
			->add_link('javascript:void();', __('Book Now', 'theme'), 'class="book-action"');
	}
	
	/**
	 * Adds a link
	 * @param string $href the url to be linked
	 * @param string $label the inner html for the <a> tag
	 * @param string|array $params additional parameters for the <a>
	 * @return EscapeRoute $this for chainability
	 */
	public function add_link($href, $label, $params=''){
		$tmp 			= new stdClass();
		$tmp->href 		= $href;
		$tmp->label 	= $label;
		$tmp->params 	= $params;
		$this->links[$href] = $tmp;
		return $this;
	}
	
	/**
	 * Set the html template
	 * @param string $tpl the template
	 * @return EscapeRoute $this for chainability
	 */
	public function set_template($tpl){
		$this->tpl = $tpl;
		return $this;
	}
	
	/**
	 * Set the separator for the list of anchors
	 * @param string $separator html between every <a>
	 */
	public function set_separator($separator){
		$this->list_separator = $separator;
		return $this;
	}
	
	/**
	 * @return the rendered the list of anchors
	 */
	protected function render_list(){
		$toret = '';
		$first = true;
		foreach($this->links as $link){
			if(!$first){
				$toret .= $this->list_separator;
			}
			$toret .= HtmlHelper::anchor($link->href, $link->label, $link->params);
			$first = false;
		}
		return $toret;
	}
	
	/**
	 * @returns the markup
	 */
	public function get_markup(){
		return $this->templates
			->set_markup('list', $this->render_list())
			->replace_markup();
	}
}