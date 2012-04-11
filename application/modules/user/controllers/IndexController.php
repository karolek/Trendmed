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
		$request = $this->getRequest();
		$form = new User_Form_Login();
		$form->setDecorators(array(
            array('ViewScript', array('viewScript' => 'index/loginForm.phtml'))
        ));

		if($request->isPost()) {
			if ($form->isValid($request->getPost())) {
				$values = $form->getValues();
				
				// we first search for that user
				$userMapper = new User_Model_UserMapper();
				$userModel  = $userMapper->findByUsername($values['username']);
				if (!$userModel) { // we didnt find the user in DB
				    $this->_helper->FlashMessenger(array('error' => 'No such user in database as: '.$values['username']));
				} else {
				    // authorzing the user
				    $log = Zend_Registry::Get('log');
				    $log->debug($values);
				    $result = $userModel->authorize($values['password'], $values['rememberMe']);
				    if ($result === true) { // access granted
    					$this->_helper->FlashMessenger->clearCurrentMessages(); // to remove any ACL "You dont have access messages if any"
    					$this->_helper->FlashMessenger(array('success' => 'You have successfully logged in as '.$userModel->getUsername()));
    					$this->_helper->Redirector('index', 'index', 'default');
    				} else {
    					$this->_helper->FlashMessenger(array('error' => 'Wrong login or password supplied'));
    				}
			    }
            } else {
					$this->_helper->FlashMessenger(array('error' => 'Please fill the form'));
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
	
}

