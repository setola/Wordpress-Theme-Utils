window.map;
window.styledMap;
window.infowindow;

function initialize() {
	var myOptions = {
		zoom					:	map_info.zoom,
		center					:	new google.maps.LatLng(map_info.center.lat, map_info.center.lng),
		mapTypeId				:	google.maps.MapTypeId[map_info.type],
		mapTypeControl 			:	false,
		streetViewControl 		:	false,
		scrollwheel 			:	false
	};
	window.map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
	
	var styles = [];
	styles.push({
		featureType: "poi.business",
		elementType: "labels",
		stylers: [
		          { visibility: "off" }
		]
	});
	window.styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});
	
	window.map.mapTypes.set('map_style', window.styledMap);
	window.map.setMapTypeId('map_style');

	var contentString = 
		'<div id="marker-content"><p class="entry-title">'+
		map_info.title+'</p><p class="entry-content">'+
		map_info.content+'</p></div>';

	window.infowindow = new google.maps.InfoWindow({
		content			: contentString
	});

	var marker = new google.maps.Marker({
		position		:	new google.maps.LatLng(map_info.point.lat, map_info.point.lng),
		map					:	window.map,
		title				:	map_info.title
	});

	window.tilesloaded_listener = google.maps.event.addListener(window.map, 'tilesloaded', function() { 
		window.infowindow.open(map,marker);
		google.maps.event.removeListener(window.tilesloaded_listener);
    });

	google.maps.event.addListener(marker, 'click', function() {
		window.infowindow.open(map,marker);
	});	
}



google.maps.event.addDomListener(window, 'load', initialize);