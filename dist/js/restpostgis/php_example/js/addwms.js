$(document).ready(function () {    
 var wms = new OpenLayers.Layer.WMS("Muni Boundaries 2010",
		"http://gis.drcog.org/geoserver/gwc/service/wms?", 
		{layers: 'DRCOG:muni_2010',
		 format: 'image/png',
		 transparent: 'true',
		 isBaseLayer: 'false',
		 projection: 'EPSG:2232',
		 reproject: 'true'
		});
     map.addLayer(wms);

});  
