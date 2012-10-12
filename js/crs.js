$(document).ready(function() {
	var $crs = $('#bookingform');
	if($crs.size() > 0){
		var original_positions = $crs.offset();
		
		var scroll_the_bar = function(){
			if(original_positions.top < $(window).scrollTop()){
				$crs.css({
					'position'	:	'fixed',
					'top'	:	'0'
	
				});
			} else {
				$crs.css({
					'position'	:	'absolute',
					'top' 	: '270px'
				});
			}	
			
		};
		
		scroll_the_bar();	
		$(window).scroll(scroll_the_bar);
	}
});