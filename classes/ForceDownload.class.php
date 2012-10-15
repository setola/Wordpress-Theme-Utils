<?php 


class ForceDownload{
	var $request_field;
	/**
	 * Enables the force to download feature
	 * @param bool $priv
	 * @param bool $nopriv
	 * @param string $request_field
	 */
	public function __construct(bool $priv=true, bool $nopriv=true, string $request_field='id'){
		if($priv) add_action('wp_ajax_download', array(&$this, 'force_download'));
		if($nopriv) add_action('wp_ajax_nopriv_download', array(&$this, 'force_download'));
		$this->request_field = $request_field;
	}

	/**
	 * AJAX callback for forcing the download of a file
	 */
	public function force_download(){
		if(!$this->check_request()){
			wp_die('Unauthorized Resource');
		}
		
		$post = get_post($_GET['id']);
		$file = get_attached_file($post->ID);
		if(file_exists($file)){
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename= " . basename($file));
			header("Content-Transfer-Encoding: binary");
			header("Content-Type: ". 
					(empty($post->post_mime_type) 
							? "application/octet-stream;" 
							: $post->post_mime_type
					)
			);
		
			die(file_get_contents($file));
		} else {
			header('HTTP/1.0 404 Not Found');
		}
	}

	/**
	 * Tests if the $_REQUEST[] field is valid
	 * Overload this to insert more checks
	 */
	public function check_request(){
		if(!is_numeric($_REQUEST[$this->request_field])){
			return false;
		}

		return true;
	}

	/**
	 *
	 * @param int $id
	 * @param string $label
	 * @param array|string $parms @see
	 */
	public function force_download_anchor($id, $label, $parms){
		return ThemeHelpers::anchor(admin_url('admin-ajax.php').'?action=download&'.$this->request_field.'='.$id, $label, $parms);
	}
}