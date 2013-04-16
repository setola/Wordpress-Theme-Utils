<?php
/**
 * Contains the LipsumGenerator 
 */

/**
 * Generates Lorem Ipsum text
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class LipsumGenerator {

	/**
	 * @const int use html tag <p> to separate paragraphs
	 */
	const FORMAT_HTML  = 0;

	/**
	 * @const int use 2 newlines to separate paragraphs 
	 * and a tab before the first word of every paragraph
	 */
	const FORMAT_TEXT  = 1;

	/**
	 * @const int generates plain text with a 
	 * newline as separation between paragraphs
	 */
	const FORMAT_PLAIN = 2;

	/**
	 * @const int generates html with <strong> <em> <a> and <p> tags
	 */
	const FORMAT_RICH_HTML = 3;
	
	/**
	 * @const string the WP option name where 
	 * the generator can store his config
	 */
	const OPTION_NAME = 'lipsum_generator_config';
	
	/**
	 * @const string the WP options name where 
	 * the generator can store the rendered text
	 */
	//const OPTION_NAME_RENDER = 'lipsum_generator_render';

	/**
	 * @var string the format type
	 */
	private $format;
	
	/**
	 * @var int number of paragraphs
	 */
	private $number_of_paragraphs;
	
	/**
	 * @var int words in a paragraph
	 */
	private $words_per_paragraph;
	
	/**
	 * @var int minimum amount of words until a single word can be repeated
	 */
	private $words_per_sentence;
	
	/**
	 * @var array all generated texts will start with thouse words (ex. 'Lorem Ipsum') 
	 */
	private $beginning;
	
	/**
	 * @var GeneratorDictionary a dictionary for the current generator
	 */
	private $dictionary;
	
	/**
	 * @var int the minimum amount of words until a single word can be repeated
	 */
	private $min_repeat_count;
	
	/**
	 * @var array config for rich html generator
	 */
	private $rich_html_config;
	
	/**
	 * @var mixed a seed for random generator
	 */
	private $seed;
	
	/**
	 * @var string the rendered text
	 */
	private $render;
	
	/**
	 * @var GaussianMath some math utils
	 */
	private $gaussian_math;
	//public $count;


	/**
	 * Look for a config stored in the db
	 * If it doesn't exist it will load the default one
	 * @return LipsumGenerator $this for chainability
	 */
	public function init(){
		if(!$this->load()){ 
			$this->defaults();
			$this->seed = rand(0, 1000000);
		}
		return $this;
	}
	
	/**
	 * Initializes the current object with default settings
	 * @return LipsumGenerator $this for chainability
	 */
	public function defaults(){
		return $this
			->set_format(self::FORMAT_RICH_HTML)
			->set_number_of_paragraphs(5)
			->set_words_per_paragraph(100)
			->set_words_per_sentence(24.460)
			->set_dictionary(new LoremIpsumDictionary)
			->set_begins_with(array('lorem', 'ipsum'))
			->set_min_repeat_count(5)
			->set_math(new GaussianMath);
	}

	/**
	 * Set the format for the current object
	 * use only self::FORMAT_* constants
	 * @see self::FORMAT_RICH_HTML
	 * @see self::FORMAT_HTML
	 * @see self::FORMAT_TEXT
	 * @see self::FORMAT_PLAIN
	 * @param int $format the format
	 * @return LipsumGenerator $this for chainability
	 */
	public function set_format($format){
		$format = intval($format);
		switch($format){
			case self::FORMAT_HTML:
			case self::FORMAT_PLAIN:
			case self::FORMAT_RICH_HTML:
			case self::FORMAT_TEXT:
				$this->format = $format;
				break;
			default:
				throw new InvalidArgumentException(sprintf("Unsupported format '%s'", $format));
				break;
		}
		return $this;
	}

	/**
	 * Sets the number of paragraphs for the current object
	 * @param int $number_of_paragraphs the number
	 * @return LipsumGenerator $this for chainability
	 */
	public function set_number_of_paragraphs($number_of_paragraphs){
		$this->number_of_paragraphs = $number_of_paragraphs;
		return $this;
	}
	
	/**
	 * Sets how many words a paragraph is made by.
	 * @param int $words_per_paragraph the number of words
	 */
	public function set_words_per_paragraph($words_per_paragraph){
		$this->words_per_paragraph = intval($words_per_paragraph);
		return $this;
	}
	
	/**
	 * Sets how many words a sentence is made by
	 * @param int $words_per_sentence number of words
	 * @return LipsumGenerator $this for chainability
	 */
	public function set_words_per_sentence($words_per_sentence){
		$this->words_per_sentence = intval($words_per_sentence);
		return $this;
	}
	
	/**
	 * Sets the first n words of the generated text.
	 * Usually they are 'Lorem ipsum'.
	 * @param array $beginning list of first n words
	 * @return LipsumGenerator $this for chainability
	 */
	public function set_begins_with($beginning){
		$this->beginning = (array) $beginning;
		return $this;
	}
	
	/**
	 * Sets the dictionary for the current generator
	 * @param GeneratorDictionary $dictionary the dictionary
	 * @return LipsumGenerator $this for chainability
	 */
	public function set_dictionary(GeneratorDictionary $dictionary){
		$this->dictionary = $dictionary;
		return $this;
	}
	
	/**
	 * Sets the mathematical object used for count words and punctuage distribution
	 * @param GaussianMath $math
	 */
	public function set_math(GaussianMath $math){
		$this->gaussian_math = $math;
		return $this;
	}
	
	/**
	 * Sets the minimum amount of words until a single word can be repeated
	 * @param $min_repeat_count the number of words withour repetitions
	 * @return LipsumGenerator $this for chainability
	 */
	public function set_min_repeat_count($min_repeat_count){
		$this->min_repeat_count = intval($min_repeat_count);
		return $this;
	}

	/**
	 * Retrieves an array of random words
	 * @return array a list of random words
	 * @param $count int the number of words to ber retrieved
	 */
	private function get_words($count){
		$toret = array();
		$words = $this->dictionary->get_all_words();

		while(count($toret)<$count){
			//$word  = $words[array_rand($words)];
			$word  = $words[rand(0, $this->dictionary->number_of_words()-1)];
			
			// do not repeat the same word in a 5 words group
			if(!in_array($word, array_slice($toret, $this->min_repeat_count*-1))){
				$toret[] = $word;
			}
		}

		return $toret;
	}

	/**
	 * Returns a number on a gaussian distribution based
	 * on the average word length of an english sentence.
	 * Statistics Source:
	 * 	http://hearle.nahoo.net/Academic/Maths/Sentence.html
	 * 	Average: 24.46
	 * 	Standard Deviation: 5.08
	 */
	private function gaussian_sentence(){
		$avg		=	(float) 24.460;
		$std_dev	=	(float) 5.080;
		return (int) round($this->gaussian_math->gauss_ms($avg, $std_dev));
	}

	/**
	 * Builds a single paragraph
	 */
	private function get_paragraph(){
		$count = $this->words_per_paragraph;
		$words = $this->get_words($count);

		$delta = $count;
		$curr = 0;
		$sentences = array();
		while ($delta > 0) {
			$senSize = $this->gaussian_sentence();

			if (($delta - $senSize) < 4) {
				$senSize = $delta;
			}

			$delta -= $senSize;

			$sentence = array();
			for ($i = $curr; $i < ($curr + $senSize); $i++) {
				$sentence[] = $words[$i];
			}
			$sentence[0] = ucfirst($sentence[0]);

			$this->punctuate($sentence);
			$curr = $curr + $senSize;
			$sentences[] = $sentence;
		}

		return $sentences;
	}

	/**
	 * Renders the current object according to the current format
	 * @return LipsumGenerator $this for chainability
	 */
	public function render(){
		srand($this->seed);
		if(empty($this->render)){
			switch($this->format){
				case self::FORMAT_HTML:
					$this->render_html();
					break;

				case self::FORMAT_PLAIN:
					$this->render_plain();
					break;

				case self::FORMAT_RICH_HTML:
					$this->render_rich_html();
					break;

				case self::FORMAT_TEXT:
					$this->render_text();
					break;

				default:
					throw new InvalidArgumentException(sprintf("Unsupported format '%s'", $format));
			}
		}
		return $this;
	}

	/**
	 * Magic method called when the current object is casted to string
	 */
	public function __toString(){
		return $this->render()->render;
	}
	
	/**
	 * Generates and renders in plain forma
	 * @see LipsumGenerator::FORMAT_PLAIN
	 * @return LipsumGenerator $this for chainability
	 */
	private function render_plain(){
		$this->render = '';
		$is_first_p = true;
		for($i=0; $i<$this->number_of_paragraphs; $i++){
			$paragraph = $this->get_paragraph();
			$rendered_p = '';
			
			if($is_first_p){
				$this->force_beginning(&$paragraph);
			} else {
				$rendered_p .= "\n";
			}
			
			foreach($paragraph as $sentence){
				$rendered_p .= implode(' ', $sentence).' ';
			}
			$this->render .= $rendered_p;
			$is_first_p = false;
		}
		return $this;
	}
	
	/**
	 * Generates and render in text format
	 * @see LipsumGenerator::FORMAT_TEXT
	 * @return LipsumGenerator $this for chainability
	 */
	private function render_text(){
		$this->render = '';
		$is_first_p = true;
		for($i=0; $i<$this->number_of_paragraphs; $i++){
			$paragraph = $this->get_paragraph();
			$rendered_p = '';
			
			if($is_first_p){
				$this->force_beginning(&$paragraph);
			} else {
				$rendered_p .= "\n\n\t";
			}
			
			foreach($paragraph as $sentence){
				$rendered_p .= implode(' ', $sentence).' ';
			}
			$this->render .= $rendered_p;
			$is_first_p = false;
		}
		return $this;
	}

	/**
	 * Generates and renders in html format
	 * @see LipsumGenerator::FORMAT_HTML
	 * @return LipsumGenerator $this for chainability
	 */
	private function render_html(){
		$this->render = '';
		$is_first_p = true;
		for($i=0; $i<$this->number_of_paragraphs; $i++){
			$paragraph = $this->get_paragraph();
			$rendered_p = '';
			
			if($is_first_p){
				$this->force_beginning(&$paragraph);
			} else {
				$rendered_p .= "\n";
			}
			
			$rendered_p .= HtmlHelper::open_tag('p'); //'<p>';
			foreach($paragraph as $sentence){
				$rendered_p .= implode(' ', $sentence);
			}
			$rendered_p .= HtmlHelper::close_tag('p'); //"</p>";
			
			$this->render .= $rendered_p;
			$is_first_p = false;
		}
		return $this;
	}
	
	/**
	 * Sets the configuration for the rich html generation
	 * @param array $config an array of parameters:
	 * <code>
	 * $default = array(
			'strong'	=>	array(
				'percent'	=>	2,
				'max_words'	=>	5,
				'params'	=>	''
			),
			'a'			=>	array(
				'percent' 	=>	5,
				'max_words' =>	5,
				'params'	=>	'href="#"'
			),
			'em'		=>	array(
				'percent'	=>	10,
				'max_words'	=>	5,
				'params'	=>	''
			)
		);
		<code>
	 */
	public function set_rich_html_config($config=array()){
		$default = array(
			'strong'	=>	array(
				'percent'	=>	10,
				'max_words'	=>	5,
				'params'	=>	''
			),
			'a'			=>	array(
				'percent' 	=>	5,
				'max_words' =>	5,
				'params'	=>	'href="#"'
			),
			'em'		=>	array(
				'percent'	=>	2,
				'max_words'	=>	5,
				'params'	=>	''
			)
		);
		
		$this->rich_html_config = array_merge($default, (array)$config);
		return $this;
	}
	
	/**
	 * Generates and renders in rich html format
	 * @see LipsumGenerator::FORMAT_RICH_HTML
	 * @return LipsumGenerator $this for chainability
	 */
	private function render_rich_html(){
		$this->render = '';
		$is_first_p = true;
		$this->stats = array();
		// useful to avoid the same tag to be repeated without plain text between
		$last_closed_tag = false;
		
		if(empty($this->rich_html_config)) $this->set_rich_html_config();
		
		for($i=0; $i<$this->number_of_paragraphs; $i++){
			$paragraph = $this->get_paragraph();
			$rendered_p = '';
			
			if($is_first_p){
				$this->force_beginning(&$paragraph);
			} else {
				$rendered_p .= "\n";
			}
			
			$rendered_p .= HtmlHelper::open_tag('p'); //'<p>';
			foreach($paragraph as $sentence){
				// stores the currently opened html tag
				$current_tag = '';
				// stores how many words are left before the current tag have to be closed
				$words_until_close_tag = 0;
				foreach($sentence as $index => $word){
					if($words_until_close_tag < 0){
						$words_until_close_tag = 0;
					}
					
					if(empty($current_tag) && empty($last_closed_tag)){
						$tag_to_add = '';
						foreach($this->rich_html_config as $tag => $tag_config){
							$rnd = rand(0, 99);
							if($rnd < $tag_config['percent']){
								//v(array($rnd, $tag, $index));
								if(
										isset($this->rich_html_config[$tag_to_add]['percent']) 
										&& $this->rich_html_config[$tag_to_add]['percent'] < $tag_config['percent']
								){
									$tag_to_add = $tag;
									$this->stats[$tag]++;
								}
							}
						}
					}
					
					if(!empty($tag_to_add)){
						// insert the current tag!
						$current_tag = $tag_to_add;
						//$rendered_p .= $this->open_tag($tag_to_add);
						$rendered_p .= HtmlHelper::open_tag($tag_to_add, $this->rich_html_config[$tag_to_add]['params']);
						$words_until_close_tag = rand(1, $this->rich_html_config[$tag_to_add]['max_words']);
						$tag_to_add = '';
					}
					
					
					$rendered_p .= $word;
					
					$last_closed_tag = '';
					
					if(!empty($current_tag)){
						if($words_until_close_tag == 0){
							$rendered_p .= HtmlHelper::close_tag($current_tag);
							$last_closed_tag = $current_tag;
							$current_tag = '';
						} else {
							$words_until_close_tag--;
						}
					}
					
					$rendered_p .= ' ';
					
					
				}
				// close the last tag if it's still open at the paragraph end
				$rendered_p .= HtmlHelper::close_tag($current_tag);
			}
			$rendered_p .= HtmlHelper::close_tag('p'); //"</p>";
			
			$this->render .= $rendered_p;
			$is_first_p = false;
		}
		return $this;
	}
	
	/**
	 * Forces the given sentences (paragraph) 
	 * to start with $this->beginning list of words.
	 * @param array $sentences the list of sentences
	 */
	private function force_beginning(&$sentences){
		if(count($this->beginning)){
			foreach($this->beginning as $k => $v){
				$sentences[0][$k] = $v;
			}
			$sentences[0][0] = ucfirst($sentences[0][0]);
		}
	}

	
	/**
	 * Inserts commas and periods in the given array of words.
	 * @param array $sentence the list of sentence
	 */
	private function punctuate(& $sentence){
		$count = count($sentence);
		$sentence[$count - 1] .= '.';
	
		if ($count < 4) {
			return $sentence;
		}
	
		$commas = $this->numberOfCommas($count);
	
		for ($i = 1; $i <= $commas; $i++) {
			$index = (int) round($i * $count / ($commas + 1));
	
			if ($index < ($count - 1) && $index > 0) {
				$sentence[$index] .= ',';
			}
		}
	}
	
	/**
	 * Determines the number of commas for a
	 * sentence of the given length. Average and
	 * standard deviation are determined superficially
	 * @param $len int a medium amount of words between two commas
	 */
	private function numberOfCommas($len){
		$avg    = (float) log($len, 6);
		$stdDev = (float) $avg / 6.000;
	
		return (int) round($this->gaussian_math->gauss_ms($avg, $stdDev));
	}
	
	/**
	 * Retrieves the config set for this generator
	 * @return array the list of settings
	 */
	private function get_config(){
		$fields = array_keys(get_class_vars(__CLASS__));
		$config = array();
		foreach($fields as $field){
			$config[$field] = $this->{$field};
		}
		// avoid waste of memory in the db
		unset($config['render']);
		return $config;
	}
	
	/**
	 * Bulk configure the generator.
	 * @param array $config the list of settings pairs name=>value
	 * @return LipsumGenerator $this for chainability
	 */
	private function set_config($config){
		$fields = array_keys(get_class_vars(__CLASS__));
		foreach($fields as $field){
			if(isset($config[$field]))
				$this->{$field} = $config[$field];
		}
		return $this;
	/*		->set_math(new GaussianMath())
			->set_dictionary(new LoremIpsumDictionary());*/
	}
	
	/**
	 * Store config into WP options system
	 * @return LipsumGenerator $this for chainability
	 */
	public function save(){
		add_option(self::OPTION_NAME, $this->get_config(), null, false);
		return $this;
	}
	
	/**
	 * Retrives config from WP options system
	 * @return boolean false if config is not present
	 */
	public function load(){
		$config = get_option(self::OPTION_NAME, false);
		if(!$config) return false;
		$this->set_config($config);
		return true;
	}
	
	/**
	 * Removes current config from WP options system
	 * @return LipsumGenerator $this for chainability
	 */
	public function flush(){
		delete_option(self::OPTION_NAME);
		return $this;
	}
	
	/**
	 * Hooks to 'the_content' hook.
	 * If the page has no content it will fill it with the generated text.
	 * @return LipsumGenerator $this for chainability
	 */
	public function hook(){
		$this->init();
		add_filter('the_content', array($this, 'the_content'), 10, 1);
		return $this;
	}
	
	/**
	 * Hook for the_content: 
	 * if there is no content it appends the generated
	 * @param $content string the content
	 */
	public function the_content($content){
		return (empty($content)) 
			? $this->init()->__toString() 
			: $content;
	}
	


}




