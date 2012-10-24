var fbqs = fbqs || {
	hotelname	:	'ITROMHTLDomusAustral',
};

/**
 * Example of how to customize the qs markup
 */
/*
BookingFormType.types.one_line_simplest	= {
	'html'				: '<div class="bookingform one_line_nbdays"><h2 class=bf_title></h2><form class=bf_form target=dispoprice><input class=bf_showpromotions type=hidden name=showPromotions><input class=bf_langue type=hidden name=langue><input class=bf_clusternames type=hidden name=Clusternames><input class=bf_hotelnames type=hidden name=Hotelnames><select class=bf_hotel_list name=useless style=display:none></select><select style="display:none;" class=bf_from_day name=fromday></select><select style="display:none;" class=bf_from_month name=frommonth></select><select style="display:none;" class=bf_from_year name=fromyear></select><span class=bf_datepicker_wrapper><input class=datepicker_from ><span class=datepicker_button></span></span><select class=bf_nbdays name=nbdays></select><select class=bf_adulteresa name=adulteresa></select><select style="display:none;" class=bf_enfantresa name=enfantresa></select><input class=bf_accesscode name=AccessCode><input type=button class=bf_booknow name=B1><div class="links"><a href="javascript:;" class=bf_options></a><a href="javascript:;" class=bf_cancel></a></div></form></div>',
	'select_type'	: 'select',
	'nbdays'			: true
};
*/

new SingleBookingForm({
	'id' 				:	'fastbooking_qs',
	'type'				:	'one_line_nbdays_with_labels',
	'where'				:	'before',
	'cname'				:	fbqs.hotelname,
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

var _gaq = _gaq || []; // without this the lib will give an erro i f setted to use analytics

/**
 * Example of custom labels
 */
BF_languages.lingue.it.onlinebooking = 'Verifica disponibilità';
BF_languages.lingue.en.onlinebooking = 'Check Availability';

(function(){	
	for(language in BF_languages.lingue){
		BF_languages.lingue[language].fblangcode = '';
	}
	BookingForm.renderAll();
	jQuery('.book-action').live('click', function(){
		jQuery('#defaultBf .bf_booknow').trigger('click');
	});
	jQuery('.book').live('click', function(e){
		Fblib.hhotelResaDirect(fbqs.hotelname,'',jQuery(this).attr('data-promocode'),'','','');
	});
	jQuery('.book-room').live('click', function(e){
		Fblib.hhotelResaDirect(fbqs.hotelname,'','',jQuery(this).attr('data-roomcode'),'','');
	});
	jQuery('.direct').live('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		Fblib.hhotelResaDirect(fbqs.hotelname,'',jQuery(this).parent('.promo-wrapper').attr('id'),'','','');
	});
	jQuery('.bookingform').fadeIn('slow');
})();


