<?php

/**
 * Class SpecialOffersSnippedCached
 * Manages the cache subsystem for the special offers snippet
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class SpecialOffersSnippetCached extends SpecialOffersSnippet{
    /**
     * Prefix of the transient where we store the html markup
     */
    const TRANSIENT_CACHE = 'SO_cache_';

    /**
     * Prefix of the transient where we store the last update unix timestamp
     */
    const TRANSIENT_LAST_UPDATE = 'SO_lastupdate_';

    /**
     * @var string stores the HID
     */
    public $hid;

    /**
     * @var string stores the unique id of the cache
     */
    protected $unique;

    public function __construct($hid){
        $this->hid = $hid;
        parent::__construct($hid);
        $this->tpl = <<< EOF
	%pre%
	<div class="offers-container">
		<div id="%divdest%"%option_divdest%>
			%offers_list%
		</div>
		%controls%
		<div class="">
			<a href="javascript:;" class="offers-toggler"></a>
		</div>
	</div>
	%jsfix%
	%post%
EOF;
        $this->templates
            ->set_tpl($this->tpl)
            ->set_markup('jsfix', '');
    }


    protected function get_uniq(){
        return md5(http_build_query($this->params));
    }

    /**
     * Purges the cache, fetches new data and stores them into the cache
     */
    public function refresh(){
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $this->get_iframe_src());
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($handler);
        curl_close($handler);

        preg_match("/<body[^>]*>(.*?)<\/body>/is", $html, $matches);

        set_transient(self::TRANSIENT_CACHE.$this->get_uniq(), $matches[1]);
        set_transient(self::TRANSIENT_LAST_UPDATE.$this->get_uniq(), time());
    }

    /**
     * Prints some javascript to simulate the iframe
     * loaded event while serving the cache
     */
    public function javascript_fix(){
        add_action('wp_footer', array(&$this, 'on_footer_scripts'));
        //return HtmlHelper::script('', array('src'=>'http://hotelsitecontents.fastbooking.com/js/fb.js'));
        $toret = <<<EOF

    var FB                  = FB || {};
    FB.Loader               = {};
    FB.Loader.eventType     = {
        BEFORE_LOADING: 0,
        AFTER_LOADING: 1
    };
    FB.Loader.Events        = [];
    FB.Loader.attachEvent   = function (e, t) {
        this.Events.push(t);
    };

EOF;
        return HtmlHelper::script($toret);
    }

    /**
     * Adds some javascripts to maintain compatibility with iframe style implementation
     * It's called by Wordpress on footer
     */
    public function on_footer_scripts(){
        ?>
            <script>
                jQuery(document).ready(function(){
                    jQuery.each(FB.Loader.Events, function(index, element){
                        if(typeof(element.onNotify != 'undefined')){
                            element.onNotify();
                        }
                    });
                });
            </script>
        <?php
    }

    /**
     * Retrieves the markup for the offers
     * @return string html markup
     */
    public function get_markup(){
        $cache = get_transient(self::TRANSIENT_CACHE.$this->get_uniq());

        if(!isset($_REQUEST['refresh']) && $cache){
            if(class_exists('TestManager')){
                TestManager::get_instance()->add_parameter('offersSnippetCacheAge',time()-get_transient(self::TRANSIENT_LAST_UPDATE.$this->get_uniq()));
            }
            return $this->templates
                ->set_markup('offers_list', $cache)
                ->set_markup('divdest', $this->get_param('divdest'))
                ->set_markup('jsfix', $this->javascript_fix())
                ->replace_markup();
        } else {
            $this->refresh();
            $offers = new parent($this->hid);
            foreach($this->params as $k => $v){
                $offers->add_param($k, $v);
            }
            return $offers->get_markup();
        }
    }
}