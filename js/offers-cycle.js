window.offers_checker = window.offers_checker || [];

FB.Loader.attachEvent(FB.Loader.eventType.AFTER_LOADING, {
	'onNotify': function(){
		window.offers_checker.push(
			setInterval(checkOffesCycleIsReady, 1000)
		);
	}
});

function checkOffesCycleIsReady(){
	if(initializeOffersCycle()){
		clearOffersCycleInterval();
	}
}

function clearOffersCycleInterval(){
	for(i=0; i<window.offers_checker.length; i++)
		clearInterval(window.offers_checker[i]);
}

function initializeOffersCycle(){
	if(typeof(jQuery)=='undefined') return false;
	if(typeof(jQuery.fn.cycle)=='undefined') return false;
	if(jQuery('.offers-container .cycle > ul').html() == '') return false;
	
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
	
	return true;
};