<?php

require_once("steamUser.class.php");


use \Dannyps\Steam;

var_dump( (new Steam\SteamUser("76561198023745587"))->getPrimaryGroupName());
#echo (new SteamID("103582791429521412"))->profileInfo->getSteamName();
 
echo PHP_EOL;