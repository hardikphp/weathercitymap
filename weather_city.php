<code>
<html>
<head>
<title>Open Weather Map</title>
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
    <style type="text/css">
        .layersDiv{display:none}
    </style>

</head>
<body>
<div id="basicMap"></div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="OpenLayers.js"></script>
    <script src="OWM.OpenLayers.1.3.6.js" ></script>
<script>
function set_cookie(name, value, expires)
{
    if (!expires)
    {
    expires = new Date();
    }
    document.cookie = name + "=" + escape(value) + "; expires=" + expires.toGMTString() +  "; path=/";
}

function get_cookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
      ))
  return matches ? decodeURIComponent(matches[1]) : undefined
}
function set_lang(lang)
{
  expires = new Date();
  expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24));
  set_cookie('lang', lang, expires);
  window.location.reload();
}


function set_units()
{
  var units = 'metric';
  if( document.getElementById("units_check").checked ) units = 'imperial';
  expires = new Date();
  expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24));
  set_cookie('units', units, expires);
  window.location.reload();
}
</script>

<script type="text/javascript">
var map;
jQuery(document).ready( function() {


    map = new OpenLayers.Map("basicMap",
		{
		        units: 'm',
		        projection: new OpenLayers.Projection("EPSG:900913"),
		        displayProjection: new OpenLayers.Projection("EPSG:4326")
		}
	);

    var mapnik = new OpenLayers.Layer.OSM();
	var opencyclemap = new OpenLayers.Layer.XYZ(
		"opencyclemap",
		"http://a.tile3.opencyclemap.org/landscape/${z}/${x}/${y}.png",
		{
			numZoomLevels: 18, 
			sphericalMercator: true
		}
	);

	var stations = new OpenLayers.Layer.Vector.OWMStations("Stations informations", {units : ''} );
	stations.setVisibility(false);

	var city = new OpenLayers.Layer.Vector.OWMWeather("Current weather", {units : ''});

	var precipitation = new OpenLayers.Layer.XYZ(
		"Precipitation forecasts",
		"http://${s}.tile.openweathermap.org/map/precipitation/${z}/${x}/${y}.png",
		{
			numZoomLevels: 19, 
			isBaseLayer: false,
			opacity: 0.6,
			sphericalMercator: true
		}
	);
        map.addLayers([mapnik, opencyclemap, stations, city]);
	
	// need for permalink
	var args = OpenLayers.Util.getParameters();
        if (args.lat && args.lon && args.zoom) {
    		var position = new OpenLayers.LonLat(parseFloat(args.lon), parseFloat(args.lat));
    		if(args.lon< 181 && args.lat < 181)
    			position.transform(
    			    new OpenLayers.Projection("EPSG:4326"),
    			    new OpenLayers.Projection("EPSG:900913")
    			);
    
    		map.setCenter(position, parseFloat(args.zoom));
        } else {
    		var lat = 28.6000, lon = 77.2000;
    		var centre = new OpenLayers.LonLat(lon, lat);
    		centre.transform(
    		    new OpenLayers.Projection("EPSG:4326"),
    		    new OpenLayers.Projection("EPSG:900913")
    		);
	        map.setCenter( centre, 4);
        }


	// Layers switcher
	var ls = new OpenLayers.Control.LayerSwitcher({'ascending':false});
	map.addControl(ls);
	ls.maximizeControl();

	map.addControl(new OpenLayers.Control.Permalink('permalink'));


	// Activate Popup windows 
	selectControl = new OpenLayers.Control.SelectFeature([stations, city]);
	map.addControl(selectControl);
	selectControl.activate(); 

	//Save cookie
	map.events.register('moveend', map, function (e) {    
		var longlat = map.getCenter();
		longlat.transform(
			new OpenLayers.Projection("EPSG:900913"), 
			new OpenLayers.Projection("EPSG:4326")
		);
		expires = new Date();					
		expires.setTime(expires.getTime() + (1000 * 60 * 60 * 24 * 7));	
		set_cookie('lat', longlat.lat, expires);	
		set_cookie('lng', longlat.lon, expires);	
		set_cookie('zoom', map.getZoom(), expires);	

	}); 

}

);
</script>
</html>
</code>