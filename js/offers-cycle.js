FB.Loader.observers[FB.Loader.eventType.AFTER_LOADING] = FB.Loader.observers[FB.Loader.eventType.AFTER_LOADING] || [];
FB.Loader.observers[FB.Loader.eventType.AFTER_LOADING].push({
	'onNotify': function(){ 
		jQuery('#FB_so.cycle > ul').cycle({
			next:   '#offers-preview .next',
			prev:   '#offers-preview .prev',
			pager:	'#pager',
			pause: true
		});
		jQuery('#show-offers-toggler a').fadeIn().click(function(){
			var element = jQuery('#offers-container');
			var toggler = jQuery(this);
			if(element.css('width')!='0px'){
				toggler.html(toggler.attr('data-open'));
				element.animate({width:0});
			}else{
				toggler.html(toggler.attr('data-close'));
				element.css('display','block').animate({width:"280px"});
			}
		});
	}
});