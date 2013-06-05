var closed=false;
jQuery(document).ready(function(){
	jQuery('.open-close').each(function(){
		var that = jQuery(this);
		var to_open = jQuery(that.attr('data-selector'));
		that.hover(function() {
			if (!closed){
				to_open.fadeOut(200);
				jQuery(this).addClass('open');
			}
		}, 
		function() {
			if (!closed){
				to_open.fadeIn(200);
				jQuery(this).removeClass('open');
			}
		}); 
		that.click(function () {
			if (closed){
				to_open.fadeIn(200);
				jQuery(this).removeClass('open');
				closed=!closed;
			} else {
				to_open.fadeOut(200);
				jQuery(this).addClass('open');
				closed=!closed;
			}
		});
	});
});