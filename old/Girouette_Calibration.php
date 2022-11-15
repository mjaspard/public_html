 <?php
 
 
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
		
		
// $AnalogVal_N = GetParam(AnalogVal_N);
// $AnalogVal_NE = GetParam(AnalogVal_NE);
// $AnalogVal_E = GetParam(AnalogVal_E);
// $AnalogVal_SE = GetParam(AnalogVal_SE);
// $AnalogVal_SO = GetParam(AnalogVal_SO);
// $AnalogVal_O = GetParam(AnalogVal_O);
// $AnalogVal_NO = GetParam(AnalogVal_NO);
// $AnalogVal_S = GetParam(AnalogVal_Sud);

$AnalogVal_NO = GetParam(AnalogVal_N);
$AnalogVal_N = GetParam(AnalogVal_NE);
$AnalogVal_NE = GetParam(AnalogVal_E);
$AnalogVal_E = GetParam(AnalogVal_SE);
$AnalogVal_SE = GetParam(AnalogVal_Sud);
$AnalogVal_S = GetParam(AnalogVal_SO);
$AnalogVal_SO = GetParam(AnalogVal_O);
$AnalogVal_O = GetParam(AnalogVal_NO);



echo $AnalogVal_N."<br>";
echo $AnalogVal_NE."<br>";
echo $AnalogVal_E."<br>";
echo $AnalogVal_SE."<br>";
echo $AnalogVal_S."<br>";
echo $AnalogVal_SO."<br>";
echo $AnalogVal_O."<br>";
echo $AnalogVal_NO."<br>";

 
$address = '/home2/windspot/public_html/json/id/GirouetteCal.html';
    
    
$json = json_encode(array('N' => $AnalogVal_N, 'NE' => $AnalogVal_NE, 'E' => $AnalogVal_E, 'SE' => $AnalogVal_SE, 'S' => $AnalogVal_S, 'SO' => $AnalogVal_SO, 'O' => $AnalogVal_O, 'NO' => $AnalogVal_NO), true);
file_put_contents($address, $json);



?>