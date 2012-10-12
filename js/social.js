var socials = {
	facebook:{
		href:"http://www.facebook.com",
		text:"Facebook"
	},
	twitter:{
		href:"http://www.facebook.com",
		text:"Facebook"
	},
	plus:{
		href:"http://www.facebook.com",
		text:"Facebook"
	},
	triadv:{
		href:"http://www.facebook.com",
		text:"Facebook"
	},
	skype:{
		href:"http://www.facebook.com",
		text:"Facebook"
	}
};
jQuery(document).ready(function(){
	var social_container = jQuery('.social');
	jQuery.each(socials, function(index, element){
		social_container.append(
			jQuery('<div>',{
				'class':'button'
			}).append(
				jQuery('<a>',{
					href:element.href,
					'class':'inner',
					title:element.text
				}).append(
					jQuery('<span>', {
						'class':'sprite '+index
					})
				)
			)
		);
	});
});