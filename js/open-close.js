var closed=false;
jQuery(document).ready(function(){
	jQuery('.open-close').hover(function() {
		if (!closed){
			jQuery('#front-page-text').fadeOut(200);
			jQuery(this).addClass('open');
		}
	}, 
	function() {
		if (!closed){
			jQuery('#front-page-text').fadeIn(200);
			jQuery(this).removeClass('open');
		}
	}); 
	jQuery('.open-close').click(function () {
		if (closed){
			jQuery('#front-page-text').fadeIn(200);
			jQuery(this).removeClass('open');
			closed=!closed;
		} else {
			jQuery('#front-page-text').fadeOut(200);
			jQuery(this).addClass('open');
			closed=!closed;
		}
	});
});