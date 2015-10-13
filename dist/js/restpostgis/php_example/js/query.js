

var buff_dist = '5280';
$(document).ready(function () {


//var html;
var currentPopup;

var p4326 = new OpenLayers.Projection("EPSG:4326");
var p900913 = new OpenLayers.Projection("EPSG:900913");
var selected_tracts = new OpenLayers.Layer.Vector("Selected Tracts");
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
                          var html, pop_change;
                          var sum = 0;
                          var total = 0;

                           $.ajax({

                                     url: "buffer_point.php", 
                                     data: {lat: xy.lat, lon: xy.lon, distance: buff_dist},
                                     dataType: "json",
                                     //dataType:"text",
                                     type: "POST",
                                     success: function(data, status, xhr) {
                                             var len = parseFloat(data.total_rows);
                                            
                                           
                                              if (len == 0) {
                                               alert("Nothing found");
                                              }
                                              else{
                                              
                                              $.each(data.rows, function(k, v) {
                                                    pop_change = parseFloat(data.rows[k].row.pop_change)*100;

                                                     sum = sum + pop_change;
                                                     total = total + 1;
                                                       });
                                              var mean = sum/total;
                                              var mean = mean.toFixed(2);
                                              var mean = mean.toString();
                                              var mean = mean+"%";
                                              selected_tracts.destroy();
                                              selected_tracts = new OpenLayers.Layer.Vector("Selected Tracts");
                                              map.addLayer(selected_tracts);
                                              
                                              var p = new OpenLayers.Format.GeoJSON();
                                              var gformat = new OpenLayers.Format.GeoJSON();    					   
                                              var gcoords='';
                                             $.each(data.rows, function(k, v) {
                                                 var last = len - 1; 
                                                 var coords = JSON.stringify(data.rows[k].row.geojson);
                                                
                                                 if (k < last){
                                                 gcoords += '{"geometry":'+coords+'},';
                                                 }
                                                 else{ 
                                                 gcoords += '{"geometry":'+coords+'}';
                                                 }
                                               });  
                                             gcoords = '['+gcoords+']';
                                                                     
      					     var gg = '{"type":"FeatureCollection", "features":' + gcoords + '}';
                                             var feats = gformat.read(gg);
                                             selected_tracts.addFeatures(feats);   
                                          
                                              }    
                                          
                                            if (currentPopup != null && currentPopup.visible()) {
                                                 currentPopup.hide();
                                                currentPopup.destroy();
                                                  }

                       		             info = new OpenLayers.Popup.FramedCloud(
                                             "data",
                                             xy_900913,
		                             null,
                                             mean,
                                             null,
                                             true
                                            );
                                             currentPopup = info;
                                             map.addPopup(info);
                                             },
                                     error: function(xhr, status, error) {
                                       $().trigger("ajax:failure", [xhr, status, error]);
                                       
                                             alert(error);
                                            
                                        }                       
                          });
                         }
                                     
                                    }
                        		
                    
    })
    

    var click = new OpenLayers.Control.Click();
    map.addControl(click);
    click.activate();
   
   

});
function onChange(){
    buff_dist = $("#selectdistance").val();
  
}
    

