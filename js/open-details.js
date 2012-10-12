jQuery(document).ready(function(){
	jQuery('.open-details-toggler').click(function(){
		var toggler = jQuery(this);
		toggler.animate({'opacity':0}, 'fast', function(){
			jQuery('#'+jQuery(this).attr('data-id')+' .openclose').slideToggle('slow', function(){
				var that = jQuery(this);
				if(that.is(':visible')){
					toggler.html(toggler.attr('data-close')).animate({'opacity':1}, 'fast');
				}
				else{
					toggler.html(toggler.attr('data-open')).animate({'opacity':1}, 'fast');
				}
			});
		});
	});
});