
var SchoolMaps = new Array();
var SchoolMarkers = new Array();

function initialize() {
	var SchoolMapOptions = {
		center: {lat: 0, lng: 0},
          zoom: 8,
		disableDefaultUI: true,
		mapTypeControl: true,
		zoomControl: true,
		zoomControlOptions: {
    		style:google.maps.ZoomControlStyle.SMALL,
		},
		mapTypeId:google.maps.MapTypeId.ROADMAP
	};

	var schoolmaps = document.getElementsByClassName('uc-school-marker-map');
	var bounds = new google.maps.LatLngBounds();
	var geoposition = {lat: 39.8163023, lng: 105.1200482};

	for(var i = 0; i < schoolmaps.length; i++) {
		SchoolMaps[i] = new google.maps.Map(schoolmaps.item(i), SchoolMapOptions);

		SchoolMaps[i].info_window = new google.maps.InfoWindow({
			maxWidth: 800,
		});

		for(var j = 0; j < schools.length; j++) {
			SchoolMarkers[i] = new Array();
			SchoolMarkers[i][j] = new google.maps.Marker({
				position: new google.maps.LatLng(schools[j]["lat"],schools[j]["long"]),
				name: schools[j]["name"],
				map: SchoolMaps[i],
			});
			google.maps.event.addListener(SchoolMarkers[i][j], 'click', function() {
				var infowindow_content = this.name;
				var map = this.getMap();
				map.info_window.setContent(infowindow_content);
				map.info_window.open(map,this);
			});
			bounds.extend(SchoolMarkers[i][j].position);
		}

		var input = schoolmaps.item(i).parentElement.getElementsByClassName('stemplayground-map-search').item(0).getElementsByTagName("input").item(0);
		var autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.map = SchoolMaps[i];


		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			this.map.info_window.close();
			var place = this.getPlace();
			if (place.geometry.viewport) {
				this.map.fitBounds(place.geometry.viewport);
			} else if(place.geometry.location) {
				this.map.setZoom(12);
				this.map.setCenter(place.geometry.location);
			}
		});

		google.maps.event.addListener(SchoolMaps[i], "click", function(){
			this.info_window.close();
		});
	}

    if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			geoposition = {
				lat: position.coords.latitude,
				lng: position.coords.longitude
			};
			for(var i = 0; i < schoolmaps.length; i++) {
				SchoolMaps[i].setCenter(geoposition);
			}
		}, function() {
			for(var i = 0; i < schoolmaps.length; i++) {
				SchoolMaps[i].setCenter(new google.maps.LatLng(40, -95));
				SchoolMaps[i].setZoom(4);
			}
		});
	} else {
		for(var i = 0; i < schoolmaps.length; i++) {
			SchoolMaps[i].setCenter(new google.maps.LatLng(40, -95));
			SchoolMaps[i].setZoom(4);
		}
	}
}

google.maps.event.addDomListener(window, 'load', initialize);