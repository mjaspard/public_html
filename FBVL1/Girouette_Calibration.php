 <?php
 
 
 function GetParam($data)
		{
			$paramfile = fopen("parameters.txt","r");
			while(!feof($paramfile)) 
			{
			$line = fgets($paramfile);
	        if (preg_match("/$data$/", $line))
		//	if (preg_match("/$data/i", $line))
				{
				$value = strstr($line, '#', true);
				$value=preg_replace('/\s+/', '', $value); 
				return $value;
		
				}
			}
		fclose($paramfile);
		}
		
$Site = GetParam('Site');
	
 $AnalogVal_N = GetParam("AnalogVal_N");
 $AnalogVal_NNE = GetParam("AnalogVal_NNE");
 $AnalogVal_NE = GetParam("AnalogVal_NE");
 $AnalogVal_ENE = GetParam("AnalogVal_ENE");
 $AnalogVal_E = GetParam("AnalogVal_E");
 $AnalogVal_ESE = GetParam("AnalogVal_ESE");
 $AnalogVal_SE = GetParam("AnalogVal_SE");
 $AnalogVal_SSE = GetParam("AnalogVal_SSE");
 $AnalogVal_S = GetParam("AnalogVal_S");
 $AnalogVal_SSO = GetParam("AnalogVal_SSO");
 $AnalogVal_SO = GetParam("AnalogVal_SO");
 $AnalogVal_OSO = GetParam("AnalogVal_OSO");
 $AnalogVal_O = GetParam("AnalogVal_O");
 $AnalogVal_ONO = GetParam("AnalogVal_ONO");
 $AnalogVal_NO = GetParam("AnalogVal_NO");
 $AnalogVal_NNO = GetParam("AnalogVal_NNO");

// $AnalogVal_SSE = GetParam(AnalogVal_N);
// $AnalogVal_S = GetParam(AnalogVal_NNE);
// $AnalogVal_SSO = GetParam(AnalogVal_NE);
// $AnalogVal_SO = GetParam(AnalogVal_ENE);
// $AnalogVal_OSO = GetParam(AnalogVal_E);
// $AnalogVal_O = GetParam(AnalogVal_ESE);
// $AnalogVal_ONO = GetParam(AnalogVal_SE);
// $AnalogVal_NO = GetParam(AnalogVal_SSE);
// $AnalogVal_NNO = GetParam(AnalogVal_S);
// $AnalogVal_N = GetParam(AnalogVal_SSO);
// $AnalogVal_NNE = GetParam(AnalogVal_SO);
// $AnalogVal_NE = GetParam(AnalogVal_OSO);
// $AnalogVal_ENE = GetParam(AnalogVal_O);
// $AnalogVal_E = GetParam(AnalogVal_ONO);
// $AnalogVal_ESE = GetParam(AnalogVal_NO);
// $AnalogVal_SE = GetParam(AnalogVal_NNO);



echo $AnalogVal_N."<br>";
echo $AnalogVal_NNE."<br>";
echo $AnalogVal_NE."<br>" ;
echo $AnalogVal_ENE."<br>" ;
echo $AnalogVal_E."<br>" ;
echo $AnalogVal_ESE."<br>";
echo $AnalogVal_SE."<br>" ;
echo $AnalogVal_SSE."<br>" ;
echo $AnalogVal_S."<br>" ;
echo $AnalogVal_SSO."<br>" ;
echo $AnalogVal_SO."<br>";
echo $AnalogVal_OSO."<br>";
echo $AnalogVal_O."<br>";
echo $AnalogVal_ONO."<br>";
echo $AnalogVal_NO."<br>" ;
echo $AnalogVal_NNO."<br>" ;

 
$address = '/home2/windspot/public_html/'.$Site.'/json/GirouetteCal.html';
 
 // Write calibration value with json format to be read later by te station   
    
$json = json_encode(array('N' => $AnalogVal_N, 'NNE' => $AnalogVal_NNE,'NE' => $AnalogVal_NE, 'ENE' => $AnalogVal_ENE, 'E' => $AnalogVal_E, 'ESE' => $AnalogVal_ESE, 'SE' => $AnalogVal_SE, 'SSE' => $AnalogVal_SSE, 'S' => $AnalogVal_S, 'SSO' => $AnalogVal_SSO, 'SO' => $AnalogVal_SO, 'OSO' => $AnalogVal_OSO, 'O' => $AnalogVal_O, 'ONO' => $AnalogVal_ONO, 'NO' => $AnalogVal_NO, 'NNO' => $AnalogVal_NNO), true);
file_put_contents($address, $json);



?>