<?php 

/**
 * Helper class useful to generate some html tags
 * @author etessore
 * @version 1.0.0
 */
class HtmlHelper extends HtmlBuilder{
	
	/**
	 * Retrieves a <script> tag
	 * @param string $content the inner script content
	 * @param array $parms additional parameters
	 */
	public static function script($content, $parms=array()){
		$parms['type'] 	= 'text/javascript';
		return self::standard_tag('script', $content, $parms);
	}
	
	/**
	 * Get the markup for a <img> tag
	 * @param string $src the image source
	 * @param array $parms additional parameters
	 */
	public static function image($src, $parms=array()){
		$parms['src'] 	= esc_attr($src);
		return self::standard_tag('img', '', $parms);
	}
	
	/**
	 * Get the markup for an <a> tag
	 * @return the markup for an html <a> tag
	 * @param string $href the url to be pointed
	 * @param string $label the text
	 * @param array $parms some html attributes in key=>value pairs or a plain string
	 */
	public static function anchor($href, $label, $parms=array()){
		$parms['href'] = $href;
		return self::standard_tag('a', $label, $parms);
	}
	
	/**
	 * Retrieves a <li> tag
	 * @param string $inner_html the inner html
	 * @param array $parms the optional attributes for the <li>
	 */
	public static function list_item($inner_html, $parms=array()){
		return self::standard_tag('li', $inner_html, $parms);
EOF;
	}
	
	/**
	 * Prepare the inner html for a list (ul\ol).
	 * If it is a string it will be returnes as it is.
	 * If it is an array every element will be wrapped in a <li> tag
	 * @param string|array $inner_html the inner html
	 */
	private static function list_inner_html($inner_html){
		$toret = '';
		if(is_array($inner_html)){
			foreach ($inner_html as $list_item){
				$toret .= self::list_item($list_item);
			}
		} else {
			$toret .= $inner_html;
		}
		return $toret;
	}
	
	/**
	 * Retrieves a <ul> tag
	 * @param string|array $inner_html the inner html.
	 * If an array is passed, every element will be wrapped inside a <li> tag
	 * @param string|array $parms the optional attributes for the <ul>
	 */
	public static function unorderd_list($inner_html, $parms=''){
		return self::standard_tag('ul', self::list_inner_html($inner_html), $parms);
	}
	
	/**
	 * Retrieves a <ol> tag
	 * @param string|array $inner_html the inner html.
	 * If is an array every element will be brapped inside a <li> tag
	 * @param string|array $parms the optional attributes for the <ol>
	 */
	public static function ordered_list($inner_html, $parms=''){
		return self::standard_tag('ol', self::list_inner_html($inner_html), $parms);
	}
}


/**
 * Inner class to build some very basic html nodes
 * @author etessore
 * @version 1.0.0
 */
class HtmlBuilder {
	/**
	 * @var string value of the version of the html/xhtml used
	 * useful to have different behavior if not using HTML 5
	 */
	const HTML_VERSION = 'html5';
	
	/**
	 * @return array a list of self-closing tags
	 */
	public static function self_closing_tags(){
		return array('br', 'hr', 'img');
	} 
	
	/**
	 * Gets the opening html tag for the given one
	 * @return string the opening tag
	 * @param string $tag the html tag
	 * @param array $params the additional parameters
	 */
	public static function open_tag($tag='', $params = array()){
		if(empty($tag)) return '';
		return '<' . $tag . self::params($params) . '>';
	}
	
	/**
	 * Gets the closing html tag for the given one
	 * @return string the closing tag
	 * @param string $tag the html tag
	 */
	public static function close_tag($tag=''){
		if(empty($tag)) return '';
		if(in_array($tag, self::self_closing_tags())) return '';
		return "</$tag>";
	}
	
	/**
	 * Prepare the $parms to be printed as html attributes
	 * @param array $parms list of html attributes
	 */
	protected static function params($parms=array()){
		$parms 	= trim(self::array_to_html_attributes('=', $parms));
		if(!empty($parms)) $parms = ' '.$parms;
		return $parms;
	}
	
	/**
	 * Generates HTML Node Attribures
	 * @param string $glue
	 * @param array|string $pieces
	 * @return string
	 * @author http://blog.teknober.com/2011/04/13/php-array-to-html-attributes/
	 */
	protected static function array_to_html_attributes($glue, $pieces) {
		$str = $pieces;
		if (is_array($pieces)) {
			$str = " ";
			foreach($pieces as $key => $value) {
				if (strlen($value) > 0) {
					$str .= esc_attr($key) . esc_attr($glue) . '"' . esc_attr($value) . '" ';
				}
			}
		}
	
		return rtrim($str);
	}
	
	/**
	 * Retrives the attributes list choosing between the given parameters
	 * 
	 * @param string|array $inner_html the inner html
	 * @param array $params the parameters
	 * @return string the html tag attributes ready to be printed in the DOM
	 */
	protected static function get_html_attributes($inner_html, $params=array()){
		if(is_array($inner_html) && isset($inner_html['params'])){
			$params = $inner_html['params'];
		}
		$params = trim(self::array_to_html_attributes('=', $parms));
		if(!empty($params)) $params = ' '.$params;
		return $params;
	}
	
	/**
	 * Retrieves the correct DOCTYPE
	 * @param string $type the type, default is html5
	 */
	public static function doctype() {
		$doctypes = array(
				'html5'			=> '<!DOCTYPE html>',
				'xhtml11'		=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
				'xhtml1-strict'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
				'xhtml1-trans'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
				'xhtml1-frame'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
				'html4-strict'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
				'html4-trans'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
				'html4-frame'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		);
	
		if (isset($doctypes[self::HTML_VERSION])) {
			return $doctypes[self::HTML_VERSION];
		}
		return '';
	}
	
	/**
	 * Builds a default tag structure: <tagname tagparameters>inner_html</tagname>
	 * @param string $tag the tag
	 * @param string $inner_html the inner html
	 * @param array $parms the additional html tag parameters
	 */
	public static function standard_tag($tag, $inner_html = '', array $parms = array()){
		return 
			self::open_tag($tag, $parms) 
			. $inner_html 
			. self::close_tag($tag);
	}
	
}