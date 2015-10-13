

var path_type;
var crash_sites = new OpenLayers.Layer.Vector("Crash Locations");
var style = new OpenLayers.Style({
  'pointRadius': 6,
  'externalGraphic': 'img/pulsing.gif'
});
var styleMap = new OpenLayers.StyleMap(style);
$(document).ready(function () {

var p4326 = new OpenLayers.Projection("EPSG:4326");
var p900913 = new OpenLayers.Projection("EPSG:900913");
var crash_sites = new OpenLayers.Layer.Vector("Crash Locations",
                                               {styleMap: styleMap});

Proj4js.defs["EPSG:2232"] = "+proj=lcc +lat_1=39.75 +lat_2=38.45 +lat_0=37.83333333333334 +lon_0=-105.5 +x_0=914401.8288036576 +y_0=304800.6096012192 +ellps=GRS80 +datum=NAD83 +to_meter=0.3048006096012192 +no_defs"; 
var p2232 = new OpenLayers.Projection("EPSG:2232");

var wms = new OpenLayers.Layer.WMS("Bicycle Faclities",
		"http://gis.drcog.org/geoserver/wms?", 
		{layers: 'DRCOG:bicycle_facility_inventory_2011',
		 format: 'image/png',
		 transparent: 'true',
		 isBaseLayer: 'false',
		 projection: 'EPSG:2232',
		 reproject: 'true'
		});
    map.addLayer(wms);  
});

function onChange2(){
    path_type = $("#selectdistance").val();
    $.ajax({

                                     url: "crashes.php", 
                                     data: {path_type: path_type},
                                     dataType: "json",
                                     //dataType:"text",
                                     type: "POST",
                                     success: function(data, status, xhr) {
                                             
                                             var len = parseFloat(data.total_rows);
                                           
                                             var crash_number = len.toString();
                                            //alert(data);
                                             $("#crash_number").html(crash_number);
                                             $("#path_type").html($("#selectdistance option:selected").text());
                                             crash_sites.destroy();
                                             crash_sites = new OpenLayers.Layer.Vector("Crash Locations",
                                                                                 {styleMap: styleMap});
                                             map.addLayer(crash_sites);
                                             var p = new OpenLayers.Format.GeoJSON();
                                             var gformat = new OpenLayers.Format.GeoJSON();    					    
                                             var gcoords='';
                                             $.each(data.rows, function(k, v) {
                                                 var last = len - 1; 
                                                 //console.log(k);
                                                 var coords = JSON.stringify(data.rows[k].row.geojson);
                                                 if (k < last){
                                                 gcoords += '{"geometry":'+coords+'},';
                                                 }
                                                 else{ 
                                                 gcoords += '{"geometry":'+coords+'}';
                                                 }
                                               });  
                                             gcoords = '['+gcoords+']';
                                            // alert(gcoords);                               
      					     var gg = '{"type":"FeatureCollection", "features":' + gcoords + '}'; 
                                           
                                             var feats = gformat.read(gg);
                                             
                                             crash_sites.addFeatures(feats); 
                                             
                                             },
                                     error: function(xhr, status, error) {
                                       $().trigger("ajax:failure", [xhr, status, error]);
     
                                             alert(error);
                                            
                                        }                       
                          });


}

                            

