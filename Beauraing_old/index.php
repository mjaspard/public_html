<?php

//echo "<br> restart mercredi 17 vers 14h avec pile li-ion";
function GetParam($data)
		{
			$paramfile = fopen("parameters.txt","r");
			while(!feof($paramfile)) 
			{
			$line = fgets($paramfile);
	
			if (preg_match("/$data/i", $line))
				{
				$value = strstr($line, '#', true);
				$value=preg_replace('/\s+/', '', $value); 
				return $value;
		
				}
			}
		fclose($paramfile);
		}
		

function GetJson($file,$object)
	{
		$json = file_get_contents($file, true);
		$obj = json_decode($json, true);
		$value = $obj[$object];
		return $value;
	}





date_default_timezone_set('Europe/Brussels');
$t=time();


$CurrentStatus = GetJson('json/Sleep.html','SleepOrAwake');
$CurrentStatus = strval($CurrentStatus);


$Site = GetParam('Site');

$hour = date("H",$t);
$min = date("i",$t);
$month = date("m",$t);
$StartSleep = 'StartSleep_'.$month;
$StartSleep = GetParam($StartSleep);
$WakeUp = 'WakeUp_'.$month;
$WakeUp = GetParam($WakeUp);
$AwakeDuration = ($StartSleep-$WakeUp)*12; 
$arraylenght = $AwakeDuration - 1;
//echo $AwakeDuration;        // data for all the day (Nbr hours * 12 because 12 records per hours)
$BorderColor = "grey";

$servername = "localhost";

// REPLACE with your Database name
$dbname = "windspot_wind_data";
// REPLACE with Database user
$username = "windspot_max";
// REPLACE with Database user password
$password = "Massul_34";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// ---------------  Check if data hes been received in last 10 minutes

$sql = "SELECT id from $Site where reading_time >= now() - interval 15 minute limit 1";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    //echo "id: " . $row["id"]. "<br>";
    $lastId = $row["id"];
    }

if ($lastId == ''){
    $dataOK = false;
}else{
     $dataOK = true;
    }

function GetSleepInfo()
    {
        global $BorderColor;
        if ($BorderColor == 'red'){
            $myfile = fopen("SleepInfo.txt", "r") ;
            $SleepInfo= fread($myfile,filesize("SleepInfo.txt"));
            fclose($myfile);
        } else {
            $SleepInfo= "";
        }
        return $SleepInfo;
    }


//-----------------

$sql = "SELECT N, NNE, NE, ENE, E, ESE, SE, SSE, S, SSO, SO, OSO, O, ONO, NO, NNO, speed_max, speed_min, speed_avg,voltage_battery,voltage_input, voltage_solar, reading_time FROM $Site order by reading_time desc limit ".$AwakeDuration;

$result = $conn->query($sql);

while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
}



$readings_time = array_column($sensor_data, 'reading_time');


// ******* Uncomment to convert readings time array to your timezone ********
$i = 0;
foreach ($readings_time as $reading){
    // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
    //$readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading - 1 hours"));
    // Uncomment to set timezone to + 4 hours (you can change 4 to any number)
    $readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading + 8 hours"));
    $i += 1;
}

$N = json_encode(array_reverse(array_column($sensor_data, 'N')), JSON_NUMERIC_CHECK);
$NNE = json_encode(array_reverse(array_column($sensor_data, 'NNE')), JSON_NUMERIC_CHECK);
$NE = json_encode(array_reverse(array_column($sensor_data, 'NE')), JSON_NUMERIC_CHECK);
$ENE = json_encode(array_reverse(array_column($sensor_data, 'ENE')), JSON_NUMERIC_CHECK);
$E= json_encode(array_reverse(array_column($sensor_data, 'E')), JSON_NUMERIC_CHECK);
$ESE = json_encode(array_reverse(array_column($sensor_data, 'ESE')), JSON_NUMERIC_CHECK);
$SE = json_encode(array_reverse(array_column($sensor_data, 'SE')), JSON_NUMERIC_CHECK);
$SSE = json_encode(array_reverse(array_column($sensor_data, 'SSE')), JSON_NUMERIC_CHECK);
$S = json_encode(array_reverse(array_column($sensor_data, 'S')), JSON_NUMERIC_CHECK);
$SSO = json_encode(array_reverse(array_column($sensor_data, 'SSO')), JSON_NUMERIC_CHECK);
$SO= json_encode(array_reverse(array_column($sensor_data, 'SO')), JSON_NUMERIC_CHECK);
$OSO = json_encode(array_reverse(array_column($sensor_data, 'OSO')), JSON_NUMERIC_CHECK);
$O = json_encode(array_reverse(array_column($sensor_data, 'O')), JSON_NUMERIC_CHECK);
$ONO = json_encode(array_reverse(array_column($sensor_data, 'ONO')), JSON_NUMERIC_CHECK);
$NO = json_encode(array_reverse(array_column($sensor_data, 'NO')), JSON_NUMERIC_CHECK);
$NNO = json_encode(array_reverse(array_column($sensor_data, 'NNO')), JSON_NUMERIC_CHECK);
$speed_max = json_encode(array_reverse(array_column($sensor_data, 'speed_max')), JSON_NUMERIC_CHECK);
$speed_min = json_encode(array_reverse(array_column($sensor_data, 'speed_min')), JSON_NUMERIC_CHECK);
$speed_avg = json_encode(array_reverse(array_column($sensor_data, 'speed_avg')), JSON_NUMERIC_CHECK);
$voltage_input = json_encode(array_reverse(array_column($sensor_data, 'voltage_input')), JSON_NUMERIC_CHECK);
$voltage_solar = json_encode(array_reverse(array_column($sensor_data, 'voltage_solar')), JSON_NUMERIC_CHECK);
$voltage_battery = json_encode(array_reverse(array_column($sensor_data, 'voltage_battery')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);

$result->free();
$conn->close();


if (strcmp($CurrentStatus, 'Sleep') == 0){

	$val = GetJson('json/Sleep.html','SleepTime');
	$RemainTime = "Wake-up in ".$val. " minutes";
	$BorderColor = 'grey';
	$CurrentStatusdisp = $CurrentStatus;
	}else{
	$RemainTime = "";
	$SleepInfo = "";
	if ($dataOK == true){
	    $BorderColor = 'green';
	    $CurrentStatusdisp = $CurrentStatus;
	    }else{
	    $BorderColor = 'red';
	    $CurrentStatusdisp = "Error";
	    }
    }



?>

<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

<!DOCTYPE html>
<html>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/highchart.css" rel="stylesheet">
<head>
    
    <meta charset="ibm866">
	<!-- Load plotly.js into the DOM -->
	
	<meta name="viewport" content="width=device-width"/><!-- To adapth at each screen -->



 <style>


.jumbotron {
    margin: auto;
    background-image: url("site.jpg");
    background-repeat: no-repeat;
    background-size: 100% 100%;
    font-family: "Comic Sans MS";
    text-align: center;
    color: #ECF0F1;
    min-height: 30rem;
    max-width: 70rem;
    padding: 3 rem;
    /*position: center; */

}

.top{
    position: absolute; 
    top: 0;
    /*left: 0; */
    /*right: 0; */
} 



.child > section {
margin: 0.5rem;
border-radius: 6px;
border: 1px solid <?php echo $BorderColor; ?>;
background-color: #EEEEEE;
}
.child {
/*border: 1px solid pink;*/
display: flex;
flex-wrap: wrap;
justify-content: center;
align-content: space-around;
}
.child-V-50 {
width: 45%;
min-width: 300px;
max-width: 350px;
display: flex;
flex-direction: column;
justify-content: space-around;
align-content: space-around;
background-color: #EEEEEE;
}
.child-H-50 {
width: 45%;
display: flex;
justify-content: space-around;
align-content: space-around;
background-color: #EEEEEE;
}
.child-V-100 {
width: 100%;
min-width: 360px;
min-height: 200px;
max-width: 710px;
display: flex;
justify-content: space-around;
align-content: space-around;
flex-direction: column;
background-color: #EEEEEE;
}
.child-H-100 {
width: 100%;
display: flex;
justify-content: space-around;
align-content: space-around;
background-color: #EEEEEE;
}



#Last5MinArrow{
display: flex;
flex-direction: column;
justify-content: space-around;
align-items: center;
}
#Last5MinArrow .arrow{
font-size: 6em;
position: center;
line-height: 1rem;
width: 1rem;
}
#Last5MinSpeed{
color: black;
position: -webkit-sticky;
display: flex;
flex-direction: column;
justify-content: space-around;
align-content: center;
font-size: 4rem;
}
#Last2Hour{
color: black;
position: -webkit-sticky;
}
#ShowVoltage{
color: black;
position: -webkit-sticky;  
}
.button{
position: fixed;
left: 50%;
color: dimgrey;
    
}
section h4{
    text-align:center;
    font-size: 1.8rem;
}
.child p{
font-size: 1.5rem;
padding: 3rem;
margin:1rem;
font-family: sans-serif;
}


.chart_block{
    display: block;
    width: 100%;
    height: 100%;
}

h2{
 font-family: "COMIC SANS MS";   
 font-size: 3.5rem;
}
h3{
 font-family: "COMIC SANS MS";   
 font-size: 3rem;
}
.vh{visibility: hidden;}
.dn{display: none;}
  </style>
</head>

<body>
    
<div class="container" id="HeadPage">
    <div class="jumbotron">
        <section class="top">
            <h2><?php echo $Site; ?></h2>
            <h3><?php echo date("l d M Y"); ?></h3><br></br>
            <h3><?php echo date("h:i:s"); ?></h3><br></br>
         </section>   

        
    </div><br>
<!--	<div class="warp">-->
        <div class="child">
            

	     	<section class="child-V-50">
  	            <h4>Status Station</h4><br>
                <p>
                <?php echo "Current station status: ".$CurrentStatusdisp ?><br>
                <?php echo GetSleepInfo()  ?><br>
                <?php echo "Battery voltage = <a id=\"GetVoltage\"></a>";?><br>
                <?php echo " Hours of activation: ".$WakeUp."H to ".$StartSleep."H" ?><br>
                <?php echo " Last data: ".$readings_time[0] ?>
                </p>
            </section>

	   
	     	<section class="child-V-50">
  	            <h4>Last 5 minute</h4><br>
  	            <section class="child-H-100">
    	            <div id="Last5MinArrow"></div>
            	    <div id="Last5MinSpeed"></div></section>
  	        </section>
  	    </div>
  	    <div class="child">
  	        <section class="child-V-50">
	            <h4>Wind rose last 30 minutes: <br><i>Average speed</i></h4>
	            <div id="Last30Min"></div>
	        </section>
	        <section class="child-V-50">
	            <h4>Wind rose last 2 hours: <br><i>Average speed</i></h4>
	            <div class="chart_block" id="Last2Hour"></div>
	        </section>
        </div>
  	    <div class="child">
            <section class="child-V-100">
                <h4>Wind speed last 2 hours [km/h]</h4>
		        <div class="chart_block" id="speed_chart_2h"></div>
	        </section>
	        <section class="child-V-100">
                <h4>Wind speed all day [km/h]</h4>
		        <div class="chart_block" id="speed_chart_day"></div>
	        </section>
	        <section id=ShowVoltage class="child-V-100">
		        <h4>Voltage Monitoring</h4>
	            <div class="chart_block" id="voltage_monitoring"></div>
            </section>
	    </div>

      <!--      <button class="button" onclick="Show_Voltage()">Show Voltage</button>-->
      <!--  <div class="child" class="dn">-->
		    <!--<section id=ShowVoltage class="child-V-100">-->
		    <!--    <h4>Voltage Monitoring</h4>-->
	     <!--       <div id="voltage_monitoring"></div>-->
      <!--      </section>-->
      <!--  </div>-->
	        

    
</div>
</body>



<script src='https://cdn.plot.ly/plotly-latest.min.js'></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>

  var x = document.getElementById("ShowVoltage");
  x.style.display = "block"

function Show_Voltage() {
  var x = document.getElementById("ShowVoltage");
  if (x.style.display == "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}


//----------------- Write PHP variable in Javascript variable ---------------//

var N = <?php echo $N; ?>;
var NNE = <?php echo $NNE; ?>;
var NE = <?php echo $NE; ?>;
var ENE = <?php echo $ENE; ?>;
var E = <?php echo $E; ?>;
var ESE = <?php echo $ESE; ?>;
var SE = <?php echo $SE; ?>;
var SSE = <?php echo $SSE; ?>;
var S = <?php echo $S; ?>;
var SSO = <?php echo $SSO; ?>;
var SO = <?php echo $SO; ?>;
var OSO = <?php echo $OSO; ?>;
var O = <?php echo $O; ?>;
var ONO = <?php echo $ONO; ?>;
var NO = <?php echo $NO; ?>;
var NNO = <?php echo $NNO; ?>;
var speed_min = <?php echo $speed_min; ?>;
var speed_max = <?php echo $speed_max; ?>;
var speed_avg = <?php echo $speed_avg; ?>;
var voltage_input = <?php echo $voltage_input; ?>;
var voltage_solar = <?php echo $voltage_solar; ?>;
var voltage_battery = <?php echo $voltage_battery; ?>;
var reading_timer = <?php echo $reading_time; ?>;

//speed_max = speed_max * 1.8;
//var speed_max = speed_max.map(function(x) x * 1.8);
speed_min = speed_min.map(x => x * 2);
speed_avg = speed_avg.map(x => x * 2);
speed_max = speed_max.map(x => x * 2);
//voltage_battery = voltage_battery.map(x => x * 1.09);
console.log("voltage_battery = ");
console.log(voltage_battery);


var BorderColor = '<?=$BorderColor?>';


var d = new Date();
    NowYear = d.getFullYear();
    NowMonth = d.getMonth();
    NowDay = d.getDate();
    NowHour = d.getHours();
    NowMin = d.getMinutes();


 
// Create variable to scale the speed monitoring on last 2 hours   
var now = new Date();
now.setHours(now.getHours() - 2); // timestamp
NowHourMinus2 = now.getHours();

var StartSleep = <?php echo $StartSleep ?>;
var WakeUp = <?php echo $WakeUp ?>;

  
//----------------- function get-Voltage --------------------//  
// We return the average of last 5 values of battery
document.getElementById("GetVoltage").innerHTML = voltage_battery[voltage_battery.length - 1].toFixed(2);


//----------------- function display Last record ---------------//
// Prepare data


if (BorderColor == 'green'){
    
   
document.getElementById("Last5MinSpeed").innerHTML = SpeedLast5min();

function SpeedLast5min(){
	
    var speed_min_t1 = speed_min.slice(-1).pop();
    var speed_max_t1 = speed_max.slice(-1).pop();
    var speed_avg_t1 = speed_avg.slice(-1).pop();
return `

<h4 style="color: red;  text-align: left;">max: ${speed_max_t1}</h4>
<h4 style="color: black;  text-align: left;">avg: ${speed_avg_t1}<a> [km/h]</a></h4>
<h4 style="color: blue;  text-align: left;">min: ${speed_min_t1}</h4>
`;
}



document.getElementById("Last5MinArrow").innerHTML = ArrowLast5min();
function ArrowLast5min(){
ArrayLast5MinDir=[];
    ArrayLast5MinDir[0] = N.slice(-1).pop();
    ArrayLast5MinDir[1]  = NNE.slice(-1).pop();
    ArrayLast5MinDir[2]  = NE.slice(-1).pop();
    ArrayLast5MinDir[3]  = ENE.slice(-1).pop();
    ArrayLast5MinDir[4]  = E.slice(-1).pop();
    ArrayLast5MinDir[5]  = ESE.slice(-1).pop();
    ArrayLast5MinDir[6]  = SE.slice(-1).pop();
    ArrayLast5MinDir[7]  = SSE.slice(-1).pop();
    ArrayLast5MinDir[8]  = S.slice(-1).pop();
    ArrayLast5MinDir[9]  = SSO.slice(-1).pop();
    ArrayLast5MinDir[10]  = SO.slice(-1).pop();
    ArrayLast5MinDir[11]  = OSO.slice(-1).pop();
    ArrayLast5MinDir[12]  = O.slice(-1).pop();
    ArrayLast5MinDir[13]  = ONO.slice(-1).pop();
    ArrayLast5MinDir[14]  = NO.slice(-1).pop();
    ArrayLast5MinDir[15]  = NNO.slice(-1).pop();


    var LastMainDir_mem = 0;
    var LastMainDirTxt = '';
    //console.log(ArrayLast5MinDir);
    //console.log(ArrayLast5MinDir);
    for (var i=0; i<16; i++){
        ArrayLast5MinDir[i] = (ArrayLast5MinDir[i] / 75);
        if (ArrayLast5MinDir[i] > LastMainDir_mem){
        	var LastMainDir = i*22.5;
        	var LastMainRate = Math.round(ArrayLast5MinDir[i] * 100);
        	//console.log("LastMainDir_mem = ");
            //console.log(LastMainDir_mem);
            //console.log("i = ");
            //console.log(i);
        	LastMainDir_mem = ArrayLast5MinDir[i]
        }
    }
    //console.log("LastMainDir = ");
    //console.log(LastMainDir);
    switch (LastMainDir){
		case 0:
			LastMainDirTxt = 'North';
			break
		case 22.5:
			LastMainDirTxt = 'North North East';
			break			
		case 45:
			LastMainDirTxt = 'North East';
			break   
		case 67.5:
			LastMainDirTxt = 'East North East';
			break
		case 90:
			LastMainDirTxt = 'East';
			break			
		case 112.5:
			LastMainDirTxt = 'East South East';
			break   
		case 135:
			LastMainDirTxt = 'South East';
			break
		case 157.5:
			LastMainDirTxt = 'South South East';
			break			
		case 180:
			LastMainDirTxt = 'South';
			break   
		case 202.5:
			LastMainDirTxt = 'South South West';
			break
		case 225:
			LastMainDirTxt = 'South West';
			break			
		case 247.5:
			LastMainDirTxt = 'West South West';
			break 	
		case 270:
			LastMainDirTxt = 'West';
			break
		case 292.5:
			LastMainDirTxt = 'West North West';
			break			
		case 315:
			LastMainDirTxt = 'North West';
			break 
		case 337.5:
			LastMainDirTxt = 'North North West';
			break 

    }
	LastMainDir = LastMainDir + 'deg';
	//console.log(LastMainDir);

	return `
	<div class="arrow"; style=" opacity:${LastMainDir_mem}; -webkit-transform:rotate(${LastMainDir});">&#8595;</div>
	<h5>${LastMainDirTxt}  (${LastMainRate} %)</h5>
	`;
}
} // end if bordecolor == green



//-----------------display wind rose 30 minutes  ---------------

var WindRoseDuration = 30;    // Nbr of hours to display wind distribution on wind rose
var WindRose_TableIn= [];
var length = speed_avg.length;
//console.log("speed_avg.length");
//console.log(length);

	k=1;
WindRose_TableIn[0] = ["", "N", "NNE", "NE", "ENE","E", "ESE","SE", "SSE", "S", "SSO", "SO", "OSO", "O", "ONO", "NO", "NNO" ];
for (var i=1; i<=length; i++) {
		j = i-1;
	
	    var t_WR = reading_timer[j];
        var Last_hour_WR = t_WR.substr(11, 2);
        var Last_Day_WR = t_WR.substr(8, 2);
        var Last_min_WR = t_WR.substr(14, 2);
        var DeltaHour = NowHour - Last_hour_WR;
        var DeltaMinute = NowMin - Last_min_WR;
        var StartMinute1 = NowMin + 40;
        var StartMinute2 = NowMin - 40;
        //console.log("DeltaHour = "+ DeltaHour)
        //console.log("last minute = "+ Last_min_WR)
        //console.log("StartMinute1 "+ StartMinute1)
        //console.log("Last_hour_WR "+ Last_hour_WR)
        //console.log("NowHour "+ NowHour)
      
         // Fill the table only if the day of the record is equal to today and within the last 30 minutes (or 1 hour if choose by viewer)
        if  ((Last_Day_WR == NowDay) && 
            (((NowMin <= 30) && (DeltaHour == 1) && (Last_min_WR > StartMinute1)) || 
            ((NowMin <= 30) && (DeltaHour == 0) && (Last_min_WR < NowMin)) || 
            ((NowMin > 30) && (DeltaHour == 0) && (Last_min_WR > StartMinute2)))) 
            {
            //console.log("add data to last 30 min = "+ j)
            WindRose_TableIn[k]  = [speed_avg[j], N[j], NNE[j], NE[j], ENE[j], E[j], ESE[j], SE[j], SSE[j], S[j], SSO[j], SO[j], OSO[j], O[j], ONO[j], NO[j], NNO[j]];
            k++;
        }
}


RowNum = WindRose_TableIn.length;
ColNum = WindRose_TableIn[0].length;

let WindRose_TableOut = new Array(ColNum);
for (var i=1; i<ColNum; i++) {
	WindRose_TableOut[i] = [0, 0, 0, 0, 0, 0, 0 , 0, 0];
}

WindRose_TableOut[0] = ["Direction", "< 5 km/h", "5-10 km/h","10-15 km/h", "15-20 km/h", "20-25 km/h", "25-30 km/h", "30-35 km/h", ">35 km/h" ];


for (var i=1; i<RowNum; i++){
    for (var j=1; j<ColNum; j++) {
            if(WindRose_TableIn[i][j] == ""){
                WindRose_TableIn[i][j] = 0;
                }
        }
}


for (var i=1; i<RowNum; i++){
	if (WindRose_TableIn[i][0] <= 5){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][1] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
  	if ((WindRose_TableIn[i][0] > 5) && (WindRose_TableIn[i][0] <= 10)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][2] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
   if ((WindRose_TableIn[i][0] > 10) && (WindRose_TableIn[i][0] <= 15)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][3] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
   if ((WindRose_TableIn[i][0] > 15) && (WindRose_TableIn[i][0] <= 20)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][4] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
    if ((WindRose_TableIn[i][0] > 20) && (WindRose_TableIn[i][0] <= 25)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][5] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
if ((WindRose_TableIn[i][0] > 25) && (WindRose_TableIn[i][0] <= 30)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][6] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
if ((WindRose_TableIn[i][0] > 30) && (WindRose_TableIn[i][0] <= 35)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][7] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
if (WindRose_TableIn[i][0] > 35) {
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][8] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
}


// Convert each element of table out in minutes (1 record every 4 sec = minutes --> divide per 15)
for (var i=1; i<ColNum; i++){
    for (var j=1; j<8; j++) {
            
                WindRose_TableOut[i][j] = WindRose_TableOut[i][j]/15;
                WindRose_TableOut[i][j] = Math.round(WindRose_TableOut[i][j]);
                
        }
}

console.log("table_in last 30 minutes");
console.log(WindRose_TableIn);
console.log("table_out last 30 minutes");
console.log(WindRose_TableOut);


Highcharts.chart('Last30Min', {

    chart: {
       polar: true,
       type: 'column',    
       
    },
 /*     pane: {
    startAngle: 0,
    endAngle: 360
      },*/

    title: {
        text: null
    },

    pane: {
        size: '85%'
    },

    legend: {
        align: 'center',
        verticalAlign: 'bottom',
        y: 0,
        layout: 'horizontal'
    },

   xAxis: {
        tickmarkPlacement: 'on',
       labels: {
      formatter: function() {
      
        let label;
        switch (this.value) {
          case 0:
            label = 'N';
            break;
          case 1:
            label = 'NNE';
            break;
          case 2:
            label = 'NE';
            break;
          case 3:
            label = 'ENE';
            break;
          case 4:
            label = 'E';
            break;
          case 5:
            label = 'ESE';
            break;
          case 6:
            label = 'SE';
            break;
          case 7:
            label = 'SSE';
            break;
          case 8:
            label = 'S';
            break;
          case 9:
            label = 'SSW';
            break;
          case 10:
            label = 'SW';
            break;
          case 11:
            label = 'WSW';
            break;
          case 12:
            label = 'W';
            break;
          case 13:
            label = 'WNW';
            break;
          case 14:
            label = 'NW';
            break;
          case 15:
            label = 'NNW';
            break;
        }
        
        return label;
      }
      }
    },
    yAxis: {
        min: 0,
        endOnTick: false,
        showLastLabel: true,
        title: {
            text: ''
        },
        labels: {
            formatter: function () {
                timevalue = this.value;
             //   timevalue = (timevalue/15);
             //   timevalue = Math.round(timevalue);
                return timevalue + ' min';
            }
        }
       // reversedStacks: false
    },

    tooltip: {
        valueSuffix: 'min',
        followPointer: true
    },

    plotOptions: {
        series: {
            stacking: 'normal',
            shadow: false,
            groupPadding: 0,
            pointPlacement: 'on'
        }
    },
      series: [{
      
 

name: WindRose_TableOut[0][8],
        data: [
        ["N", WindRose_TableOut[1][8]],
        ["NNE", WindRose_TableOut[2][8]],
        ["NE", WindRose_TableOut[3][8]],
        ["ENE", WindRose_TableOut[4][8]],
        ["E", WindRose_TableOut[5][8]],
        ["ESE", WindRose_TableOut[6][8]],
        ["SE", WindRose_TableOut[7][8]],
        ["SSE", WindRose_TableOut[8][8]],
        ["S", WindRose_TableOut[9][8]],
        ["SSW", WindRose_TableOut[10][8]],
        ["SW", WindRose_TableOut[11][8]],
        ["WSW", WindRose_TableOut[12][8]],
        ["W", WindRose_TableOut[13][8]],
        ["WNW", WindRose_TableOut[14][8]],
        ["NW", WindRose_TableOut[15][8]],
        ["NNW", WindRose_TableOut[16][8]],
       ],
        color: 'rgba(248, 32, 32, 1)' 
          }, {
      name: WindRose_TableOut[0][7],
        data: [
        ["N", WindRose_TableOut[1][7]],
        ["NNE", WindRose_TableOut[2][7]],
        ["NE", WindRose_TableOut[3][7]],
        ["ENE", WindRose_TableOut[4][7]],
        ["E", WindRose_TableOut[5][7]],
        ["ESE", WindRose_TableOut[6][7]],
        ["SE", WindRose_TableOut[7][7]],
        ["SSE", WindRose_TableOut[8][7]],
        ["S", WindRose_TableOut[9][7]],
        ["SSW", WindRose_TableOut[10][7]],
        ["SW", WindRose_TableOut[11][7]],
        ["WSW", WindRose_TableOut[12][7]],
        ["W", WindRose_TableOut[13][7]],
        ["WNW", WindRose_TableOut[14][7]],
        ["NW", WindRose_TableOut[15][7]],
        ["NNW", WindRose_TableOut[16][7]],
       ],
        color: 'rgba(248, 32, 32, 0.7)'  
           },{
       name: WindRose_TableOut[0][6],
        data: [
        ["N", WindRose_TableOut[1][6]],
        ["NNE", WindRose_TableOut[2][6]],
        ["NE", WindRose_TableOut[3][6]],
        ["ENE", WindRose_TableOut[4][6]],
        ["E", WindRose_TableOut[5][6]],
        ["ESE", WindRose_TableOut[6][6]],
        ["SE", WindRose_TableOut[7][6]],
        ["SSE", WindRose_TableOut[8][6]],
        ["S", WindRose_TableOut[9][6]],
        ["SSW", WindRose_TableOut[10][6]],
        ["SW", WindRose_TableOut[11][6]],
        ["WSW", WindRose_TableOut[12][6]],
        ["W", WindRose_TableOut[13][6]],
        ["WNW", WindRose_TableOut[14][6]],
        ["NW", WindRose_TableOut[15][6]],
        ["NNW", WindRose_TableOut[16][6]],
       ],
        color: 'rgba(248, 169, 32, 1)' 
          },{
       name: WindRose_TableOut[0][5],
        data: [
        ["N", WindRose_TableOut[1][5]],
        ["NNE", WindRose_TableOut[2][5]],
        ["NE", WindRose_TableOut[3][5]],
        ["ENE", WindRose_TableOut[4][5]],
        ["E", WindRose_TableOut[5][5]],
        ["ESE", WindRose_TableOut[6][5]],
        ["SE", WindRose_TableOut[7][5]],
        ["SSE", WindRose_TableOut[8][5]],
        ["S", WindRose_TableOut[9][5]],
        ["SSW", WindRose_TableOut[10][5]],
        ["SW", WindRose_TableOut[11][5]],
        ["WSW", WindRose_TableOut[12][5]],
        ["W", WindRose_TableOut[13][5]],
        ["WNW", WindRose_TableOut[14][5]],
        ["NW", WindRose_TableOut[15][5]],
        ["NNW", WindRose_TableOut[16][5]],
       ],
        color: 'rgba(247, 215, 8, 1)'  
                        },{
       name: WindRose_TableOut[0][4],
        data: [
        ["N", WindRose_TableOut[1][4]],
        ["NNE", WindRose_TableOut[2][4]],
        ["NE", WindRose_TableOut[3][4]],
        ["ENE", WindRose_TableOut[4][4]],
        ["E", WindRose_TableOut[5][4]],
        ["ESE", WindRose_TableOut[6][4]],
        ["SE", WindRose_TableOut[7][4]],
        ["SSE", WindRose_TableOut[8][4]],
        ["S", WindRose_TableOut[9][4]],
        ["SSW", WindRose_TableOut[10][4]],
        ["SW", WindRose_TableOut[11][4]],
        ["WSW", WindRose_TableOut[12][4]],
        ["W", WindRose_TableOut[13][4]],
        ["WNW", WindRose_TableOut[14][4]],
        ["NW", WindRose_TableOut[15][4]],
        ["NNW", WindRose_TableOut[16][4]],
       ],
        color: 'rgba(64, 174, 45, 1)' 
                },{
      name: WindRose_TableOut[0][3],
        data: [
        ["N", WindRose_TableOut[1][3]],
        ["NNE", WindRose_TableOut[2][3]],
        ["NE", WindRose_TableOut[3][3]],
        ["ENE", WindRose_TableOut[4][3]],
        ["E", WindRose_TableOut[5][3]],
        ["ESE", WindRose_TableOut[6][3]],
        ["SE", WindRose_TableOut[7][3]],
        ["SSE", WindRose_TableOut[8][3]],
        ["S", WindRose_TableOut[9][3]],
        ["SSW", WindRose_TableOut[10][3]],
        ["SW", WindRose_TableOut[11][3]],
        ["WSW", WindRose_TableOut[12][3]],
        ["W", WindRose_TableOut[13][3]],
        ["WNW", WindRose_TableOut[14][3]],
        ["NW", WindRose_TableOut[15][3]],
        ["NNW", WindRose_TableOut[16][3]],
       ],
        color: 'rgba(109, 196, 90, 1)'   
        },{
      name: WindRose_TableOut[0][2],
        data: [
        ["N", WindRose_TableOut[1][2]],
        ["NNE", WindRose_TableOut[2][2]],
        ["NE", WindRose_TableOut[3][2]],
        ["ENE", WindRose_TableOut[4][2]],
        ["E", WindRose_TableOut[5][2]],
        ["ESE", WindRose_TableOut[6][2]],
        ["SE", WindRose_TableOut[7][2]],
        ["SSE", WindRose_TableOut[8][2]],
        ["S", WindRose_TableOut[9][2]],
        ["SSW", WindRose_TableOut[10][2]],
        ["SW", WindRose_TableOut[11][2]],
        ["WSW", WindRose_TableOut[12][2]],
        ["W", WindRose_TableOut[13][2]],
        ["WNW", WindRose_TableOut[14][2]],
        ["NW", WindRose_TableOut[15][2]],
        ["NNW", WindRose_TableOut[16][2]],
       ],
        color: 'rgba(199, 232, 191, 1)'
          }, {  
        name: WindRose_TableOut[0][1],
        data: [
        ["N", WindRose_TableOut[1][1]],
        ["NNE", WindRose_TableOut[2][1]],
        ["NE", WindRose_TableOut[3][1]],
        ["ENE", WindRose_TableOut[4][1]],
        ["E", WindRose_TableOut[5][1]],
        ["ESE", WindRose_TableOut[6][1]],
        ["SE", WindRose_TableOut[7][1]],
        ["SSE", WindRose_TableOut[8][1]],
        ["S", WindRose_TableOut[9][1]],
        ["SSW", WindRose_TableOut[10][1]],
        ["SW", WindRose_TableOut[11][1]],
        ["WSW", WindRose_TableOut[12][1]],
        ["W", WindRose_TableOut[13][1]],
        ["WNW", WindRose_TableOut[14][1]],
        ["NW", WindRose_TableOut[15][1]],
        ["NNW", WindRose_TableOut[16][1]],
       ],
        color: 'rgba(191, 232, 225, 0.5)'
       
        }]

});

//-----------------display wind rose 2 hours  ---------------

var WindRoseDuration = 2;    // Nbr of hours to display wind distribution on wind rose
var WindRose_TableIn= [];
var length = speed_avg.length;


	k=1;
WindRose_TableIn[0] = ["", "N", "NNE", "NE", "ENE","E", "ESE","SE", "SSE", "S", "SSO", "SO", "OSO", "O", "ONO", "NO", "NNO" ];
for (var i=1; i<=length; i++) {
		j = i-1;
	
	    var t_WR = reading_timer[j];
        var Last_hour_WR = t_WR.substr(11, 2);
        var Last_Day_WR = t_WR.substr(8, 2);
        var Last_min_WR = t_WR.substr(14, 2);
        var StartTimeWindRose = NowHour - Last_hour_WR;
         // Fill the table only if the day of the record is equal to today and within the last 2 hours (or 1 hour if choose by viewer)
        if ((Last_Day_WR == NowDay) && ((StartTimeWindRose < WindRoseDuration) || ((StartTimeWindRose == WindRoseDuration) && (Last_min_WR > NowMin)))){       
            WindRose_TableIn[k]  = [speed_avg[j], N[j], NNE[j], NE[j], ENE[j], E[j], ESE[j], SE[j], SSE[j], S[j], SSO[j], SO[j], OSO[j], O[j], ONO[j], NO[j], NNO[j]];
            k++;
        }
}


RowNum = WindRose_TableIn.length;
ColNum = WindRose_TableIn[0].length;

//let WindRose_TableOut = new Array(ColNum);
for (var i=1; i<ColNum; i++) {
	WindRose_TableOut[i] = [0, 0, 0, 0, 0, 0, 0 , 0, 0];
}

WindRose_TableOut[0] = ["Direction", "< 5 km/h", "5-10 km/h","10-15 km/h", "15-20 km/h", "20-25 km/h", "25-30 km/h", "30-35 km/h", ">35 km/h" ];


for (var i=1; i<RowNum; i++){
    for (var j=1; j<ColNum; j++) {
            if(WindRose_TableIn[i][j] == ""){
                WindRose_TableIn[i][j] = 0;
                }
        }
}


for (var i=1; i<RowNum; i++){
	if (WindRose_TableIn[i][0] <= 5){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][1] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
  	if ((WindRose_TableIn[i][0] > 5) && (WindRose_TableIn[i][0] <= 10)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][2] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
   if ((WindRose_TableIn[i][0] > 10) && (WindRose_TableIn[i][0] <= 15)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][3] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
   if ((WindRose_TableIn[i][0] > 15) && (WindRose_TableIn[i][0] <= 20)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][4] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
    if ((WindRose_TableIn[i][0] > 20) && (WindRose_TableIn[i][0] <= 25)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][5] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
if ((WindRose_TableIn[i][0] > 25) && (WindRose_TableIn[i][0] <= 30)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][6] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
if ((WindRose_TableIn[i][0] > 30) && (WindRose_TableIn[i][0] <= 35)){
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][7] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
if (WindRose_TableIn[i][0] > 35) {
    		for (var j=1; j<ColNum; j++) {
        		 WindRose_TableOut[j][8] +=  parseInt(WindRose_TableIn[i][j]);
  							}
  			}
}


// Convert each element of table out in minutes (1 record every 4 sec = minutes --> divide per 15)
for (var i=1; i<ColNum; i++){
    for (var j=1; j<8; j++) {
            
                WindRose_TableOut[i][j] = WindRose_TableOut[i][j]/15;
                WindRose_TableOut[i][j] = Math.round(WindRose_TableOut[i][j]);
                
        }
}

console.log("table_in last 2 hours ");
console.log(WindRose_TableIn);
console.log("table_out last 2 hours ");
console.log(WindRose_TableOut);


Highcharts.chart('Last2Hour', {

    chart: {
       polar: true,
       type: 'column',    
       
    },
 /*     pane: {
    startAngle: 0,
    endAngle: 360
      },*/

    title: {
        text: null
    },

    pane: {
        size: '85%'
    },

    legend: {
        align: 'center',
        verticalAlign: 'bottom',
        y: 0,
        layout: 'horizontal'
    },

   xAxis: {
        tickmarkPlacement: 'on',
       labels: {
      formatter: function() {
      
        let label;
        switch (this.value) {
          case 0:
            label = 'N';
            break;
          case 1:
            label = 'NNE';
            break;
          case 2:
            label = 'NE';
            break;
          case 3:
            label = 'ENE';
            break;
          case 4:
            label = 'E';
            break;
          case 5:
            label = 'ESE';
            break;
          case 6:
            label = 'SE';
            break;
          case 7:
            label = 'SSE';
            break;
          case 8:
            label = 'S';
            break;
          case 9:
            label = 'SSW';
            break;
          case 10:
            label = 'SW';
            break;
          case 11:
            label = 'WSW';
            break;
          case 12:
            label = 'W';
            break;
          case 13:
            label = 'WNW';
            break;
          case 14:
            label = 'NW';
            break;
          case 15:
            label = 'NNW';
            break;
        }
        
        return label;
      }
      }
    },
    yAxis: {
        min: 0,
        endOnTick: false,
        showLastLabel: true,
        title: {
            text: ''
        },
        labels: {
            formatter: function () {
                timevalue = this.value;
             //   timevalue = (timevalue/15);
             //   timevalue = Math.round(timevalue);
                return timevalue + ' min';
            }
        }
       // reversedStacks: false
    },

    tooltip: {
        valueSuffix: 'min',
        followPointer: true
    },

    plotOptions: {
        series: {
            stacking: 'normal',
            shadow: false,
            groupPadding: 0,
            pointPlacement: 'on'
        }
    },
      series: [{
      
 

name: WindRose_TableOut[0][8],
        data: [
        ["N", WindRose_TableOut[1][8]],
        ["NNE", WindRose_TableOut[2][8]],
        ["NE", WindRose_TableOut[3][8]],
        ["ENE", WindRose_TableOut[4][8]],
        ["E", WindRose_TableOut[5][8]],
        ["ESE", WindRose_TableOut[6][8]],
        ["SE", WindRose_TableOut[7][8]],
        ["SSE", WindRose_TableOut[8][8]],
        ["S", WindRose_TableOut[9][8]],
        ["SSW", WindRose_TableOut[10][8]],
        ["SW", WindRose_TableOut[11][8]],
        ["WSW", WindRose_TableOut[12][8]],
        ["W", WindRose_TableOut[13][8]],
        ["WNW", WindRose_TableOut[14][8]],
        ["NW", WindRose_TableOut[15][8]],
        ["NNW", WindRose_TableOut[16][8]],
       ],
        color: 'rgba(248, 32, 32, 1)' 
          }, {
      name: WindRose_TableOut[0][7],
        data: [
        ["N", WindRose_TableOut[1][7]],
        ["NNE", WindRose_TableOut[2][7]],
        ["NE", WindRose_TableOut[3][7]],
        ["ENE", WindRose_TableOut[4][7]],
        ["E", WindRose_TableOut[5][7]],
        ["ESE", WindRose_TableOut[6][7]],
        ["SE", WindRose_TableOut[7][7]],
        ["SSE", WindRose_TableOut[8][7]],
        ["S", WindRose_TableOut[9][7]],
        ["SSW", WindRose_TableOut[10][7]],
        ["SW", WindRose_TableOut[11][7]],
        ["WSW", WindRose_TableOut[12][7]],
        ["W", WindRose_TableOut[13][7]],
        ["WNW", WindRose_TableOut[14][7]],
        ["NW", WindRose_TableOut[15][7]],
        ["NNW", WindRose_TableOut[16][7]],
       ],
        color: 'rgba(248, 32, 32, 0.7)'  
           },{
       name: WindRose_TableOut[0][6],
        data: [
        ["N", WindRose_TableOut[1][6]],
        ["NNE", WindRose_TableOut[2][6]],
        ["NE", WindRose_TableOut[3][6]],
        ["ENE", WindRose_TableOut[4][6]],
        ["E", WindRose_TableOut[5][6]],
        ["ESE", WindRose_TableOut[6][6]],
        ["SE", WindRose_TableOut[7][6]],
        ["SSE", WindRose_TableOut[8][6]],
        ["S", WindRose_TableOut[9][6]],
        ["SSW", WindRose_TableOut[10][6]],
        ["SW", WindRose_TableOut[11][6]],
        ["WSW", WindRose_TableOut[12][6]],
        ["W", WindRose_TableOut[13][6]],
        ["WNW", WindRose_TableOut[14][6]],
        ["NW", WindRose_TableOut[15][6]],
        ["NNW", WindRose_TableOut[16][6]],
       ],
        color: 'rgba(248, 169, 32, 1)' 
          },{
       name: WindRose_TableOut[0][5],
        data: [
        ["N", WindRose_TableOut[1][5]],
        ["NNE", WindRose_TableOut[2][5]],
        ["NE", WindRose_TableOut[3][5]],
        ["ENE", WindRose_TableOut[4][5]],
        ["E", WindRose_TableOut[5][5]],
        ["ESE", WindRose_TableOut[6][5]],
        ["SE", WindRose_TableOut[7][5]],
        ["SSE", WindRose_TableOut[8][5]],
        ["S", WindRose_TableOut[9][5]],
        ["SSW", WindRose_TableOut[10][5]],
        ["SW", WindRose_TableOut[11][5]],
        ["WSW", WindRose_TableOut[12][5]],
        ["W", WindRose_TableOut[13][5]],
        ["WNW", WindRose_TableOut[14][5]],
        ["NW", WindRose_TableOut[15][5]],
        ["NNW", WindRose_TableOut[16][5]],
       ],
        color: 'rgba(247, 215, 8, 1)'  
                        },{
       name: WindRose_TableOut[0][4],
        data: [
        ["N", WindRose_TableOut[1][4]],
        ["NNE", WindRose_TableOut[2][4]],
        ["NE", WindRose_TableOut[3][4]],
        ["ENE", WindRose_TableOut[4][4]],
        ["E", WindRose_TableOut[5][4]],
        ["ESE", WindRose_TableOut[6][4]],
        ["SE", WindRose_TableOut[7][4]],
        ["SSE", WindRose_TableOut[8][4]],
        ["S", WindRose_TableOut[9][4]],
        ["SSW", WindRose_TableOut[10][4]],
        ["SW", WindRose_TableOut[11][4]],
        ["WSW", WindRose_TableOut[12][4]],
        ["W", WindRose_TableOut[13][4]],
        ["WNW", WindRose_TableOut[14][4]],
        ["NW", WindRose_TableOut[15][4]],
        ["NNW", WindRose_TableOut[16][4]],
       ],
        color: 'rgba(64, 174, 45, 1)' 
                },{
      name: WindRose_TableOut[0][3],
        data: [
        ["N", WindRose_TableOut[1][3]],
        ["NNE", WindRose_TableOut[2][3]],
        ["NE", WindRose_TableOut[3][3]],
        ["ENE", WindRose_TableOut[4][3]],
        ["E", WindRose_TableOut[5][3]],
        ["ESE", WindRose_TableOut[6][3]],
        ["SE", WindRose_TableOut[7][3]],
        ["SSE", WindRose_TableOut[8][3]],
        ["S", WindRose_TableOut[9][3]],
        ["SSW", WindRose_TableOut[10][3]],
        ["SW", WindRose_TableOut[11][3]],
        ["WSW", WindRose_TableOut[12][3]],
        ["W", WindRose_TableOut[13][3]],
        ["WNW", WindRose_TableOut[14][3]],
        ["NW", WindRose_TableOut[15][3]],
        ["NNW", WindRose_TableOut[16][3]],
       ],
        color: 'rgba(109, 196, 90, 1)'   
        },{
      name: WindRose_TableOut[0][2],
        data: [
        ["N", WindRose_TableOut[1][2]],
        ["NNE", WindRose_TableOut[2][2]],
        ["NE", WindRose_TableOut[3][2]],
        ["ENE", WindRose_TableOut[4][2]],
        ["E", WindRose_TableOut[5][2]],
        ["ESE", WindRose_TableOut[6][2]],
        ["SE", WindRose_TableOut[7][2]],
        ["SSE", WindRose_TableOut[8][2]],
        ["S", WindRose_TableOut[9][2]],
        ["SSW", WindRose_TableOut[10][2]],
        ["SW", WindRose_TableOut[11][2]],
        ["WSW", WindRose_TableOut[12][2]],
        ["W", WindRose_TableOut[13][2]],
        ["WNW", WindRose_TableOut[14][2]],
        ["NW", WindRose_TableOut[15][2]],
        ["NNW", WindRose_TableOut[16][2]],
       ],
        color: 'rgba(199, 232, 191, 1)'
          }, {  
        name: WindRose_TableOut[0][1],
        data: [
        ["N", WindRose_TableOut[1][1]],
        ["NNE", WindRose_TableOut[2][1]],
        ["NE", WindRose_TableOut[3][1]],
        ["ENE", WindRose_TableOut[4][1]],
        ["E", WindRose_TableOut[5][1]],
        ["ESE", WindRose_TableOut[6][1]],
        ["SE", WindRose_TableOut[7][1]],
        ["SSE", WindRose_TableOut[8][1]],
        ["S", WindRose_TableOut[9][1]],
        ["SSW", WindRose_TableOut[10][1]],
        ["SW", WindRose_TableOut[11][1]],
        ["WSW", WindRose_TableOut[12][1]],
        ["W", WindRose_TableOut[13][1]],
        ["WNW", WindRose_TableOut[14][1]],
        ["NW", WindRose_TableOut[15][1]],
        ["NNW", WindRose_TableOut[16][1]],
       ],
        color: 'rgba(191, 232, 225, 0.5)'
       
        }]

});




//------------------------display Speed monitoring Last 2H --------------------//

// Create array for to display wind direction in series (Not yet developped)

var WinDir_Table= [];
var Temp_Table= [];
var length = (N.length) - 1;
for (var i=0; i<=length; i++) {
    Temp_Table[i]=[N[i],NE[i],E[i],SE[i],S[i],SO[i],O[i],NO[i]];
    var Temp_Table_max = Math.max(...(Temp_Table));
    var isLargeNumber = (element) => element == Temp_Table_max;       //Find pointer value of highest element
    var Wind_pointer = Temp_Table.findIndex(isLargeNumber);
    switch (Wind_pointer) {
        case 0: 
            WinDir_Table[i] = 270;
            break;
        case 1: 
            WinDir_Table[i] = 315;
            break;
        case 2: 
            WinDir_Table[i] = 0;
            break;
        case 3: 
            WinDir_Table[i] = 45;
            break;
        case 4: 
            WinDir_Table[i] = 90;
            break;
        case 5: 
            WinDir_Table[i] = 135;
            break;
         case 6: 
            WinDir_Table[i] = 180;
            break;
         case 7: 
            WinDir_Table[i] = 225;
            break;
    }
}
//console.log('Wind_Dir table = ');
//console.log(WinDir_Table);


// Start creation of the graph

speed_min_2 = MergeArray(reading_timer, speed_min);
speed_avg_2 = MergeArray(reading_timer, speed_avg);
speed_max_2 = MergeArray(reading_timer, speed_max);
speed_range = Merge3Array(reading_timer, speed_min, speed_max);
//console.log('speed_range = ');
//console.log(speed_range);
WindDir_Table_2 = MergeArray(reading_timer, WinDir_Table);

var speed_max_cut = speed_max.slice(-30); // keep only last 30 value = 2hour
var maxspeed = Math.max(...speed_max_cut);
maxspeed = (maxspeed/10)+0.5;
maxspeed=10*(Math.round(maxspeed));
if (maxspeed < 20){ maxspeed = 20};
console.log('maxspeed:');
console.log(maxspeed)


var chartT = new Highcharts.Chart({
    chart:{ 
  		renderTo : 'speed_chart_2h',
  		alignTicks: false,
	//	height: (3.5 / 10 * 100) + '%' 
	    height:  '40%' 
      
        },
    title: { text: '' },
 
    subtitle: {
        //text: NowDay+'/'+ NowMonth+'/'+NowYear
        },
    legend: {
        enabled: false
        },
    tooltip: {
        crosshairs: true,
        shared: true,
        valueSuffix: 'km/h'
        },  
  series: [
			
            {
                data: speed_avg_2,
				//name: 'Avg',
				zIndex: 1,
				yAxis: 0,
				color: 'blue',
				lineWidth: 1,
				type: 'spline',
                marker: {
                symbol: 'cross',
                lineColor: null,
                lineWidth: 2,
            }
        //  marker: {
               //     useHTML: true,
               //     symbol: '<p class="arrow2"; style="-webkit-transform:rotate(225deg) -size: 3px;">&#10229;</p>',
               //    lineColor: null,
               //     lineWidth: 2
               //     }
            },
            {
                name: 'Range',
                data: speed_range,
                type: 'arearange',
                lineWidth: 0,
                linkedTo: ':previous',
                //color: 'red',
                fillOpacity: 0.3,
                zIndex: 0,

                
            },
		],
   
    
  plotOptions: {
                line: { 
                    animation: false,
                    dataLabels: { enabled: true }
                    },
                
				series: {
					marker: {enabled: false},
					animation: {duration: 1000}
				    }
		    	},
 
 xAxis: {
    type: 'datetime',
    min: Date.UTC(NowYear, NowMonth, NowDay, NowHourMinus2, NowMin),
    max: Date.UTC(NowYear, NowMonth, NowDay, NowHour, NowMin),

    tickInterval: 900 * 1000,  // 900 sec = 15min
    gridLineWidth: 1,
    labels: {
            rotation: -45,
            formatter: function () {                
                var time = Highcharts.dateFormat('%H:%M', this.value);
                return time;
                }
            }
 },
yAxis: { // 1er yAxis (numero 0)
             gridLineWidth: 1,
             tickInterval: 5,
             min: 0,
             max: maxspeed,
            startOnTick: false,
            labels: {
                formatter: function() {return this.value;},
                style: {color: 'dimgrey'}
                },
            title: {
                text: '',
                style: {color: 'black'}
                },
        },
credits: { enabled: false }
});

//------------------------display Speed monitoring whole day --------------------//

speed_avg_2 = MergeArray(reading_timer, speed_avg);
speed_range = Merge3Array(reading_timer, speed_min, speed_max);

var speed_max_cut = speed_max.slice(-120); // keep only last 120 value = 8hour
var maxspeed = Math.max(...speed_max_cut);
maxspeed = (maxspeed/10)+0.5;
maxspeed=10*(Math.round(maxspeed));
if (maxspeed < 20){ maxspeed = 20};


var chartT = new Highcharts.Chart({
  chart:{ 
  		renderTo : 'speed_chart_day',
		height: '40%' 
      
  },
  title: { text: '' },
    legend: {
        enabled: false
    },
    subtitle: {
       // text: NowDay+'/'+ NowMonth+'/'+NowYear
    },
    
  series: [{
 
				data: speed_avg_2,
				name: 'Avg',
				zIndex: 1,
				yAxis: 0,
				color: 'blue',
				lineWidth: 1,
				type: 'spline',
                marker: {
                symbol: 'cross',
                lineColor: null,
                lineWidth: 2
                }
				},
				   {
               name: 'Range',
                data: speed_range,
                type: 'arearange',
                lineWidth: 0,
                linkedTo: ':previous',
                //color: 'red',
                fillOpacity: 0.3,
                zIndex: 0,
				},
			
				],
   
    
  plotOptions: {
                line: { animation: false,
                        dataLabels: { enabled: true }
                    },
                
				series: 
				{
					marker: {
					enabled: false
					},animation: {
					duration: 1000}
				}
			},
 
 xAxis: {
    type: 'datetime',
    min: Date.UTC(NowYear, NowMonth, NowDay, WakeUp),
    max: Date.UTC(NowYear, NowMonth, NowDay, StartSleep),

    tickInterval: 900 * 1000,  // 900 sec = 15min
    gridLineWidth: 1,
    labels: {
            rotation: -45,
            formatter: function () {                
                var time = Highcharts.dateFormat('%H:%M', this.value);
                return time;
            }
        }
 },
yAxis: { // 1er yAxis (numero 0)
             gridLineWidth: 1,
             tickInterval: 10,
             min: 0,
             max: maxspeed,
            startOnTick: false,
            labels: {formatter: function() {return this.value;
                            },
                     style: {
                            color: 'dimgrey'
                                }
  
                   },
               title: {
                      text: '',
                      style: {color: 'black'}
               },
        },
        credits: { enabled: false }
});

//-----------------display voltage input  ---------------

// Prepare data 
voltage_battery_2 = MergeArray(reading_timer, voltage_battery);


// Manage highchart

var chartVolt2 = new Highcharts.Chart({
  chart:{ 
      renderTo:'voltage_monitoring', 
      height:  '40%',
        alignTicks: false,
        },
  title: { text: 'Battery monitoring : '},
      legend: {
        enabled: false
    },
    subtitle: {
        //text: NowDay+'/'+ NowMonth+'/'+NowYear
    },
  series: [{
                    
                
    //             data: voltage_solar_2,
				// name: 'Voltage Solar Panel',
				// zIndex: 1,
				// yAxis: 0,
				// color: 'green',
				// lineWidth: 2,
				// type: 'spline',
								
				// },
    //                 {
         
				name: 'Voltage Battery',
				zIndex: 1,
				yAxis: 0,
				color: 'red',
				lineWidth: 2,
				type: 'spline',
				data: voltage_battery_2,				
				},
				//   {
		
				// name: 'Voltage 5VDC',
				// zIndex: 1,
				// yAxis: 0,
				// color: 'blue',
				// lineWidth: 2,
				// type: 'spline',
				// data: voltage_input_2,

				// },
			
				],
   
    
  plotOptions: {
                line: { animation: false,
                        dataLabels: { enabled: true }
                    },
                
				series: 
				{
					marker: {
					enabled: false
					},animation: {
					duration: 1000}
				}
			},
 
 xAxis: {
    type: 'datetime',
    min: Date.UTC(NowYear, NowMonth, NowDay, WakeUp),
    max: Date.UTC(NowYear, NowMonth, NowDay, StartSleep),

    tickInterval: 900 * 1000,  // 900 sec = 15min
    gridLineWidth: 1,
    labels: {
            rotation: -45,
            formatter: function () {                
                var time = Highcharts.dateFormat('%H:%M', this.value);
                return time;
            }
        }
 },
yAxis: { // 1er yAxis (numero 0)
             gridLineWidth: 1,
             tickInterval: 0.2,
             min: 3,
             max: 4.5,
            startOnTick: false,
            labels: {formatter: function() {return this.value +'V';
                            },
                     style: {
                            color: 'dimgrey'
                                }
  
                   },
               title: {
                      text: 'Voltage',
                      style: {color: 'black'}
               },
        },
        credits: { enabled: false }
});


//---------------------------------- FUNCTION ------------------------------------------------------//

function MergeArray(time, value)
{
	length = time.length;
    OutArray = [] ;
    for (var i=0; i<length; i++) {
        var t = time[i];
        Last_Year = t.substr(0, 4);
        Last_Month = t.substr(5, 2);
        Last_Month = Last_Month - 1; //in js, month 0 = jan and 11 = december
        Last_Day = t.substr(8, 2);
        Last_hour = t.substr(11, 2);
        Last_min = t.substr(14, 2);
        if ((Last_Month == NowMonth ) && (Last_Day == NowDay)){
            if (value[i] == ""){
                value[i] = 0;
                }
    	    str = [Date.UTC(Last_Year, Last_Month, Last_Day, Last_hour, Last_min), value[i]];
            OutArray.push(str);
            }
        }
    return OutArray;
    }

function Merge3Array(time, value1, value2)
{
	length = time.length;
    OutArray = [] ;
    for (var i=0; i<length; i++) {
        var t = time[i];
        Last_Year = t.substr(0, 4);
        Last_Month = t.substr(5, 2);
        Last_Month = Last_Month - 1; //in js, month 0 = jan and 11 = december
        Last_Day = t.substr(8, 2);
        Last_hour = t.substr(11, 2);
        Last_min = t.substr(14, 2);
        if ((Last_Month == NowMonth ) && (Last_Day == NowDay)){
            if (value1[i] == ""){
                value1[i] = 0;
                }
            if (value2[i] == ""){
                value2[i] = 0;
                }
    	    str = [Date.UTC(Last_Year, Last_Month, Last_Day, Last_hour, Last_min), value1[i], value2[i]];
            OutArray.push(str);
            }
        }
    return OutArray;
    }    
function FilterArray(time, value)   // Not used
{

	length = time.length;
    OutArray = [] ;
    for (var i=0; i<length; i++) {
        var t = time[i];
        Last_Year = t.substr(0, 4);
        Last_Month = t.substr(5, 2);
        Last_Month = Last_Month - 1; //in js, month 0 = jan and 11 = december
        Last_Day = t.substr(8, 2);
        Last_hour = t.substr(11, 2);
        Last_min = t.substr(14, 2);
        if ((Last_Month == NowMonth ) && (Last_Day == NowDay)){
    	    data = value[i];
            OutArray.push(data);
            }else{
             data = 0;
            OutArray.push(data);
        }
    }
    return OutArray;
    }


function round5(x)
{
    return Math.ceil(x/5)*5;
}

function ArrayFilter(array, min, max){   // Not used
    	length = array.length;
        OutArray = [] ;
        for (var i=0; i<length; i++) {
               if ((array[i] > min ) && (array[i] < max )){
    	            value = array[i];
               }else{
                    value = 0;
                    }
                OutArray.push(value);
        }
    return OutArray;
    
}
function WindRose_Table_Builder(speed, direction, max){   // Not used
    	length = array.length;
        OutArray = [] ;
        for (var i=0; i<length; i++) {
               if ((array[i] > min ) && (array[i] < max )){
    	            value = array[i];
               }else{
                    value = 0;
                    }
                OutArray.push(value);
        }
    return OutArray;
    
}
function showDiv(DivName) {
   document.getElementById(DivName).style.display = "block";
}

function avg_array_last5(array, num){
    length = array.length;
    start = length - num;
    avg_array = array.slice(start, length);
    var total = 0;
    for(var i = 0; i < avg_array.length; i++) {
    total += avg_array[i];
    }
    var avg = (total / avg_array.length).toFixed(2);
    return avg
}


</script>

</body>
</html>