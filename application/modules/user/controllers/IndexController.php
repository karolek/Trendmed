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
        $this->view->headTitle('Login');
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
				    if ($values['rememberMe'] == 1) {
				        $config = Zend_Registry::get('config'); 
				        $rememberMe = $config->usersAccounts->rememberMeTimeinHours;
				    } else {
				        $rememberMe = false;
				    }
				    
				    // authorizing the user
				    $result = $userModel->authorize($values['password'], $rememberMe);
				    if ($result === true) { // access granted
    					$this->_helper->FlashMessenger->clearCurrentMessages(); // to remove any ACL "You dont have access messages if any"
    					$this->_helper->FlashMessenger(array('success' => 'You have successfully logged in as '.$userModel->getUsername()));
    					$this->_helper->Redirector('profile', 'index', 'user');
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
        Zend_Session:: namespaceUnset('Zend_Auth');
		$this->_helper->FlashMessenger(array('info' => 'You have been logged out of the system'));
		$this->_helper->Redirector('index');
	}
	
	public function profileAction()
	{
	    $this->view->headTitle('Your profile dashboard');
        
		$auth = Zend_Auth::getInstance();
		if(!$identity = $auth->getIdentity()) throw new Exception("No user is logged in, so You cant checkout Your profile", 500);
		$this->view->identity = $identity;
	}
	
	public function passwordRecoveryAction()
	{
	   $this->view->headTitle('Password recovery');
	   $form = new User_Form_PasswordRecovery();
	   $request = $this->getRequest();
	   $userMapper = new User_Model_UserMapper();
	   $token = $request->getParam('token');
    
	   if($request->isPost()) {
	       $post = $request->getPost();
	       if($form->isValid($post)) {
	           $values = $form->getValues();
				// we first search for that user
    				$userModel  = $userMapper->findByUsername($values['username']);
    				if (!$userModel) { // we didnt find the user in DB
    				    $this->_helper->FlashMessenger(array('error' => 'No such user in database as: '.$values['username']));
    				} else {
    				    $userModel->generatePasswordRecoveryToken();
    				    $userModel->sendPasswordRecoveryToken();
    				    // we have to save token that our model generated
    				    $userMapper->save($userModel);
       					$this->_helper->FlashMessenger(array('success' => 'Recovery password e-mail send to: '.$userModel->getEmailaddress()));
                        $this->_helper->viewRenderer->setRender('password-recovery-confirm'); 
       				} 
   			} else {
   					$this->_helper->FlashMessenger(array('error' => 'Please fill the form'));
			}
	    } 
	    $this->view->form = $form;
 	   
	    // if user supplied a token in URL
    }
    
    public function newPasswordFromTokenAction()
    {
        $this->view->headTitle('Seting up of a new password');
        
        $request    = $this->getRequest();
        $token      = $request->getParam('token'); // either get or post param
       
        $userMapper = new User_Model_UserMapper();
 	   
        // we have to find user by token
        $user = $userMapper->findByToken($token);
        if(!$user) {
            throw new Zend_Expcetion('No user with this token found in DB', 500);
        }
        
        // checking if token is valid
        if (false === $user->tokenIsValid($token)) {
		    $this->_helper->FlashMessenger(array('error' => 'The token You have supplied in URL 
		    is either incorrect or not valid anymore, please generate new link.'));
		    $this->_helper->Redirector('password-recovery', 'index', 'user');  
        } 
        $form = new User_Form_NewPassword; 
        $form->populate(array('token' => $token));
        
        if ($request->isPost()) {
           if($form->isValid($request->getPost())) {
               $values = $form->getValues();
               $user->setPassword($values['password']);
               $user->setToken(null);
               $user->setTokenValidUntil(null);
               $mapper = $user->getMapper();
               $mapper->save($user);
               $this->_helper->FlashMessenger(array('success' => 'New password set for user: '.$user->getUsername()));
               $this->_helper->Redirector('index', 'index', 'user');
           } else {
               $this->_helper->FlashMessenger(array('warning' => 'There where some errors in the form'));
           }
        }
        $this->view->form = $form;
    }    
}

