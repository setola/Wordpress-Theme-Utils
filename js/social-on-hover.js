jQuery(document).ready(function(){
	var html_to_add = 
		'<div class="addthis_toolbox addthis_default_style">'
			+'<a class="addthis_button_facebook"></a>'
			+'<a class="addthis_button_google_plusone"></a>'
			+'<a class="addthis_button_twitter"></a>'
			+'<a class="addthis_button_compact"></a>'
			+'<a class="addthis_counter addthis_bubble_style"></a>'
		+'</div>';
	jQuery('#socials-placeholder').hover(function(){
		var container = jQuery('#socials');
		jQuery.getScript('http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-502116d244d86b08', function(){
			container.html(html_to_add);
			addthis.init();
		});
	});
});