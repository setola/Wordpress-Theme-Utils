jQuery(document).ready(function() {

	var $crs = jQuery('#auto-fixed-top');
	if($crs.size() > 0){
		var original_positions = $crs.offset();
		
		var scroll_the_bar = function(){
			if(original_positions.top < jQuery(window).scrollTop()){
				$crs.addClass('fixed');
			} else {
				$crs.removeClass('fixed');
			}	
			
		};
		
		scroll_the_bar();	
		jQuery(window).scroll(scroll_the_bar);
	}
});