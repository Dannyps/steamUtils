<?php

namespace Dannyps\Steam;


/**
 * @todo docs
 *
 * @author Daniel Silva <mail@dannyps.net>
 * 
 * @warning Instantiating this class may result in additional loading time for the user as some content may need to be fetched from the steam servers.
 * 
 * App IDs are positive integers associated with the respective game. Here's an example table.
 * 
 *  |ID         | Name                   
 *  |:---       | :------------          
 *  |10         | Counter-Strike (1.6)
 *  |220        | Half-Life 2
 *  |240        | Counter-Strike: Source 
 *  |400        | Portal
 *  |306490     | Railroad Pioneer
 * 
 * Reference: https://developer.valvesoftware.com/wiki/Steam_Application_IDs
 * 
 * ### Internal Representation
 * Games are a though one. %Steam gives us information in both the XML and JSON formats, and different information in each one of these. We 
 * 
 */
class SteamGame{

	/** @brief The JSON assoc array containing the game's info. */
    private $JSON=NULL;
    /** @brief The XML Object containing the game's info. */
    private $XML=NULL;

    /** @param $id a *%Steam Appplication ID*, the XML resultant of a previous query, or a game name.
     * @throw -1 on invalid %SteamID.
     * */
	public function __construct($id){
		if(is_integer($id)){ //appID
            $this->__constructFromAppID($id);
        }else if(get_class($id)=="SimpleXMLElement"){
            $this->__constructFromXML($id);
        }else{
            // Assume it's an app name.
            $this->__constructFromAppName($id);
        }
	}

    /**
     * @todo docs
     */
    private function __constructFromAppID($id){
        $this->appID=$id;
        $this->getJSON();
    }

    /**
     * @todo docs
     */
    private function __constructFromXML($xml){
        $this->XML=$xml;
        $elements = explode('/', $this->XML->gameLink->__toString());
        $this->appID=(int) end($elements);
        echo $this->appID.PHP_EOL;
    }

    /**
     * @todo docs
     */
    private function __constructFromAppName($name){
        $this->appID=$id;
        $this->getJSON();
    }

	/** @brief get the JSON content from the steam API, if we don't have it already. */
	private function getJSON(){
		if($this->JSON==NULL){ // we don't have it yet.
			$this->JSON = json_decode(file_get_contents("http://store.steampowered.com/api/appdetails?appids=".$this->appID));
		}
	}

}

?>
