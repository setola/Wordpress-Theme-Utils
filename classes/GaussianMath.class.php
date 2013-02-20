<?php 
/**
 * sstores the GaussianMath class definition
 */

/**
 * The following object is used to compute numbers with a gaussian distribution
 * @link http://us.php.net/manual/en/function.rand.php#53784
 */
class GaussianMath{
	
	/**
	 * Gets a random number in a gaussian distribution
	 */
	public function gauss(){
		$x = $this->random_0_1();
		$y = $this->random_0_1();
	
		return sqrt(-2 * log($x)) * cos(2 * pi() * $y);
	}
	
	/**
	 * Get an ms value
	 * @param double $m
	 * @param double $s
	 */
	public function gauss_ms($m = 0.0, $s = 1.0){
		return $this->gauss() * $s + $m;
	}
	
	/**
	 * Gets a random float between 0 and 1
	 */
	public function random_0_1(){
		return (float) rand() / (float) getrandmax();
	}
}
