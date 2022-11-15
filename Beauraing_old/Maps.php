
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
    
 <!-- Bootstrap -->
       
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">

<link rel="stylesheet" href="css/qgis2web.css">
<link rel="stylesheet" href="css/leaflet.css">
<link rel="stylesheet" href="css/leaflet.groupedlayercontrol.min.css">
<link rel="stylesheet" href="css/leaflet-measure.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css" /> 

<link href="css/main.css" rel="stylesheet"> 
<link href="css/max.css" rel="stylesheet">
 
	
<style>
        #map {
         	width: 800px;
            height: 700px;
            padding: 0;
            margin: auto;
        }
</style>

   
        <title></title>
    </head>
    
  
    <body>

<div class="container">

	
	

   		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js"></script>
        <script src="js/qgis2web_expressions.js"></script>
        <script src="js/leaflet.js"></script>
        <script src="js/leaflet.rotatedMarker.js"></script>
        <script src="js/leaflet.pattern.js"></script>
        <script src="js/leaflet-hash.js"></script>
<!--        <script src="js/Autolinker.min.js"></script>  -->
        <script src="js/rbush.min.js"></script>
        <script src="js/labelgun.min.js"></script>
        <script src="js/labels.js"></script>
        <script src="js/leaflet-measure.js"></script>
        <script type="text/javascript" src="js/proj4js-compressed.min.js"></script>
        <script src="js/fs.js"></script>

  		<script src="js/leaflet.groupedlayercontrol.min.js"></script>

	 
		

</div>   

<script>



if (Nbr_interfero > 0){
  
        var map = L.map('map', {
            zoomControl:true, maxZoom:13, minZoom:1
        })
        var hash = new L.Hash(map);
         map.attributionControl.setPrefix('<a href="https://github.com/tomchadwin/qgis2web" target="_blank">qgis2web</a> &middot; <a href="https://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> &middot; <a href="https://qgis.org">QGIS</a>');
       
 //        var autolinker = new Autolinker({truncate: {length: 30, location: 'smart'}});
        var measureControl = new L.Control.Measure({
            position: 'topleft',
            primaryLengthUnit: 'meters',
            secondaryLengthUnit: 'kilometers',
            primaryAreaUnit: 'sqmeters',
            secondaryAreaUnit: 'hectares'
        });
        measureControl.addTo(map);
        document.getElementsByClassName('leaflet-control-measure-toggle')[0]
        .innerHTML = '';
        document.getElementsByClassName('leaflet-control-measure-toggle')[0]
        .className += ' fas fa-ruler';
        var bounds_group = new L.featureGroup([]);
        function setBounds() {
            if (bounds_group.getLayers().length) {
                map.fitBounds(bounds_group.getBounds());
            }
        }
//###################  Add TileLayer ##########################        

        var layer_terrain_6 = L.tileLayer('http://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}', {
          //   pane: 'pane_terrain_6',
            transparent: true,
            attribution: '',
            minZoom: 1,
            maxZoom: 18,
            minNativeZoom: 0,
            maxNativeZoom: 18
        });
        layer_terrain_6;
        var layer_satellite_7 = L.tileLayer('http://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}', {
         //    pane: 'pane_satellite_7',
            transparent: true,
            attribution: '',
            minZoom: 1,
            maxZoom: 18,
            minNativeZoom: 0,
            maxNativeZoom: 18
        });
        layer_satellite_7;
        map.addLayer(layer_satellite_7);



      
	var baseLayers = {"satellite": layer_satellite_7,"relief": layer_terrain_6};
         



  //	L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);



    
	setBounds();
	L.ImageOverlay.include({
		getBounds: function () {
			return this._bounds;
		}
	});
  
}  // Retour if nombre interfero > 0

</script>

 

</div> 
    </body>

</html

