<?php

class User_IndexController extends Br_Controller_Action
{

	/**
	 * This is a login action, it uses Zend_Auth compontent to authorize user based on data in DB.
	 *
	 * If the auth is success, then the credentials are stored in SESSION.
	 * 
	 */
    public function indexAction()
    {
		$this->_helper->addPrefix('Br_Controller_Action_Helper');
		
		$request = $this->getRequest();
		$form = new User_Form_Login();
				
		if($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				$values = $form->getValues();
				
				$adapter = $this->_getAuthAdapter();
				$adapter->setIdentity($values['username'])->setCredential($values['password']);
				
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate($adapter);
				
                if($result->isValid()) {
					// access granted
					$user = $adapter->getResultRowObject();
					$auth->getStorage()->write($user);
					$this->_helper->FlashMessenger->clearCurrentMessages(); // to remove any ACL "You dont have access messages if any"
					$this->_helper->FlashMessenger(array('success' => 'Zostałeś zalogowany'));
					$this->_helper->Redirector('index', 'index', 'default');
				} else {
					$this->_helper->FlashMessenger(array('error' => 'Podałeś zły login lub hasło'));
				}
            } else {
					$form->buildBootstrapErrorDecorators();
					$this->_helper->FlashMessenger('Wypełnij poprawnie formularz');
			}
		}
		$this->view->form = $form;
    }

	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_helper->FlashMessenger(array('info' => 'Poprawnie wylogowano z systemu'));
		$this->_helper->Redirector('index');
	}
	
	public function profileAction()
	{
		$auth = Zend_Auth::getInstance();
		if(!$identity = $auth->getIdentity()) throw new Exception("No user is logged in, so You cant checkout Your profile", 500);
		$this->view->identity = $identity;
	}
	
	protected function _getAuthAdapter()
	{
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        
        $authAdapter->setTableName('acluser')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('SHA1(CONCAT(?,salt))');
            
        return $authAdapter;
	}
}

