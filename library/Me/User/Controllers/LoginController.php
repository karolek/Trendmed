<?php
abstract class Me_User_Controllers_LoginController extends Zend_Controller_Action
{
    protected $_userModel; // class name of the user model
    protected $_messageAfterLogin = array(
      'success' => 'You have successfully logged in'  
    );
    protected $_messageAfterLogout = array(
      'success' => 'You have successfully logged out'
    );
    protected $_redirectAfterLogin = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default',
    );
    protected $_redirectAfterLogout = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default',
    );
    protected $_redirectAfterNewPassword = array(
        'action'        => 'index',
        'controller'    => 'index',
        'module'        => 'default'
    );

    public function indexAction()
    {
		$request = $this->getRequest();
		$form = $this->getLoginForm();
        $model = new $this->_userModel;
        $log = $this->_helper->getLogger();
        if($request->isPost()) {
        	
        	$log->debug('login: is POST');
			if ($form->isValid($request->getPost())) {
				$values = $form->getValues();
				
				// we first search for that user
				$model  = $this->_helper->getEm()->getRepository($this->_userModel)
                        ->findOneByLogin($values['username']);
				if (!$model) { // we didnt find the user in DB
				    $this->_helper->FlashMessenger(array('error' => 'Given clinic e-mail not found in database'));
				} else {
				    if ($values['rememberMe'] == 1) {
				        $config = Zend_Registry::get('config'); 
				        $rememberMe = $config->usersAccounts->rememberMeTimeinHours;
				    } else {
				        $rememberMe = false;
				    }
				    
				    // authorizing the user
				    $result = $model->authorize($values['password'], $rememberMe);
				    if ($result === true) { // access granted
    					$this->_helper->FlashMessenger->clearCurrentMessages(); // to remove any ACL "You dont have access messages if any"
    					$this->_helper->FlashMessenger($this->_messageAfterLogin);
    					$this->_helper->Redirector(
                                $this->_redirectAfterLogin['action'],
                                $this->_redirectAfterLogin['controller'],
                                $this->_redirectAfterLogin['module']
                                );
    				} else {
    					$this->_helper->FlashMessenger(array('error' => 'Wrong login or password supplied'));
    				}
			    }
            } else {
					$this->_helper->FlashMessenger(array('error' => 'Please fill the form'));
			}
		} else {
			$log->debug('login: is not POST');
		}
		$this->view->form = $form;
        $this->view->headTitle($this->view->translate('Login'));
    }
    
    /**
     * To implement in extending controller 
     */
    public function getLoginForm()
    {
        
    }
    
    /**
     * To implement in extending controller 
     */
    public function getPasswordRecoveryForm()
    {
        
    }
    
    /**
    * To implement in extending controller 
    */   
    public function getNewPasswordForm()
    {
        
    }

	public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Session:: namespaceUnset('Zend_Auth');
        $this->_helper->FlashMessenger(
                $this->_messageAfterLogout
                );
        $this->_helper->Redirector(
                $this->_redirectAfterLogout['action'], 
                $this->_redirectAfterLogout['controller'], 
                $this->_redirectAfterLogout['module']
        );
    }
	
	public function passwordRecoveryAction() 
    {
        $this->view->headTitle($this->view->translate('Password recovery'));
        $form = $this->getPasswordRecoveryForm();
        $request = $this->getRequest();
        $model = new $this->_userModel;
        if ($request->isPost()) {
            $post = $request->getPost();
            if ($form->isValid($post)) {
                $values = $form->getValues();
                // we first search for that user
                $userModel = $this->_helper->getEm()->getRepository($this->_userModel)
                        ->findOneByLogin($values['username']);
                if (!$userModel) { // we didnt find the user in DB
                    $this->_helper->FlashMessenger(array('error' => 'No such user in database as: ' . $values['username']));
                } else {
                    $userModel->generatePasswordRecoveryToken();
                    $link = 'http://'. $_SERVER['HTTP_HOST'] . '/' . $request->getModuleName() . '/' . $request->getControllerName() . '/new-password-from-token/token/%s';
                    $userModel->sendPasswordRecoveryToken($link);
                    // we have to save token that our model generated
                    $this->_helper->getEm()->persist($userModel);
                    $this->_helper->getEm()->flush();
                    $this->_helper->FlashMessenger(array('success' => 'Recovery password e-mail send to: ' . $userModel->getEmailaddress()));
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
        $this->view->headTitle($this->view->translate('Setup Your new password'));
        
        $request    = $this->getRequest();
        $token      = $request->getParam('token', ''); // either get or post param
       
        if(empty($token)) {
            throw new Exception('No token given in URL', 404);
        }
        $model = new $this->_userModel;
        $userMapper = $model->getMapper();
 	   
        // we have to find user by token
        $user = $userMapper->findByToken($token);
        if(!$user) {
            throw new Expcetion('No user with this token found in DB', 500);
        }
        // checking if token is valid
        if (false === $user->tokenIsValid($token)) {
		    $this->_helper->FlashMessenger(array('error' => 'The token You have supplied in URL 
		    is either incorrect or not valid anymore, please generate new link.'));
		    $this->_helper->Redirector('password-recovery', 'index', 'user');  
        } 
        $form = $this->getNewPasswordForm(); 
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
               
               $this->_helper->Redirector(
                       $this->_redirectAfterNewPassword['action'],
                       $this->_redirectAfterNewPassword['controller'],
                       $this->_redirectAfterNewPassword['module']
               );
           } else {
               $this->_helper->FlashMessenger(array('warning' => 'There where some errors in the form'));
           }
        }
        $this->view->form = $form;
    }    
}

