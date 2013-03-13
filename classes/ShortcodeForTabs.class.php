<?php 

/**
 * Stores ShortcodeForTabs class definition
 */

/**
 * Manages tabs design with shortcodes.
 * 
 * Use [tab]<some text>[/tab] to wrap the content
 * of a single tag and then [tabs-list] for the
 * list of the tabs in the current post content.
 * 
 * These optional parameter are available:
 * 	icon	=	string|int number if you want a incon from the media library, string for the <img> tag src attribute
 * 	class	=	string class attribute for the current tab div
 * 	title	=	string the title of the tab
 * 	list	=	boolean set to false if you want to remove the current entry from the tabs list
 * 
 * @author etessore
 * @version 1.0.0
 * @since 1.0
 */
class ShortcodeForTabs{
	/**
	 * Stores the shortcode used for wrapping a single tab
	 * @var string
	 */
	public static $tab_shortcode = 'tab';
	
	/**
	 * Stores the shortcode for placing the tabs lis
	 * @var unknown_type
	 */
	public static $list_shortcode = 'tabs_list';
	
	/**
	 * Substitution template for the single tab
	 * @var SubstitutionTemplate
	 */
	public $tab_tpl;
	
	/**
	 * Substitution template for the tabs list
	 * @var SubstitutionTemplate
	 */
	public $list_tpl;
	
	/**
	 * Stores the list of tabs for the current post
	 * @var array
	 */
	private $list_of_entries = array();
	
	/**
	 * Stores the number of tabs for the current post
	 * @var int
	 */
	private $number_of_entries = 0;
	
	/**
	 * Initializes the istance with default values
	 */
	public function __construct(){
		$this->add_shordcode();
		add_filter('the_content', array(&$this, 'build_list'), 7);
		$this->tab_tpl = new SubstitutionTemplate();
		$this->tab_tpl->set_tpl('<section%id%%class%>%icon%%content%</section>');
	}
	
	/**
	 * Register the needed shortcodes with WordPress subsystem
	 */
	private function add_shordcode(){
		add_shortcode(self::$tab_shortcode, array(&$this, 'tab_hook'));
		add_shortcode(self::$list_shortcode, array(&$this, 'list_hook'));
	}
	
	/**
	 * Deletes the shortcodes used in this feature
	 */
	private function delete_shortcode(){
		remove_shortcode(self::$tab_shortcode, array(&$this, 'tab_hook'));
		remove_shortcode(self::$list_shortcode, array(&$this, 'list_hook'));
	}
	
	/**
	 * This is called back by wordpress when a tab shortcode is found
	 * @param unknown_type $atts
	 * @param string $content the content wrapped into the shortcode
	 */
	public function tab_hook($atts, $content = null){
		$parms = shortcode_atts( array(
			'icon'	=>	'',
			'class'	=>	'tab',
			'title'	=>	'tab-entry_'.$this->number_of_entries,
			'list'	=>	true
		), $atts );
		
		if($parms['list'] !== 'false'){
			// let's maintain a list of tabs found in the current post
			// so we don't have to rebuild in the list_hook()
			$this->number_of_entries++;
			$this->list_of_entries[$parms['title']] = $parms;
			$this->list_of_entries[$parms['title']]['content'] = $content;
		}
		
		ThemeHelpers::load_js('tabs');
		
		// render the html and return it to WordPress
		return $this->tab_tpl
			->set_markup('id', 		' id="'.$parms['title'].'" ')
			->set_markup('class', 	' class="'.$parms['class'].'" ')
			->set_markup('icon', 	$this->get_image($parms['icon']))
			->set_markup('content',	do_shortcode($content))
			->replace_markup();
		
	}
	
	/**
	 * Get the image for the tab.
	 * 
	 * build the image for this tab: take it from the media
	 * library if the parameter is a number, use such parameter
	 * as src attribute for img tag if it's not a number.
	 * @param int|string $parm id of the image or string of the src parameter 
	 */
	private function get_image($parm){
		if(empty($parm)) return '';
		return (is_numeric($parms['icon']))
			? wp_get_attachment_image($parms['icon'])
			: HtmlHelper::image($parms['icon']);
	}
	
	/**
	 * This is called back by WordPress when the tab list shortcode is found
	 * @param array $atts User defined attributes in shortcode tag.
	 */
	public function list_hook($atts){
		$parms = shortcode_atts(array(
			'class'	=>	'tabs',
		), $atts);
		
		$inner_html = array();
		foreach($this->list_of_entries as $entry){
			$inner_html[] = HtmlHelper::anchor(
				'#'.$entry['title'], 
				$this->get_image($entry['icon']).$entry['title'],
				array('data-title'=>$entry['title'])
			);
		}
		
		global $post;
		//do_shortcode(get_the_content());
		
		return HtmlHelper::unorderd_list($inner_html, array('class'=>$parms['class']));
	}
	
	/**
	 * Builds the list of tabs.
	 * 
	 * This is called to be sure we don't leave no tab behind
	 * Thanks to Viper007Bond: @link http://www.viper007bond.com/2009/11/22/wordpress-code-earlier-shortcodes/
	 * @param string $content the post content
	 */
	public function build_list($content){
		global $post, $shortcode_tags;
		
		// Backup current registered shortcodes and clear them all out
   		$orig_shortcode_tags = $shortcode_tags;
    	remove_all_shortcodes();
    	
    	// Do the shortcode (only the one above is registered)
    	$this->add_shordcode();
		do_shortcode(get_the_content());
		
    	// Put the original shortcodes back
    	$shortcode_tags = $orig_shortcode_tags;
		
    	return $content;
	}
}