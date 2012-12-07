<?php 
/**
 * The following object is used to compute numbers with a guassian distribution
 * @link http://us.php.net/manual/en/function.rand.php#53784
 */
class GaussianMath{
	
	public function gauss(){
		$x = $this->random_0_1();
		$y = $this->random_0_1();
	
		return sqrt(-2 * log($x)) * cos(2 * pi() * $y);
	}
	
	public function gauss_ms($m = 0.0, $s = 1.0){
		return $this->gauss() * $s + $m;
	}
	
	public function random_0_1(){
		return (float) rand() / (float) getrandmax();
	}
}
