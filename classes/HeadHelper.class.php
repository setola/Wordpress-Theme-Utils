<?php 
class HeadHelper{
	
	/**
	 * @var array of name => content valuse for the meta tags.
	 */
	public $meta_tags = array();
	
	/**
	 * @var array the links to be added
	 */
	public $links = array();
	
	/**
	 * @var string the title
	 */
	public $title;
	
	/**
	 * @var the charset of the page
	 */
	public $charset;
	
	/**
	 * @var string Google Analytics tracking code
	 */
	public $ua;
	
	
	/**
	 * Initializes this object to default data
	 */
	public function __construct(){
		$tempate_directory_uri = get_template_directory_uri();
		$title = get_bloginfo('name') . wp_title(null, false);
		$description = get_bloginfo('description');
		
		$this
			->set_title($title)
			->set_meta_tag(array('name'=>'description', 'content'=>$description))
			->set_meta_tag(array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'))
			->set_charset(get_bloginfo('charset'))
			->set_link(
				array(
					'rel'=>'shortcut icon', 
					'href'=>"$tempate_directory_uri/images/favicon.png"
				)
			);
	}
	
	/**
	 * @return some usefull meta tags for the <head>
	 */
	public function get_head(){
		$meta_tags = $this->render_meta_tags();
		$title = esc_html($this->title);
		$desc = $this->render_meta_tag('description');
		$ga_tracking = '';
		if(!empty($this->ua)){
		$ga_tracking = HtmlHelper::script(<<< EOF
     var _gaq = _gaq || [];
     _gaq.push(['_setAccount', '{$this->ua}'],
     ['_setDomainName', 'none'],
     ['_setAllowLinker', true ],
     ['_trackPageview'],
     ['_trackPageLoadTime'],
     ['second._setAccount', 'UA-4717938-7'],
     ['second._setDomainName', 'none'],
     ['second._trackPageview'],
     ['second._trackPageLoadTime']
      );
     (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
EOF
		);

		echo <<<EOF
	<title>$title</title>
	$desc
    <meta charset="{$this->charset}">
    $meta_tags
    <link rel="shortcut icon" href="$tempate_directory_uri/images/favicon.png">
EOF;
		}
	}
	
	/**
	 * Sets the Google Analytics tracking code
	 * @param string $ga UA-XXXXXX
	 */
	public function set_ga($ga){
		$this->ua = $ga;
		return $this;
	}
	
	/**
	 * Print the markup
	 */
	public function the_head(){
		echo $this->get_head();
	}
	
	/**
	 * Set a meta tag 
	 * @param array $meta the meta tag 
	 * @return HeadHelper $this for chainability
	 */
	public function set_meta_tag($meta){
		if(isset($meta['name'])){
			$this->meta_tags[$meta['name']] = $meta;
		}
		return $this;
	}
	
	/**
	 * Set the title
	 * @param string $title the title
	 */
	public function set_title($title){
		$this->title = $title;
		return $this;
	}
	
	/**
	 * Set a link
	 * @param array $link the link
	 */
	public function set_link($link){
		if(isset($link['name'])){
			$this->link[$link['name']] = $link;
		}
		return $this;
	}
	
	/**
	 * Remove a link
	 * @param string $link the link name
	 */
	public function delete_link($link){
		unset($this->links[$link]);
		return $this;
	}
	
	/**
	 * Delete a meta tag
	 * @param string $name the meta name
	 */
	public function delete_meta_tag($name){
		unset($this->meta_tags[$name]);
		return $this;
	}
	
	/**
	 * Retrieve the meta tags list ready to be inserted into <head>
	 */
	public function render_meta_tags(){
		$toret = '';
		foreach($this->meta_tags as $name => $content){
			if($name != 'description'){
				$toret .= $this->render_meta_tag($name);
			}
		}
		return $toret;
	}
	
	/**
	 * Render a single meta tag of the current set stored in $this->meta_tags
	 * @param string $name the name of the meta
	 */
	private function render_meta_tag($name){
		if(!isset($this->meta_tags[$name])) return '';
		return  
			'<meta name="'.$this->meta_tags[$name]['name']
			.'" content="'.$this->meta_tags[$name]['content'].'">'."\n";
	}
	
	/**
	 * Set the charset for the page
	 * @param string $charset the charset
	 */
	public function set_charset($charset){
		$this->charset = $charset;
		return $this;
	}
}