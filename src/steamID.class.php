<?php

namespace Dannyps\Steam;

/**
 * @brief Useful functions to convert SteamIDs.
 
 * \anchor steamIDFormats
 * There are several representations for steam IDs.
 * 
 * They are the following:
 * 
 * 
 *  |Name       | Example                | Regex                     	| Supports clans
 *  |:---       | :------------          | :---                      	| :------------------------------
 *  |steamID    | STEAM_1:1:31739929     | `^STEAM_(\d):(\d):(\d*)$` 	| no (individual user by default)
 *  |steamID3(2)| [U:1:63479859]         | `^\[(U\|g):1:(\d*)\]$` 		| yes
 *  |steamID64  | 76561198023745587      | `^\d{17,18}$`                | yes
 * 
 * Reference: https://developer.valvesoftware.com/wiki/SteamID
 * @author Daniel Silva <mail@dannyps.net>
 *  
 */
class SteamID{

    /** @brief The internal representation is the SteamID64 */
	protected $steamID;

	/** @brief **true** if the SteamID represents a user, **false** otherwise (a group). */
     private $user;

    /** @param $id a steamID in any of the three formats supported.
     * @throw -1 on invalid %SteamID.
     * */
    public function __construct($id){
        if(preg_match("/^STEAM_(\d):(\d):(\d*)$/", $id, $matches)){ // If we're dealing with a SteamID
            // A SteamID always represents a user, and never a clan/group.
            $this->steamID = $matches[3]*2+$matches[2]+0x0110000100000000;
            $this->user=1;
        }elseif(preg_match("/^\[(U|g):1:(\d*)\]$/", $id, $matches)){
            if($matches[1]=='U'){
				$V=0x0110000100000000;
				$this->user=1;
            }else{
				$V=0x0170000000000000;
				$this->user=0;
			}

			// W = Z * 2 + Y ------ steamID3
			// W = Z * 2 + Y + v -- steamID64

			$this->steamID=$matches[2] + $V;

        }elseif(preg_match("/^\d{17,18}$/", $id, $matches)){
			$this->steamID=$matches[0];
			$this->user=$this->isUserBySteamID64($matches[0]);
        }else{
            throw new \Exception("The SteamID passed is not valid!", -1);
		}
		
	}
	
	/**
	 * @brief determines whether the passed steamID64 belongs to a user or a group.
	 * @return **true** if user, **false** otherwise.
	 */
	private function isUserBySteamID64($w){
		if ($w - 0x0170000000000000 < 0){ // user
			return true;
		}else{ // group/clan
			return false;
		}
	}

    /** @return **true** if the SteamID represents a user, **false** otherwise (a group). */
     public function isUser()
     {
          return $this->user;
     }

    /** @return **true** if the SteamID represents a group, **false** otherwise (a user). */
	public function isGroup()
	{
		 return !$this->isGroup;
	}

	/** @return the steamID64 for the current steamID. */
	public function getSteamID64()
	{
		 return $this->steamID;
	}

	/** @return the steamID3 for the current steamID. */
	public function getSteamID3()
	{
		// W = Z * 2 + Y ------ steamID3
		// W = Z * 2 + Y + v -- steamID64	
		$W = $this->steamID;
		//echo $W%2;

		if ($this->isUserBySteamID64($W)){
			// user
			return "[U:1:" . ($W - 0x0110000100000000) . "]";
		}else{
			// group/clan
			return "[g:1:" . ($W - 0x0170000000000000) . "]";
		}
		return 0;
	}

	/** @return the steamID for the current steamID.
	 * @warning If the current steamID belongs to a group, this will fail.
	 * @throws -2 the current steamID belongs to a group, and cannot be represented in this format.
	*/
	public function getSteamID()
	{
		if(!$this->user){
			throw new \Exception("The current SteamID represents a group/clan, and cannot be expressed in this fashion! Use steamID3 or steamID64 instead.", -2);
		}

		$W = $this->steamID - 0x0110000100000000;
		if($W%2){
			$Z=($W-1)/2;
			$Y=1;
		}else{
			$Z=$W/2;
			$Y=0;
		}

		return "STEAM_1:" . $Y . ":" . $Z;
	}

}


?>