FB.Loader.observers[FB.Loader.eventType.AFTER_LOADING] = FB.Loader.observers[FB.Loader.eventType.AFTER_LOADING] || [];
FB.Loader.observers[FB.Loader.eventType.AFTER_LOADING].push({
	'onNotify': function(){ 
		jQuery('.offers-container').each(function(key, value){
			var that 				= jQuery(this);
			var cycle_container 	= that.find('.cycle > ul');
			var pager 				= that.find('.pager');
			var controls 			= that.find('.controls');
			var offers_toggler 		= that.find('.offers-toggler');
			
			cycle_container.cycle({
				pager:	pager,
				pause: true
			});

			controls.find('.next').click(function(){
				cycle_container.cycle('next');
			});
			controls.find('.prev').click(function(){
				cycle_container.cycle('prev');
			});
			
			offers_toggler.fadeIn().click(function(){
				var element = that;
				var toggler = jQuery(this);
				if(element.css('width')!='0px'){
					toggler.html(toggler.attr('data-open'));
					element.animate({width:0});
				}else{
					toggler.html(toggler.attr('data-close'));
					element.css('display','block').animate({width:"280px"});
				}
			});
		});
	}
});