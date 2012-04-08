<?php
/**
* 
*/
class Br_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$config = $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
		if(!$config->production->acl->use) {
			return false;
		}
		if(
			($request->getModuleName() == 'user' and $request->getActionName() == 'index' and $request->getControllerName() == 'index')
		) { return true; }
		
		$acl = new Br_Acl_Acl();
		$loggedUser = Zend_Auth::getInstance()->getIdentity();
		if(!$loggedUser) {
			$roleId = 1;
		} else {
			$roleId = $loggedUser->aclrole_id;
		}
		
	    if($acl->isRoleAllowed($roleId, $request, null) === false) {
			//If the user has no access we send him elsewhere by changing the request
			$messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			$messenger->addMessage(array('warning' => 'You dont have access here, please login'));
			$request->setModuleName('user')
					->setControllerName('index')
					->setActionName('index')
					->setDispatched(false);
			return false;
		}
	}
}

?>