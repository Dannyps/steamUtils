<?php

namespace Dannyps\Steam;

require("steamID.class.php");

/**
 * @brief Useful functions to convert SteamIDs and extract information from %Steam about Users.
 * There are several representations for steam IDs.
 *
 * @author Daniel Silva <mail@dannyps.net>
 * 
 * @warning Instantiating this class will result in additional loading time for the user as an XML will be fetched from the steam servers.
 * 
 */
class SteamUser extends SteamID{

	/** @brief The XML content corresponding to the user's page. */
	private $XML=NULL;

	/** @brief parse the STeamID and fetch the XML content from the steam Servers. */
	public function __construct($id){
		parent::__construct($id);
		$this->getXML();
	}

	/** @brief get the XML content from the steam Servers, if we don't have it already. */
	private function getXML(){
		if($this->XML==NULL){ // we don't have it yet.
			$this->XML = new \SimpleXMLElement(file_get_contents("http://steamcommunity.com/profiles/".$this->steamID."/?xml=1"));
		}
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
	 * @todo docs
	 * @todo review returned timestamp
	 * 
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
