<?php
/**
* Class for returning info about logged user into system (if any).
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Br_View_Helper_LoggedUser extends Zend_View_Helper_Abstract
{
	private $_identity = null;
	
	public function LoggedUser($property = null)
	{
		return $this->getIdentity($property);
	}
	
	public function setIdentity($identity) 
	{
		if(!empty($identity)) $this->_identity = $identity;
		return $this;
	}
	
	public function getIdentity($property = null)
	{
		$this->_getUser();
		if(!$this->_identity) return false;
		if($property === null) return $this->_identity;
		return $this->_identity->$property;
	}
	
	private function _getUser()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()) {
			$this->setIdentity($auth->getIdentity());
		} else {
			return false;
		}
	}

}
?>