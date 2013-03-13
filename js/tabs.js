jQuery(document).ready(function(){
	jQuery('.tab').eq(0).addClass('active');
	jQuery('.tabs').each(function(){
		var tabs = jQuery(this);
		tabs.find('a').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			var current_link = jQuery(this);
			tabs.find('.active').removeClass('active');
			current_link.addClass('active');
			jQuery('.tab.active').removeClass('active');
			jQuery(current_link.attr('href')).addClass('active');
			if(current_link.attr('data-title')){
				jQuery('#tabs-title').html(current_link.attr('data-title'));
			}
		});
	}).find('#'+jQuery('.tab.active').attr('id')).addClass('active');
});