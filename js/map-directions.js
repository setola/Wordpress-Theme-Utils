function directions(){
	
	var directionsDisplay = new google.maps.DirectionsRenderer();
	var directionsService = new google.maps.DirectionsService();
	directionsDisplay.setMap(window.map);

	jQuery('form.map-directions').each(function(){
		var form = jQuery(this);
		
		form.find('.mode').click(function(){
			form.find('.mode.current').removeClass('current');
			jQuery(this).addClass('current');
		}).eq(0).click();
		
		form.submit(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			window.infowindow.close();
			google.maps.event.removeListener(window.tilesloaded_listener);
			
			var request = {
					destination	: new google.maps.LatLng(map_info.point.lat, map_info.point.lng).toString(),
					origin		: form.find('.from').val(),
					travelMode	: google.maps.TravelMode[form.find('.mode.current').attr('data-mode')]
				};
			
			directionsService.route({
					destination	: new google.maps.LatLng(map_info.point.lat, map_info.point.lng),
					origin		: form.find('.from').val(),
					travelMode	: google.maps.TravelMode[form.find('.mode.current').attr('data-mode')]
				}, 
				function(result, status) {
					if (status == google.maps.DirectionsStatus.OK) {
						directionsDisplay.setDirections(result);
					}
				}
			);
		});
	});
};


google.maps.event.addDomListener(window, 'load', directions);










function calcRoute(from, to, mode) {
	var request = {
		origin		:	from,
		destination	:	to,
		travelMode	:	mode
	};
	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(response);
		} else {
			alert('Error, try using a different address.');
			if (window.console && console.log) console.log(status);
		}
	});
}