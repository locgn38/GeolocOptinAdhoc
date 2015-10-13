var map; 

$(document).ready(function () {  
    map = new OpenLayers.Map('map', {
	maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
         maxResolution: 156543.0399,
		 numZoomLevels: "auto",
         units: 'm',
         projection: new OpenLayers.Projection("EPSG:4326"),
         displayProjection: new OpenLayers.Projection("EPSG:900913")
	});
	
    map.addControl(new OpenLayers.Control.LayerSwitcher());
    
    var gmap = new OpenLayers.Layer.Google(
        "Google Streets", 
        {numZoomLevels: 20}
    );
	
	var gphy = new OpenLayers.Layer.Google(
        "Google Physical",
        {type: google.maps.MapTypeId.TERRAIN}
    );

    var ghyb = new OpenLayers.Layer.Google(
        "Google Hybrid",
        {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
    );
    var gsat = new OpenLayers.Layer.Google(
        "Google Satellite",
        {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
    );
    
     map.addLayers([gmap, gphy, ghyb, gsat]);

     map.setCenter(new OpenLayers.LonLat(-104.96, 39.75).transform(
        new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject()), 9);
});

$(document).ready(function(){
			$('pre.code').highlight({source:1, zebra:1, indent:'space', list:'ol'});
		});
