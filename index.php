<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
// define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
// require __DIR__ . '/wp-blog-header.php';


// header("Location: /Beauraing/index.php");
?>

<!DOCTYPE html>
<html>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="css/bootstrap.css" rel="stylesheet">
     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
   integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
   crossorigin=""/>


 <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
   integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
   crossorigin=""></script>
<head>
    
    <meta charset="ibm866">
	<!-- Load plotly.js into the DOM -->
	
	<meta name="viewport" content="width=device-width"/><!-- To adapth at each screen -->



 <style>

#map { height: 40rem; width: 60rem;}



.header {
    margin: auto;
    background-image: url("home_picture.jpeg");
    background-repeat: no-repeat;
    background-size: 100% 100%;
    font-family: "Comic Sans MS";
    text-align: center;
    color: #ECF0F1;
    min-height: 20rem;
    /*max-width: 60rem;*/
    padding-left: 0px;
    vertical-align: middle;
    /*position: center; */
    /*margin-left = 0px;*/
}

.bottom {
  float: bottom;
  padding: 10px;
}

.main {
    max-width: 60rem;
    margin: auto;
}

img {
 float: left;
 

}


#logo{

    position: absolute;
    top: 0;
    left: 0;

  
}

.center{
    margin: auto;  
}
h2{
 font-family: "COMIC SANS MS";   
 font-size: 3.5rem;
}
h3{
 font-family: "COMIC SANS MS";   
 font-size: 3rem;
}
</style>
</head>

<body>
    
<div class="container" id="HeadPage">
    
    <div class="header">    
        <!--<img  id="logo" href="http://www.fbvl.be" src="logo_fbvl.jpeg" width="50"/>-->
        <img  href="http://www.fbvl.be" src="logo_fbvl.jpeg" width="50"/>
            <div  class="bottom">
            <h3>WindspotBelgium</h3></div>

            
           

    </div> <br>

        <div class="center" id="map"></div>
        <br>
        <div class="main">
<!--             <p> 1 new stations are in construction...</p> -->
            <img url="/FBVL2/index.php" class="logo" src='icons8-manche-à-air-48-2.png' width="40"/>
        </div> 
    </div>
       
</div>


  
  
<script>
    var map = L.map('map').setView([50.322, 5.005], 8);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);


var manche = L.icon({
    iconUrl: 'icons8-manche-à-air-48-2.png',
    iconSize:     [40, 40], // size of the icon
    // shadowSize:   [50, 64], // size of the shadow
    // iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    // shadowAnchor: [4, 62],  // the same for the shadow
    // popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});




var points = [
    ["Beauraing", 50.091, 4.924, "/Beauraing/index.php"],
    ["La Roche", 50.20128, 5.52701, "/FBVL1/index.php"],
    ["Maillen", 50.374, 4.925,  "/FBVL2/index.php"]

    ];
var marker = [];
var i;
for (i = 0; i < points.length; i++) {
    marker[i] = new L.Marker([points[i][1], points[i][2]], {
        win_url: points[i][3],
        icon: manche
        });
    marker[i].addTo(map);
    marker[i].on('click', onClick);
};
    

function onClick(e) {
    console.log(this.options.win_url);
    window.open(this.options.win_url);
}



</script> 