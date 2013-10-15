<?php 

/**
 * Stores the EmptyAssets class definition
 */

/**
 * Creates an asset object withou any registered javascript nor css
 * 
 * This is useful if you really want to optimize js and css
 * for your template
 */
class EmptyAssets extends DefaultAssets{
	public function register_custom(){return $this;}
	public function register_standard(){return $this;}
}