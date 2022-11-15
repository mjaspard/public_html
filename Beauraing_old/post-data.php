<?php
/*
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com
  
  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.
  
  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
*/

$servername = "localhost";

// REPLACE with your Database name
$dbname = "windspot_wind_data";
// REPLACE with Database user
$username = "windspot_max";
// REPLACE with Database user password
$password = "Massul_34";

// Keep this API Key value to be compatible with the ESP32 code provided in the project page. If you change this value, the ESP32 sketch needs to match
$api_key_value = "tPmAT5Ab3j7F9";

$api_key = $N = $NNE = $NE = $ENE = $E = $ESE = $SE = $SSE = $S = $SSO = $SO = $OSO = $O = $ONO = $NO = $NNO = "";

 
    $api_key = test_input($_POST["api_key"]);
    echo "api key from POST = " . $api_key;
    
    
    if ($api_key == $api_key_value) {
        $N = test_input($_POST["N"]);
        $NNE= test_input($_POST["NNE"]);
        $NE = test_input($_POST["NE"]);
        $ENE = test_input($_POST["ENE"]);
        $E= test_input($_POST["E"]);
        $ESE = test_input($_POST["ESE"]);
        $SE= test_input($_POST["SE"]);
        $SSE = test_input($_POST["SSE"]);
        $S = test_input($_POST["S"]);
        $SSO= test_input($_POST["SSO"]);
        $SO = test_input($_POST["SO"]);
        $OSO = test_input($_POST["OSO"]);
        $O= test_input($_POST["O"]);
        $ONO = test_input($_POST["ONO"]);
        $NO= test_input($_POST["NO"]);
        $NNO = test_input($_POST["NNO"]);
        $speed_max = test_input($_POST["speed_max"]);
        $speed_min = test_input($_POST["speed_min"]);
        $speed_avg = test_input($_POST["speed_avg"]);
        $sensor_avg = test_input($_POST["sensor_avg"]);
        $VoltageBattery= test_input($_POST["VoltageBatteryRealVal"]);
        $VoltageSolar= test_input($_POST["VoltageSolRealVal"]);
        $VoltageInput= test_input($_POST["VoltageInputRealVal"]);
        $SleepInfo= test_input($_POST["SleepInfo"]);
        $SleepTime= test_input($_POST["SleepTime"]);
        
        // --------------- SEND WIND DATA TO MYSQL -----------//
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                 } 
        
        $sql = "INSERT INTO BEAURAING (N, NNE, NE, ENE, E, ESE, SE, SSE, S, SSO, SO, OSO, O, ONO, NO, NNO, speed_max, speed_min, speed_avg, sensor_avg, voltage_battery, voltage_solar, voltage_input)    
        VALUES ('" . $N . "', '" . $NNE . "','" . $NE . "', '" . $ENE . "', '" . $E . "', '" . $ESE . "', '" . $SE . "', '" . $SSE . "', '" . $S . "', '" . $SSO . "', '" . $SO . "', '" . $OSO . "', '" . $O . "', '" . $ONO . "', '" . $NO . "', '" . $NNO . "','" . $speed_max . "','" . $speed_min . "','" . $speed_avg . "','" . $sensor_avg . "','" . $VoltageBattery . "','" . $VoltageSolar . "','" . $VoltageInput . "')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            } 
            else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            }
    
    
        // --------------- SEND DATA TO WEBPAGE FOR SLEEP INFO -----------//
    
        $data_file = fopen("SleepInfo.txt", "w");
        $text = "Sleep mode activated for a period of ".$SleepTime." minutes \r Info: ".$SleepInfo;
        $text_to_write = $text." ".$SleepInfo;
        fwrite($data_file, $text);
        fclose($data_file);
    
    
    
        $conn->close();
    }
    else {
        echo "Wrongg API Key provided.";

    }
echo "";



function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}