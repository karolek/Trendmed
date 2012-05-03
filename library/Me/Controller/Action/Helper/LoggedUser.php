<?php 
class Me_Controller_Action_Helper_LoggedUser extends Zend_Controller_Action_Helper_Abstract {
	private $_identity = null;
	
	public function direct($property = null)
	{
		$viewHelper = new Me_User_View_Helpers_LoggedUser();
		return $viewHelper->getIdentity($property);
	}

}