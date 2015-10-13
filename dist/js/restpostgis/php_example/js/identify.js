$(document).ready(function () {

//var html;
var currentPopup;
var p4326 = new OpenLayers.Projection("EPSG:4326");
var p900913 = new OpenLayers.Projection("EPSG:900913");
Proj4js.defs["EPSG:2232"] = "+proj=lcc +lat_1=39.75 +lat_2=38.45 +lat_0=37.83333333333334 +lon_0=-105.5 +x_0=914401.8288036576 +y_0=304800.6096012192 +ellps=GRS80 +datum=NAD83 +to_meter=0.3048006096012192 +no_defs"; 


var p2232 = new OpenLayers.Projection("EPSG:2232");

OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
                defaultHandlerOptions: {
                    'single': true,
                    'double': false,
                    'pixelTolerance': 4,
                    'stopSingle': false,
                    'stopDouble': false
                    
                },

                initialize: function(options) {
                    this.handlerOptions = OpenLayers.Util.extend(
                        {}, this.defaultHandlerOptions
                    );
                    OpenLayers.Control.prototype.initialize.apply(
                        this, arguments
                    ); 
                    this.handler = new OpenLayers.Handler.Click(
                        this, {
                            'click': this.trigger
                        }, this.handlerOptions
                    );
                }, 
	     
		trigger: function(e) {
                    
                    var longlat = map.getLonLatFromPixel(e.xy);
		    var xy = new OpenLayers.LonLat(longlat.lon, longlat.lat).transform(new OpenLayers.Projection("EPSG:900913"), p2232);
                    var xy_900913 = new OpenLayers.LonLat(xy.lon, xy.lat).transform(p2232, p900913);
                           
                    id(xy);
		     function id(location) { 
                          var html;

                           $.ajax({

                                     url: "id.php", 
                                     data: {lat: xy.lat, lon: xy.lon},
                                     dataType: "json",
                                     //dataType; "text",                                   
                                     type: "POST",
                                     success: function(data, status, xhr) {
                                             
                                              //successful AJAX request. Create a bubble with city name!.
                                             $().trigger("ajax:success", [data, status, xhr]);
                                             var htmllen = data.results;
                                             //alert(htmllen.length);
                                             if (htmllen.length == 0){
                                                 html = "No City Found"
                                             }
                                             else{
					         html = data.results[0].rows[0].row.name;
                                                 }
                                             if (currentPopup != null && currentPopup.visible()) {
                                                 currentPopup.hide();
                                                 currentPopup.destroy();
                                                  }

                       		             info = new OpenLayers.Popup.FramedCloud(
                                             "data",
                                             xy_900913,
		                             null,
                                             html,
                                             null,
                                             true
                                             );
                                             currentPopup = info;
                                             map.addPopup(info);
                                             },
                                     error: function(xhr, status, error) {
                                       $().trigger("ajax:failure", [xhr, status, error]);
                                     if (xhr.status == 404) { 
      
                                         alert("nothing found");
           
                                        }      
                                         }
                                      });

                       
                          }
                         
                                     
                                    }
                        		
                    
    })

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
    var click = new OpenLayers.Control.Click();
    map.addControl(click);
    click.activate();

});

