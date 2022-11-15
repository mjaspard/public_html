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
		
		

// Définit le fuseau horaire par défaut à utiliser. Disponible depuis PHP 5.1
date_default_timezone_set('Europe/Brussels');
$t=time();


$hour = date("H",$t);
echo '<br>read current hour: <br>';
echo $hour;
$min = date("i",$t);
echo '<br>read current minute: <br>';
echo $min;
$month = date("m",$t);
echo '<br>read current month: <br>';
echo $month;

switch ($month){
    
    case "1":
        $StartSleep = GetParam(StartSleep_01);
        $Wake_up = GetParam(WakeUp_01) ;
        break;
    case "2":
        $StartSleep = GetParam(StartSleep_02);
        $Wake_up = GetParam(WakeUp_02) ;
        break;
    case "3":
        $StartSleep = GetParam(StartSleep_03);
        $Wake_up = GetParam(WakeUp_03) ;
         break;       
    case "4":
        $StartSleep = GetParam(StartSleep_04);
        $Wake_up = GetParam(WakeUp_04) ;
        break;
    case "5":
        $StartSleep = GetParam(StartSleep_05);
        $Wake_up = GetParam(WakeUp_05) ;
        break;
    case "6":
        $StartSleep = GetParam(StartSleep_06);
        $Wake_up = GetParam(WakeUp_06) ;
        break;
    case "7":
        $StartSleep = GetParam(StartSleep_07);
        $Wake_up = GetParam(WakeUp_07) ;
        break;
    case "8":
        $StartSleep = GetParam(StartSleep_08);
        $Wake_up = GetParam(WakeUp_08) ;
        break;
    case "9":
        $StartSleep = GetParam(StartSleep_09);
        $Wake_up = GetParam(WakeUp_09) ;
        break;
    case 10:
        $StartSleep = GetParam(StartSleep_10);
        $Wake_up = GetParam(WakeUp_10) ;
        break;
    case "11":
        $StartSleep = GetParam(StartSleep_11);
        $Wake_up = GetParam(WakeUp_11) ;
        break;
    case "12":
        $StartSleep = GetParam(StartSleep_12);
        $Wake_up = GetParam(WakeUp_12) ;
        break;
}
  
 $SleepTime = ((24 - $StartSleep ) + $Wake_up);

        echo '<br>read StartSleepvalue :<br>';
            echo  $StartSleep;
 
     $address = '/home2/windspot/public_html/json/id/Sleep.html';
    
    
if (( $hour >= $StartSleep ) || ( $hour < $Wake_up )){
    if ( $hour < 12 ){
        echo '<br>current time under 12h<br>';
        echo '<br>read Wake_up value :<br>';
        echo  $Wake_up;
        $SleepTime = $Wake_up - $hour;
        $SleepTime = $SleepTime * 60;       // Convert in minutes
        $SleepTime = $SleepTime - $min;     // Minus minutes already passed in this hour
    } else {
        echo '<br>current time after 12h<br>';
        $SleepTime = 24 - $hour + $Wake_up;
        $SleepTime = $SleepTime * 60;       // Convert in minutes
        $SleepTime = $SleepTime - $min;
    }
    $json = json_encode(array('SleepOrAwake' => 'Sleep', 'SleepTime' => $SleepTime), true);
   // $json = '{ "status":"Sleep","timer":"$SleepTime"}';
    file_put_contents($address, $json);
} else {
     $json = json_encode(array('SleepOrAwake' => 'Awake', 'SleepTime' => $SleepTime), true);
    file_put_contents($address, $json); 
}
echo '<br>read sleep time <br>';
 echo  $SleepTime;
?>