<?php 
/**
 * Contains ForceDownload class definition
 */

/**
 * Force an attachment to be downloaded
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class ForceDownload{
	
	/**
	 * Enables the force to download feature
	 * @param bool $priv
	 * @param bool $nopriv
	 */
	public function __construct($priv=true, $nopriv=true){
		if($priv) add_action('wp_ajax_download', array(&$this, 'force_download'));
		if($nopriv) add_action('wp_ajax_nopriv_download', array(&$this, 'force_download'));
	}

	/**
	 * AJAX callback for forcing the download of a file
	 */
	public static function force_download(){
		if(!self::check_request()){
			wp_die('Unauthorized Resource');
		}

		$file = get_attached_file($_REQUEST['id']);
		if(file_exists($file)){
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename= " . basename($file));
			header("Content-Transfer-Encoding: binary");
			die(file_get_contents($file));
		} else {
			header('HTTP/1.0 404 Not Found');
		}
	}

	/**
	 * Tests if the $_REQUEST[] field is valid
	 * Overload this to insert more checks
	 */
	public static function check_request(){
		if(!is_numeric($_REQUEST['id'])){
			return false;
		}

		return true;
	}

	/**
	 * Calculates the url for the attachment with id $id
	 * @param int $id the attachment id
	 * @param string $label the text inside the <a> tag
	 * @param array $parms additional parameters for the <a> tag 
	 * @see HtmlHelper::anchor()
	 * @return string an <a> tag
	 */
	public function force_download_anchor($id, $label, $parms){
		return HtmlHelper::anchor(admin_url('admin-ajax.php').'?action=download&id='.$id, $label, $parms);
	}
}