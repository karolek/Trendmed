<?php
/**
* Class for returning info about logged user into system (if any).
* 
* @package Br
* @author Bartosz Rychlicki <b@br-design.pl>
*/
class Me_User_View_Helpers_LoggedUser extends Zend_View_Helper_Abstract
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
            $loggedUserInfo = $auth->getIdentity();
            $em = Zend_Registry::get('doctrine')->getEntityManager();
            // constructing namespace for entity 
            $namespaces = explode('\\',$loggedUserInfo['entityNamespace']);
            array_pop($namespaces);
            
            $namespaces[] = ucfirst($loggedUserInfo['roleName']);
            $repoNamespace = implode('\\', $namespaces);
			
            $user = $em->getRepository($repoNamespace)->find($loggedUserInfo['id']);
            $this->setIdentity($user);
		} else {
			return false;
		}
	}

}
?>