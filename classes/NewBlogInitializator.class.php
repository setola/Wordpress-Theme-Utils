<?php
/**
 * Stores class for inserting initial datas into a new blog
 */


/**
 * Fills the brand new blog with starting data
 * 
 * @author etessore
 * @version 1.0.0
 * @package classes
 * 
 */
class NewBlogInitializator{
	
	/**
	 * In a MultiSite environment stores the blog id
	 * @var int
	 */
	private $blog_id;
	
	/**
	 * Stores the parameter for the new blog theme
	 * @var array
	 */
	private $theme;
	
	/**
	 * Stores the list of automatically created pages
	 * @var array
	 */
	private $pages = array();
	
	/**
	 * Hooks the initial data filling into WordPress
	 */
	public function hook(){
		add_action('wpmu_new_blog', array(&$this, 'init'), 10, 1);
	}
	
	/**
	 * Initializes the blog with starting data.
	 * 
	 * Overload this in a child ob ject if you need customization on starting data
	 * 
	 * @param int $blog_id the blog id
	 */
	public function init($blog_id){
		//make sure we are in the right blog!
		$this->blog_id = $blog_id;
		switch_to_blog($this->blog_id);
		
		$this->delete_default_posts();
		$this->add_theme();
		
		// create the main menu if not exists
		$top_menu_id = $this->create_menu('Main Menu');
		
		// and add some pages to it
		$this->add_page_to_menu($top_menu_id, $this->add_page('Home'));
		$this->add_page_to_menu($top_menu_id, $this->add_page('Rooms'));
		$this->add_page_to_menu($top_menu_id, $this->add_page('Location'));
	}
	
	/**
	 * Creates the menu with given name
	 * 
	 * If a menu with given name exists this method will return its id.
	 * If $location parameter is passed this method will assign the menu with given $name to such location
	 * 
	 * @param string $name the menu name
	 * @param string $location
	 * @return Ambigous <mixed, boolean, unknown, multitype:, NULL, WP_Error, object, array|object, string, number, error, multitype:mixed unknown , multitype:number mixed , multitype:number Ambigous <string, NULL> >
	 */
	public function create_menu($name, $location=null){
		$id = wp_get_nav_menu_object($name)
			? wp_get_nav_menu_object($name)
			: wp_create_nav_menu($name);
		
		if(!(is_null($location) || has_nav_menu($location))){
			$locations = get_theme_mod('nav_menu_locations');
			$locations[$location] = $id;
			set_theme_mod('nav_menu_locations', $locations);
		}
		
		return is_object($id) ? intval($id->term_id) : $id;
	}
	
	/**
	 * In a MultiSite environment selects the right blog
	 * @return NewBlogInitializator $this for chainability
	 */
	public function select_blog(){
		if(function_exists('switch_to_blog')){
			switch_to_blog($this->blog_id);
		}
		return $this;
	}
	
	/**
	 * Removes the default 'Hello World' post and page
	 * @return NewBlogInitializator $this for chainability
	 */
	public function delete_default_posts(){
		// make sure we are in the right blog!
		$this->select_blog();
		(wp_delete_post(1,true) && wp_delete_post(2,true)) ||  wp_die(__('Unable to remove sample data'));
		return $this;
	}

	/**
	 * Inserts a new page into WP
	 * @param string|array $parms page details according to {@link http://codex.wordpress.org/Function_Reference/wp_insert_post wp_insert_post()}
	 * @return int the post id
	 */
	public function add_page($parms=null){
		// make sure we are in the right blog!
		$this->select_blog();
		
		// pass a string as parameter and it will be the post title
		if(is_string($parms)){
			$parms = array('post_title' => $parms);
			$parms['post_name'] = sanitize_title($parms['post_title']);
		}
		
		// che for template existence
		if(!empty($parms['page_template'])){
			$parms['page_template'] = locate_template($parms['page_template']);
		}

		$defaults = array(
			'post_parent'	=>	0,
			'post_status'	=>	'publish',
			'post_title'	=>	'New Automatic Page',
			'post_name'		=>	'new-automatic-page',
			'page_template'	=>	'',
			'post_type'		=>	'page'
		);
		$args = wp_parse_args((array)$parms, $defaults);
		
		$args['post_id'] = wp_insert_post($args);

		if(!$args['post_id']) wp_die(sprintf(__('Unable to insert page "%s"'), $args['post_name']));
		
		$this->pages[$args['post_id']] = $args;
		return $args['post_id'];
	}
	
	/**
	 * Gets the automatically generated page details
	 * @param int $index the id of the page
	 * @return array list of details, false if the $index doesn't point to an automatically generated page
	 */
	public function get_page_details($index){
		return ($this->pages[$index]) ? $this->pages[$index] : false;
	} 
	
	/**
	 * Adds an automatically generated page to the given menu
	 * @param int $index the index of $this->pages array
	 * @param int $menu_id the menu id
	 * @return NewBlogInitializator $this for chainability
	 */
	public function add_menu_entry($index, $menu_id){
		if(!isset($this->pages[$index]))
			wp_die(sprintf(__('Unable to insert page number "%s" into menu %s: <br />This page wasn\'t created by automatic content filler subsystem!'), $index, $menu_id));
		
		$parms = array(
			'menu-item-title'		=>	$this->pages[$index]['post_title'],
			'menu-item-object'		=>	'page',
			'menu-item-object-id'	=>	$this->pages[$index]['post_id'],
			'menu-item-type'		=>	'post_type',
			'menu-item-classes'		=>	$this->pages[$index]['post_name'],
			'menu-item-status'		=>	'publish'
		);
		
		return wp_update_nav_menu_item($menu_id, 0, $parms);
	}
	
	/**
	 * Adds all automatically generated pages to the given menu
	 * @param int $menu_id the menu id 
	 * @param int $start the first element index
	 * @param int $end the last element index
	 * @return NewBlogInitializator $this for chainability
	 */
	public function add_menu_entries($menu_id, $start=0, $end=null){
		$end = is_null($end) ? count($this->pages) : $end;
		for($k=$start; $k<$end; $k++){
			$this->add_menu_entry($k, $menu_id);
		}
		
		return $this;
	}
	
	/**
	 * Resets the list of the automatically generated pages
	 * @return NewBlogInitializator $this for chainability
	 */
	public function reset_page_list(){
		$this->pages = array();
		return $this;
	}


	/**
	 * Creates the basic structure for a child theme.
	 *
	 * This method tries to make a new theme directory
	 * under the wp-contents/themes.
	 * Then tries to create style.css in the newly
	 * theme folder and fill it with the default header comment
	 *
	 * An example of parameters:
	 * <code>
	 * $defaults = array(
	 * 		'name' 			=>	'Wordpress Theme Utils Child Theme',
	 * 		'folder' 		=>	'wtu-child-theme',
	 * 		'child_of' 		=>	'Wordpress-Theme-Utils',
	 * 		'description' 	=>	'This is a Child Theme of Wordpress Theme Utils',
	 * 		'theme_uri'		=>	'http://www.emanueletessore.com/',
	 * 		'author'		=>	'Emanuele Tessore',
	 * 		'author_uri'	=>	'http://www.emanueletessore.com/',
	 * 		'version'		=>	'1.0.0'
	 * );
	 * </code>
	 *
	 * Uses {@link http://codex.wordpress.org/Function_Reference/wp_die wp_die()} if something goes wrong
	 *
	 * @param array $parms parameters for the new theme
	 * @return NewBlogInitializator $this for chainability
	 */
	public function add_theme($parms=null){
		$defaults = array(
			'name' 			=>	'Wordpress Theme Utils Child Theme',
			'folder' 		=>	'wtu-child-theme',
			'child_of' 		=>	'Wordpress-Theme-Utils',
			'description' 	=>	'This is a Child Theme of Wordpress Theme Utils',
			'theme_uri'		=>	'http://www.emanueletessore.com/',
			'author'		=>	'Emanuele Tessore',
			'author_uri'	=>	'http://www.emanueletessore.com/',
			'version'		=>	'1.0.0'
		);
		$args = wp_parse_args((array)$parms, $defaults);

		// remove spaces and html\php tags
		$new_dir_path = get_theme_root().'/'.sanitize_title($args['folder']);
		$new_style_path = $new_dir_path.'/style.css';

		// this is the default header for the style.css
		$new_style_content = <<< EOF
/*
THEME NAME:      {$args['name']}
THEME URI:       {$args['theme_uri']}
VERSION:         {$args['version']}
AUTHOR:          {$args['author']}
AUTHOR URI:      {$args['author_uri']}
DESCRIPTION:     {$args['description']}
TEMPLATE:        {$args['child_of']}
*/

@import url("../{$args['child_of']}/style.css");
EOF;

		// check if the directory we want to create exists
		if(is_dir($new_dir_path)){
			// if so we cannot proceed or we will overwrite another theme
			wp_die(sprintf(__('Unable to create the new theme:<br />Directory <code>%s</code> exists'), $new_dir_path));
		}

		// try to make the new directory
		if(mkdir($new_dir_path)===false){
			// we had an error while creating the directory, we cannot proceed
			wp_die(sprintf(__('Unable to create the new theme:<br />Error while creating the directory <code>%s</code>'), $new_dir_path));
		}

		// test if the style.css exists
		if(file_exists($new_style_path)){
			// if so we cannot proceed or we will overwrite the stylesheet
			wp_die(sprintf(__('Unable to create the new theme:<br />Error <code>%s</code> already exists'), $new_style_path));
		}

		// try to write the content into style.css
		if(file_put_contents($new_style_path, $new_style_content)===false){
			// if the content insertion failed we cannot proceed
			wp_die(sprintf(__('Unable to create the new theme:Error while writing <code>%s</code>'), $new_style_path));
		}

		// if we reached this point everything is gone the right way
		$this->theme = $args;
		return $this;
	}
	
	/**
	 * Enables the new theme for the current blog
	 * @return NewBlogInitializator $this for chainability
	 */
	public function enable_theme(){
		$this->select_blog();
		
		if(is_multisite()){
			// Enable Child Theme for this site only
			$allowed_themes = get_option('allowedthemes');
			
			if ( !$allowed_themes ){
				$allowed_themes = array( $folder => true );
			} else {
				$allowed_themes[$folder] = true;
			}
			
			update_option('allowedthemes', $allowed_themes);
		}
		
		// Activate child theme on this site
		switch_theme($this->theme['child_of'], $this->theme['folder']);
		
		return $this;
	}
	
	/**
	 * Deletes all pages
	 * 
	 * WARNING: it does NOT ask if you're sure!!!
	 * 
	 * @return NewBlogInitializator $this for chainability
	 */
	public function delete_all_pages(){
		$this->select_blog();
		
		$options = array(
				'numberposts'     => 50,
				'offset'          => 0,
				'orderby'         => 'post_date',
				'order'           => 'DESC',
				'post_type'       => 'page',
		);
		
		$posts = get_posts( $options );
		
		$offset = 0;
		while( count( $posts ) > 0 ) {
			if( $offset == 10 ) {
				break;
			}
			$offset++;
			
			foreach( $posts as $post ) {
				wp_delete_post( $post->ID, true );
			}	
			$posts = get_posts( $options );
		}
		
		return $this;
	}
}











