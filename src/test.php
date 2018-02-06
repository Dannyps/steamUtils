<?php
$time_start = microtime(true); 
require_once("steamUser.class.php");


use \Dannyps\Steam;

var_dump( (new Steam\SteamUser(76561198089393237))->getMostPlayedGames());
var_dump( (new Steam\SteamUser('dannyps'))->getMostPlayedGames());
#echo (new SteamID("103582791429521412"))->profileInfo->getSteamName();
 

echo PHP_EOL;
//------------------- script end

$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo 'Total Execution Time: '.$execution_time.'s';