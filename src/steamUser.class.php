<?php

namespace Dannyps\Steam;

require("steamID.class.php");

/**
 * @brief Useful functions to convert SteamIDs and extract information from %Steam about Users.\n
 * There are several representations for steam IDs. Check them out at \ref steamIDFormats.\n
 * You can also use the customURL nick defined by the user. (e.g. `http://steamcommunity.com/id/nick`)
 *
 * @author Daniel Silva <mail@dannyps.net>
 * 
 * @warning Instantiating this class will result in additional loading time for the user as an XML will be fetched from the steam servers.
 * 
 */
class SteamUser extends SteamID{

	/** @brief The XML content corresponding to the user's page. */
	private $XML=NULL;

    /** @param $id a steamID in any of the **three formats** supported, or the customURL **nick**.
     * @throw -1 on invalid %SteamID.
     * */
	public function __construct($id){
		try{
			parent::__construct($id);
		}catch (\Exception $e) {
			if($e->getcode()==-1){ //invalid steamID
				// There is one more thing we can try before giving up.
				if($this->getXMLbyURLnick($id)==-1){
					// Looks like the nick was a bad one.
					throw new \Exception("The string passed as SteamID does not identify any user!", -2);
				}
				return;
			}else{
				throw $e;
			}
			
		}
		$this->getXML();
	}

	/** @brief get the XML content from the steam Servers, if we don't have it already. */
	private function getXML(){
		if($this->XML==NULL){ // we don't have it yet.
			$this->XML = new \SimpleXMLElement(file_get_contents("http://steamcommunity.com/profiles/".$this->steamID."/?xml=1"));
		}
	}

	/** @brief get the XML content from the steam Servers, if we don't have it already, but do it using the user's customURL. */
	private function getXMLbyURLnick($nick){
		if($this->XML!=NULL){
			die("Only the constructor can call me! Line: " . __LINE__);
		}

		$this->XML = new \SimpleXMLElement(file_get_contents("http://steamcommunity.com/id/{$nick}/?xml=1"));
		if(isset($this->XML->error)){
			if($this->XML->error->__toString()=="The specified profile could not be found."){
				// This nick is not valid.
				return -1;
			}
			else{
				// Unknown error
				return 1;
			}
		}else{
			// success
			$this->steamID = $this->XML->steamID64;
		}
		return 0;	
	}
	
	/** @brief Forces an update of the XML information currently cached. */
	public function refresh(){
		$this->getXML();
	}

	/** @return the name of the user/clan. */
	public function getSteamName(){
		return $this->XML->steamID->__toString();
	}

	/** @return the name of the user/clan. */
	public function isOnline(){
		return ($this->XML->onlineState=="online");
	}

	/** @return the state message. */
	public function getStateMessage(){
		return $this->XML->stateMessage->__toString();
	}

	/** @return the privacy state of the profile.
	 * Either `public` or `private`.
	*/
	public function getPrivacyState(){
		return $this->XML->privacyState;
	}

	/** @return the visibility state of the profile.
	 * Either `1` (Private, Friends Only, etc), or `3` (Public).
	 * Reference: https://developer.valvesoftware.com/wiki/Steam_Web_API#Public_Data
	*/
	public function getVisibilityState(){
		return $this->XML->visibilityState;
	}

	/**
	 * @return the customURL for this user.
	 * @param $full (defaults to true) whether the returned URL should contain the text "https://steamcommunity.com/id/" or not (thus not being an URL).
	 * */
	public function getCustomURL($full = TRUE){
		if($full)
			return "https://steamcommunity.com/id/".$this->XML->customURL->__toString();
		else
			return $this->XML->customURL->__toString();
	}

	/**
	 * @brief returns the date when the account was created.	
	 * @param bool $timestamp whether the output should be a timestamp, or the steam representation of the date.
	 * */
	public function getMemberSince($timestamp=0){
		if(!$timestamp)
			return $this->XML->memberSince;
		else{
			$parsed = date_parse($this->XML->memberSince);
			return mktime(
				0, 0, 0, 
				$parsed['month'], 
				$parsed['day'], 
				$parsed['year']
			);		
		}
	}

	/**@todo docs */
	public function getHeadline(){
		return $this->XML->headline->__toString();
	}
	
	/**@todo docs */
	public function getLocation(){
		return $this->XML->location->__toString();
	}

	/**@todo docs */
	public function getRealName(){
		return $this->XML->realname->__toString();
	}

	/**@todo docs */
	public function getSummary(){
		return $this->XML->summary->__toString();
	}

	/**@todo docs */
	public function getGroups(){
		$ret = array();
		foreach($this->XML->groups->group as $group){
			array_push($ret, array('isPrimary' => $group->attributes()['isPrimary']->__toString(), 'steamID64' => $group->groupID64->__toString()));
		}
		return $ret;
	}

	/**@todo docs */
	public function getPrimaryGroupId(){
		foreach($this->getGroups() as $group){
			if($group['isPrimary'])
				return $group['steamID64'];
		}
		return -1;
	}

	/**@todo docs */
	public function getPrimaryGroupName(){
		foreach($this->XML->groups->group as $group){
			if($group->attributes()['isPrimary']->__toString()==1){
				return $group->groupName->__toString();
			}
		}
		return -1;
	}

}

?>
