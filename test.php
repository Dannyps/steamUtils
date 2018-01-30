<?php

require_once("steamUser.class.php");


use \Dannyps\Steam;

var_dump( (new Steam\SteamUser("dandnyps"))->getPrimaryGroupName());
#echo (new SteamID("103582791429521412"))->profileInfo->getSteamName();
 
echo PHP_EOL;