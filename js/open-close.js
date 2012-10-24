var chiuso=false;
jQuery(document).ready(function(){
	jQuery('.open-close').hover(	function() {
		if (!chiuso){
			jQuery('#front-page-text').fadeOut(200);
			jQuery(this).addClass('open');
		}
	}, 
	function() {
		if (!chiuso){
			jQuery('#front-page-text').fadeIn(200);
			jQuery(this).removeClass('open');
		}
	}); 
	jQuery('.open-close').click(function () {
		if (chiuso){
			jQuery('#front-page-text').fadeIn(200);
			jQuery(this).removeClass('open');
			chiuso=!chiuso;
		} else {
			jQuery('#front-page-text').fadeOut(200);
			jQuery(this).addClass('open');
			chiuso=!chiuso;
		}
	});
});