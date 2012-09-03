var cname = 'ITFINHTLSPPalace';
new SingleBookingForm({
	'id' 				:	'fastbooking_qs',
	'type'				:	'one_line_nbdays_with_labels',
	'where'				:	'before',
	'cname'				:	cname,
	'lang'				:	runtimeInfos.currentLanguage,
	'FB_nb_delay'		:	7,
	'customizations'	:	[
		//new LabelOnSelects_customization({'iata':true,'adults':true,'nights':true,'children':true}),
		new Datepicker_customization().setFromDatepickerConfig({dateFormat: 'dd.mm.yy'}).setToDatepickerConfig({dateFormat: 'dd.mm.yy'})
	],
	'numberOfPeople'	:	{
			"minAdults" 	:	1,
			"maxAdults"		:	5,
			"minChildrens"	:	0,
			"maxChildrens"	:	3
	}
});

FblibConf.googleAnalytics = {
		"useGa"			: true,
		"asynch"		: true,
		"varName"		: "pageTracker"
};

var _gaq = _gaq || [];

BF_languages.lingue.it.booknow = 'Verifica disponibilità';
BF_languages.lingue.en.booknow = 'Check Availability';

(function(){
	BookingForm.renderAll();
	jQuery('.book-action').live('click', function(){
		jQuery('#defaultBf .bf_booknow').trigger('click');
	});
	jQuery('.book').live('click', function(e){
		Fblib.hhotelResaDirect(fbqs.hotelname,'',jQuery(this).attr('data-promocode'),'','','');
	});
	jQuery('.book-room').live('click', function(e){
		Fblib.hhotelResaDirect(fbqs.hotelname,'','',jQuery(this).attr('data-promocode'),'','');
	});
	jQuery('.direct').live('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		Fblib.hhotelResaDirect(fbqs.hotelname,'',jQuery(this).parent('.promo-wrapper').attr('id'),'','','');
	});
	jQuery('.bookingform').fadeIn('slow');
})();


(function() {
   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
   ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();