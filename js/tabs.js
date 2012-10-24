jQuery(document).ready(function(){
	jQuery('.tabs ul li a').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		var that = jQuery(this);
		that.parent().parent().find('.active').removeClass('active');
		that.addClass('active');
		jQuery('.preview-entry:visible').fadeOut(function(){
			jQuery(that.attr('href')).fadeIn();
		});
		
	}).eq(0).addClass('active');
	jQuery('.preview-entry').eq(0).fadeIn();
});