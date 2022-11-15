


<?php

echo "<br> restart mercredi 17 vers 14h avec pile li-ion";
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


$CurrentStatus = GetJson('json/id/Sleep.html','SleepOrAwake');
$CurrentStatus = strval($CurrentStatus);
//echo $CurrentStatus."<br>";
//echo gettype($CurrentStatus)."<br>";






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
$sql = "SELECT id from RoseWind where reading_time >= now() - interval 10 minute limit 1";
$result = $conn->query($sql);

  while($row = $result->fetch_assoc()) {
    //echo "id: " . $row["id"]. "<br>";
    $lastId = $row["id"];
  }

//echo " lastID = ".$lastId."<br>";

if ($lastId == ''){
    $BorderColor = 'red';
}else{
    $BorderColor = 'green';
}

//-----------------

$sql = "SELECT N, NE, E, SE, S, SO, O, O, NO, speed_max, speed_min, speed_avg,voltage_battery,voltage_input, voltage_solar, reading_time FROM RoseWind order by reading_time desc limit ".$AwakeDuration;

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
$NE = json_encode(array_reverse(array_column($sensor_data, 'NE')), JSON_NUMERIC_CHECK);
$E= json_encode(array_reverse(array_column($sensor_data, 'E')), JSON_NUMERIC_CHECK);
$SE = json_encode(array_reverse(array_column($sensor_data, 'SE')), JSON_NUMERIC_CHECK);
$S = json_encode(array_reverse(array_column($sensor_data, 'S')), JSON_NUMERIC_CHECK);
$SO= json_encode(array_reverse(array_column($sensor_data, 'SO')), JSON_NUMERIC_CHECK);
$O = json_encode(array_reverse(array_column($sensor_data, 'O')), JSON_NUMERIC_CHECK);
$NO = json_encode(array_reverse(array_column($sensor_data, 'NO')), JSON_NUMERIC_CHECK);
$speed_max = json_encode(array_reverse(array_column($sensor_data, 'speed_max')), JSON_NUMERIC_CHECK);
$speed_min = json_encode(array_reverse(array_column($sensor_data, 'speed_min')), JSON_NUMERIC_CHECK);
$speed_avg = json_encode(array_reverse(array_column($sensor_data, 'speed_avg')), JSON_NUMERIC_CHECK);
$voltage_input = json_encode(array_reverse(array_column($sensor_data, 'voltage_input')), JSON_NUMERIC_CHECK);
$voltage_solar = json_encode(array_reverse(array_column($sensor_data, 'voltage_solar')), JSON_NUMERIC_CHECK);
$voltage_battery = json_encode(array_reverse(array_column($sensor_data, 'voltage_battery')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);

$result->free();
$conn->close();


$myfile = fopen("SleepInfo.txt", "r") ;
$SleepInfo= fread($myfile,filesize("SleepInfo.txt"));
fclose($myfile);

if (strcmp($CurrentStatus, 'Sleep') == 0){

	$val = GetJson('json/id/Sleep.html','SleepTime');
	$RemainTime = "Wake-up in ".$val. " minutes";
	$BorderColor = 'grey';
	}else{
	$RemainTime = "";
	$SleepInfo = "";
	}

?>



<!DOCTYPE html>
<html>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/max2.css" rel="stylesheet">
<!-- 	<link href="css/highcharts.css" rel="stylesheet"> -->
    <link href="css/highchart2.css" rel="stylesheet">
<head><meta charset="euc-kr">
	<!-- Load plotly.js into the DOM -->



 <style>
/* 
.jumbotron .arrow{
color: dark;
position: fixed;
top: 320px;
left: 300px;
font-size: 80px;  
display: inline-block;
lightcoral
}
 */
.wrap {
  border: 3px solid <?php echo $BorderColor; ?>;
  padding: 1rem;
  height: 1800px;
  width: inherit;
  > style {
    position: absolute;
    top: 20%;
    left: 0%;
    transform: translate(-20%, -20%);
  }
}



.child {
  background: #eee;
  border: 0px solid green;
  padding: 1rem;
  height: 25%;
  width: 98%;
}
.child-1 {
  position: absolute;
  top: 0px;
  left: 0px;
width: 50%;

}
.child-2 {
  position: absolute;
  top: 0px;
  right: 0px;
width: 50%;
}
.child-3 {
  position: absolute;
  top: 25%;
  left: 10%;
  width: 80%;
}
.child-4 {
  position: absolute;
  top: 50%;
  left: 10%;
  width: 80%;
}
.child-5 {
  position: absolute;
  top: 75%;
  left: 10%;
  width: 80%;
}
.wrap {
  position: relative;
}

#Last5MinArrow .arrow{
color: black;
  position: -webkit-sticky;
  position: absolute;
top: 30%;
left: -5%;
font-size: 70px;  
}
#Last5MinSpeed{
color: black;
 // position: -webkit-sticky;
  position: absolute;
top: 30%;
border: red;
left: 30%;
font-size: 30px; 
width: 100%;
}
#Last2Hour{
color: black;
  position: -webkit-sticky;
  position: absolute;
top: 10%;
left: 10%;
height: 80%;
width: 80%;
align-content: center;
}

.arrow2{
font-size: 7px;   
}
/*
#voltage_monitoring{
color: black;
 // position: -webkit-sticky;
  position: relative;
top: 0px;
left: 0px;
//width: 1100px;
}
*/
  </style>
</head>

<body>
    
<div class="container" id="HeadPage">
    <div class="jumbotron">
        <h3>Site test</h3>
            <h2><?php echo date("l d M Y"); ?></h2><br></br>
            
            <h4><?php echo "Current station status: ".$CurrentStatus.":   ".$RemainTime."" ?></h4>
            <h4><?php echo " Hours of activation: ".$WakeUp."H to ".$StartSleep."H" ?></h4>
            <h4><?php echo $SleepInfo ?></h4>
            <h4><?php echo " Last data received: ".$readings_time[0] ?></h4>
        

	
        <div class="wrap">
            
  	        <div class="child child-1">
  	            <h5>Last 5 minutes</h5>
    	        <div id="Last5MinArrow"></div>
            	<div id="Last5MinSpeed"></div>
  	        </div>
	   
	   
	        <div class="child child-2">
	            <h5>Last 2 hours</h5>
	            <div id="Last2Hour"></div>
	        </div>
        
            <div class="child child-3">
		        <div id="speed_chart_2h"></div>
	        </div>
	        <div class="child child-4">
		        <div id="speed_chart_day"></div>
	        </div>
	        
		    <div class="child child-5">
	            <div id="voltage_monitoring"></div>
            </div>



</body>
</div>
</div>




<script src='https://cdn.plot.ly/plotly-latest.min.js'></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<!-- <script src="http://code.highcharts.com/modules/exporting.js"></script> -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<!-- <script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
 <script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/vector.js"></script>-->
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>


//----------------- Write PHP variable in Javascript variable ---------------//

var N = <?php echo $N; ?>;
var NE = <?php echo $NE; ?>;
var E = <?php echo $E; ?>;
var SE = <?php echo $SE; ?>;
var S = <?php echo $S; ?>;
var SO = <?php echo $SO; ?>;
var O = <?php echo $O; ?>;
var NO = <?php echo $NO; ?>;
var speed_min = <?php echo $speed_min; ?>;
var speed_max = <?php echo $speed_max; ?>;
var speed_avg = <?php echo $speed_avg; ?>;
var voltage_input = <?php echo $voltage_input; ?>;
var voltage_solar = <?php echo $voltage_solar; ?>;
var voltage_battery = <?php echo $voltage_battery; ?>;
var reading_timer = <?php echo $reading_time; ?>;
var WindRoseDuration = 2;    // Nbr of hours to display wind distribution on wind rose
console.log("speed average:");
console.log(speed_avg);
console.log("speed max:");
console.log(speed_max);
console.log("reading_timer:");
console.log(reading_timer);
console.log("N:");
console.log(N);
console.log("NE:");
console.log(NE);
console.log("E:");
console.log(E);

var d = new Date();
    NowYear = d.getFullYear();
    NowMonth = d.getMonth();
    NowDay = d.getDate();
    NowHour = d.getHours();
    NowMin = d.getMinutes();

var maxspeed = Math.max(...speed_max);
maxspeed = (maxspeed/10)+0.5;
maxspeed=10*(Math.round(maxspeed));
console.log('maxspeed:');
console.log(maxspeed)
 
// Create variable to scale the speed monitoring on last 2 hours   
var now = new Date();
now.setHours(now.getHours() - 2); // timestamp
NowHourMinus2 = now.getHours();

var StartSleep = <?php echo $StartSleep ?>;
var WakeUp = <?php echo $WakeUp ?>;

console.log(now);
console.log("date test")
    

//-----------------display Last record ---------------//
// Prepare data


function SpeedLast5min(){
	
    var speed_min_t1 = speed_min.slice(-1).pop();
    var speed_max_t1 = speed_max.slice(-1).pop();
    var speed_avg_t1 = speed_avg.slice(-1).pop();
return `

<p style="color: red;  text-align: left;">max: ${speed_max_t1}</p>
<p style="color: black;  text-align: left;">avg: ${speed_avg_t1}<a> [km/h]</a></p>
<p style="color: blue;  text-align: left;">min: ${speed_min_t1}</p>
`;
}
document.getElementById("Last5MinSpeed").innerHTML = SpeedLast5min();

function ArrowLast5min(){
ArrayLast5MinDir=[];
    ArrayLast5MinDir[0] = N.slice(-1).pop();
    ArrayLast5MinDir[1]  = NE.slice(-1).pop();
    ArrayLast5MinDir[2]  = E.slice(-1).pop();
    ArrayLast5MinDir[3]  = SE.slice(-1).pop();
    ArrayLast5MinDir[4]  = S.slice(-1).pop();
    ArrayLast5MinDir[5]  = SO.slice(-1).pop();
    ArrayLast5MinDir[6]  = O.slice(-1).pop();
    ArrayLast5MinDir[7]  = NO.slice(-1).pop();

    for (var i=0; i<8; i++){
        ArrayLast5MinDir[i] = (ArrayLast5MinDir[i] / 75);
    }

return `
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[0]}; -webkit-transform:rotate(270deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[1]}; -webkit-transform:rotate(315deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[2]}; -webkit-transform:rotate(0deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[3]}; -webkit-transform:rotate(45deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[4]}; -webkit-transform:rotate(90deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[5]}; -webkit-transform:rotate(135deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[6]}; -webkit-transform:rotate(180deg);">&#10229;</p>
<p class="arrow"; style=" opacity:${ArrayLast5MinDir[7]}; -webkit-transform:rotate(225deg);">&#10229;</p>
`;
}
document.getElementById("Last5MinArrow").innerHTML = ArrowLast5min();




//-----------------display wind rose 2 hours  ---------------


var WindRose_TableIn= [];
var length = speed_avg.length;
console.log("speed_avg.length")
console.log(length);

	k=1;
WindRose_TableIn[0] = ["", "N", "NE","E", "SE", "S", "SO", "O", "NO" ];
for (var i=1; i<=length; i++) {
		j = i-1;
	
	    var t_WR = reading_timer[j];
        var Last_hour_WR = t_WR.substr(11, 2);
        var Last_Day_WR = t_WR.substr(8, 2);
        var Last_min_WR = t_WR.substr(14, 2);
        var StartTimeWindRose = NowHour - Last_hour_WR;
        if ((Last_Day_WR == NowDay) && ((StartTimeWindRose < WindRoseDuration) || ((StartTimeWindRose == WindRoseDuration) && (Last_min_WR > NowMin)))){        // Fill the table only if the day of the record is equal to today and within the last 2 hours (or 1 hour if choose by viewer)
            WindRose_TableIn[k]  = [speed_max[j], N[j], NE[j], E[j], SE[j], S[j], SO[j], O[j], NO[j]];
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
        		 WindRose_TableOut[j][7] +=  parseInt(WindRose_TableIn[i][j]);
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

console.log("table_in");
console.log(WindRose_TableIn);
console.log("table_out");
console.log(WindRose_TableOut);


Highcharts.chart('Last2Hour', {

    chart: {
       polar: true,
       type: 'column', 
    
      
     
       
    },
      pane: {
    startAngle: 0,
    endAngle: 360
      },

    title: {
        text: null
    },

   
    legend: {
        align: 'right',
        verticalAlign: 'top',
        y: 50,
        layout: 'vertical'
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
            label = 'NE';
            break;
          case 2:
            label = 'E';
            break;
          case 3:
            label = 'SE';
            break;
          case 4:
            label = 'S';
            break;
          case 5:
            label = 'SW';
            break;
          case 6:
            label = 'W';
            break;
          case 7:
            label = 'NW';
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
            text: 'Wind distribution'
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
        ["NE", WindRose_TableOut[2][8]],
        ["E", WindRose_TableOut[3][8]],
        ["SE", WindRose_TableOut[4][8]],
        ["S", WindRose_TableOut[5][8]],
        ["SO", WindRose_TableOut[6][8]],
        ["O", WindRose_TableOut[7][8]],
        ["NO", WindRose_TableOut[8][8]],
       ],
        color: 'rgba(248, 32, 32, 1)' 
          }, {
      name: WindRose_TableOut[0][7],
        data: [
        ["N", WindRose_TableOut[1][7]],
        ["NE", WindRose_TableOut[2][7]],
        ["E", WindRose_TableOut[3][7]],
        ["SE", WindRose_TableOut[4][7]],
        ["S", WindRose_TableOut[5][7]],
        ["SO", WindRose_TableOut[6][7]],
        ["O", WindRose_TableOut[7][7]],
        ["NO", WindRose_TableOut[8][7]],
       ],
        color: 'rgba(248, 169, 32, 1)'  
           },{
       name: WindRose_TableOut[0][6],
        data: [
        ["N", WindRose_TableOut[1][6]],
        ["NE", WindRose_TableOut[2][6]],
        ["E", WindRose_TableOut[3][6]],
        ["SE", WindRose_TableOut[4][6]],
        ["S", WindRose_TableOut[5][6]],
        ["SO", WindRose_TableOut[6][6]],
        ["O", WindRose_TableOut[7][6]],
        ["NO", WindRose_TableOut[8][6]],
       ],
        color: 'rgba(247, 215, 8, 1)'  
          },{
       name: WindRose_TableOut[0][5],
        data: [
        ["N", WindRose_TableOut[1][5]],
        ["NE", WindRose_TableOut[2][5]],
        ["E", WindRose_TableOut[3][5]],
        ["SE", WindRose_TableOut[4][5]],
        ["S", WindRose_TableOut[5][5]],
        ["SO", WindRose_TableOut[6][5]],
        ["O", WindRose_TableOut[7][5]],
        ["NO", WindRose_TableOut[8][5]],
       ],
        color: 'rgba(64, 174, 45, 1)'  
                        },{
       name: WindRose_TableOut[0][4],
        data: [
        ["N", WindRose_TableOut[1][4]],
        ["NE", WindRose_TableOut[2][4]],
        ["E", WindRose_TableOut[3][4]],
        ["SE", WindRose_TableOut[4][4]],
        ["S", WindRose_TableOut[5][4]],
        ["SO", WindRose_TableOut[6][4]],
        ["O", WindRose_TableOut[7][4]],
        ["NO", WindRose_TableOut[8][4]],
       ],
        color: 'rgba(109, 196, 90, 1)'  
                },{
      name: WindRose_TableOut[0][3],
        data: [
        ["N", WindRose_TableOut[1][3]],
        ["NE", WindRose_TableOut[2][3]],
        ["E", WindRose_TableOut[3][3]],
        ["SE", WindRose_TableOut[4][3]],
        ["S", WindRose_TableOut[5][3]],
        ["SO", WindRose_TableOut[6][3]],
        ["O", WindRose_TableOut[7][3]],
        ["NO", WindRose_TableOut[8][3]],
       ],
        color: 'rgba(199, 232, 191, 1)'   
        },{
      name: WindRose_TableOut[0][2],
        data: [
        ["N", WindRose_TableOut[1][2]],
        ["NE", WindRose_TableOut[2][2]],
        ["E", WindRose_TableOut[3][2]],
        ["SE", WindRose_TableOut[4][2]],
        ["S", WindRose_TableOut[5][2]],
        ["SO", WindRose_TableOut[6][2]],
        ["O", WindRose_TableOut[7][2]],
        ["NO", WindRose_TableOut[8][2]],
       ],
        color: 'rgba(191, 232, 225, 1)'
          }, {  
        name: WindRose_TableOut[0][1],
        data: [
        ["N", WindRose_TableOut[1][1]],
        ["NE", WindRose_TableOut[2][1]],
        ["E", WindRose_TableOut[3][1]],
        ["SE", WindRose_TableOut[4][1]],
        ["S", WindRose_TableOut[5][1]],
        ["SO", WindRose_TableOut[6][1]],
        ["O", WindRose_TableOut[7][1]],
        ["NO", WindRose_TableOut[8][1]],
       ],
        color: 'rgba(191, 232, 225,0.5)'
       
        }]

});




//------------------------display Speed monitoring Last 2H --------------------//

// Create array for to display wind direction in series

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
console.log('Wind_Dir table = ');
console.log(WinDir_Table);


// Start creation of the graph

speed_min_2 = MergeArray(reading_timer, speed_min);
speed_avg_2 = MergeArray(reading_timer, speed_avg);
speed_max_2 = MergeArray(reading_timer, speed_max);
speed_range = Merge3Array(reading_timer, speed_min, speed_max);
console.log('speed_range = ');
console.log(speed_range);
WindDir_Table_2 = MergeArray(reading_timer, WinDir_Table);

var chartT = new Highcharts.Chart({
  chart:{ 
  		renderTo : 'speed_chart_2h',
		height: (3.5 / 10 * 100) + '%' 
      
  },
  title: { text: 'Wind speed last 2 hours' },
 
    subtitle: {
        text: NowDay+'/'+ NowMonth+'/'+NowYear
    },
  
    tooltip: {
    crosshairs: true,
    shared: true,
    valueSuffix: '°C'
  },  
  series: [
			
                    {
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
               // marker: {
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
             min: 0,
             max: maxspeed,
            startOnTick: false,
            labels: {formatter: function() {return this.value +'km/h';
                            },
                     style: {
                            color: 'grey'
                                }
  
                   },
               title: {
                      text: 'Wind Speed',
                      style: {color: 'black'}
               },
        },
        credits: { enabled: false }
});

//------------------------display Speed monitoring whole day --------------------//
speed_min_2 = MergeArray(reading_timer, speed_min);
speed_avg_2 = MergeArray(reading_timer, speed_avg);
speed_max_2 = MergeArray(reading_timer, speed_max);

var chartT = new Highcharts.Chart({
  chart:{ 
  		renderTo : 'speed_chart_day',
		height: (3.5 / 10 * 100) + '%' 
      
  },
  title: { text: 'Wind speed of the day' },
 
    subtitle: {
        text: NowDay+'/'+ NowMonth+'/'+NowYear
    },
    
  series: [{
                    
                
                data: speed_min_2,
				name: 'Min',
				zIndex: 1,
				yAxis: 0,
				color: 'blue',
				lineWidth: 0.5,
				type: 'spline',
				},
                    {
                data: speed_avg_2,
				name: 'Avg',
				zIndex: 1,
				yAxis: 0,
				color: 'green',
				lineWidth: 1,
				type: 'spline',
				},
				   {
		        data: speed_max_2,
				name: 'Max',
				zIndex: 1,
				yAxis: 0,
				color: 'red',
				lineWidth: 0.5,
				type: 'spline',
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
             min: 0,
             max: maxspeed,
            startOnTick: false,
            labels: {formatter: function() {return this.value +'km/h';
                            },
                     style: {
                            color: 'grey'
                                }
  
                   },
               title: {
                      text: 'Wind Speed',
                      style: {color: 'black'}
               },
        },
        credits: { enabled: false }
});

//-----------------display voltage input  ---------------

// Prepare data 
voltage_input_2 = MergeArray(reading_timer, voltage_input);
voltage_solar_2 = MergeArray(reading_timer, voltage_solar);
voltage_battery_2 = MergeArray(reading_timer, voltage_battery);


// Manage highchart

var chartVolt2 = new Highcharts.Chart({
  chart:{ 
      renderTo:'voltage_monitoring', 
        alignTicks: false,
        },
  title: { text: 'Voltages monitoring : '},
    subtitle: {
        text: NowDay+'/'+ NowMonth+'/'+NowYear
    },
  series: [{
                    
                
                data: voltage_solar_2,
				name: 'Voltage Solar Panel',
				zIndex: 1,
				yAxis: 0,
				color: 'green',
				lineWidth: 2,
				type: 'spline',
								
				},
                    {
         
				name: 'Voltage Battery',
				zIndex: 1,
				yAxis: 0,
				color: 'red',
				lineWidth: 2,
				type: 'spline',
				data: voltage_battery_2,				
				},
				   {
		
				name: 'Voltage 5VDC',
				zIndex: 1,
				yAxis: 0,
				color: 'blue',
				lineWidth: 2,
				type: 'spline',
				data: voltage_input_2,

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
             min: 0,
             max: 6,
            startOnTick: false,
            labels: {formatter: function() {return this.value +'V';
                            },
                     style: {
                            color: 'grey'
                                }
  
                   },
               title: {
                      text: 'Voltage',
                      style: {color: 'black'}
               },
        },
        credits: { enabled: false }
});


//----------------- FUNCTION --------------------//

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
function WindRose_Table_Builder(speed, direction, max){
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

</script>

</body>
</html>

