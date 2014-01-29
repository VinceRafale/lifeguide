
// read the data, create markers
function miles_googlemap(data) {
	
	// get the bounds of the map
	bounds = new google.maps.LatLngBounds();  
	// clear old markers
  	search_googlemap_deleteMarkers();   
	jsonData = jQuery.parseJSON(data);
  	// create the info window
	
  	// if no markers found, display map_marker_nofound div with no search criteria met message	
  	 if (jsonData[0].totalcount <= 0) {
		
		var mapcenter = new google.maps.LatLng(CITY_MAP_CENTER_LAT,CITY_MAP_CENTER_LNG);
		search_googlemap_listMapMarkers1(jsonData);
		map.setCenter(mapcenter);
		map.setZoom(CITY_MAP_ZOOMING_FACT);
  	}else{			
		var mapcenter = new google.maps.LatLng(CITY_MAP_CENTER_LAT,CITY_MAP_CENTER_LNG);
		search_googlemap_listMapMarkers1(jsonData);
		if(zoom_option==1){
			map.fitBounds(bounds);
			var center = bounds.getCenter();
			map.setCenter(center);
		}else{
			map.setCenter(mapcenter);
			map.setZoom(CITY_MAP_ZOOMING_FACT);
		}		
	}
}
/*Delete the existing google map markers */
function search_googlemap_deleteMarkers() {		
	 if (markerArray && markerArray.length > 0) {		
		for (i in markerArray) {
			if (!isNaN(i)){								
				markerArray[i].setMap(null);
				infoBubble.close();
			}
		}
		markerArray.length = 0;
	  }	
	
	markerClusterer.clearMarkers();
	
}
function search_googlemap_listMapMarkers1(input) {
	markers=input;
	var search_string = document.getElementById('search_string');
	var totalcount = input[0].totalcount;
	if(mClusterer != null)
	{
		mClusterer.clearMarkers();
	}	
	mClusterer = null;	
	if(totalcount > 0){
		for (var i = 0; i < input.length; i++) {							 
			var details = input[i];				
			var image = new google.maps.MarkerImage(details.icons,new google.maps.Size(PIN_POINT_ICON_WIDTH, PIN_POINT_ICON_HEIGHT));
			var coord = new google.maps.LatLng(details.location[0], details.location[1]);			
			markers[i]  = new google.maps.Marker({
							position: coord,
							title: details.name,
							visible: true,
							clickable: true,
							map: map,
							icon: details.icons
						});
			
			bounds.extend(coord);
			markerArray[i] = markers[i];				
			markers[i]['infowindow'] = new google.maps.InfoWindow({
				content: details.message,
				maxWidth: 200
			});	
			
			//New infobundle			
			 infoBubble = new InfoBubble({
				maxWidth: 210,
				minWidth: 210,
				minHeight: 'auto',
				padding: 0,
				content:details.message,
				borderRadius:0, // 4,
				borderWidth: 3,
          		borderColor: '#939393',
				overflow: 'visible',
			  });			
			//finish new infobundle
			
			//Start			
                google.maps.event.addListener(markers, "click", function (e) {														    
				infoBubble.open(map, details.message);					
                });
			
			
			mgr.addMarkers( markers[i], 0 );			
			search_marker_attachMessage(markers[i], details.message);
			
		}			
		markerClusterer = new MarkerClusterer(map, markers);
	}  
}
function search_marker_attachMessage(marker, msg) {
  var myEventListener = google.maps.event.addListener(marker, 'click', function() {
		infoBubble.setContent( msg );
		infoBubble.open(map, marker);
  });
}