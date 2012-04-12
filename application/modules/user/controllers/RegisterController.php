<?php

class User_RegisterController extends Zend_Controller_Action
{
    
    protected $_roleName = 'patient'; // role name that we want to register new user
    protected $_userModel = 'User_Model_User'; // class name of the user model
    

    public function init()
    {
        /* Initialize action controller here */
        // adding the GetDbHelper
        Zend_Controller_Action_HelperBroker::addPrefix('Me_Controller_Action_Helper_');
    }

	/**
	 * This action is resonsible for regestring new user in the system. 
	 *
	 */
    public function indexAction()
    {
        $this->view->headTitle(ucfirst($this->_roleName).' registration');
		$request 	= $this->getRequest();
		$form 		= $this->getRegistrationForm();
		$form->setDecorators(array(
            array('ViewScript', array('viewScript' => 'register/registrationForm.phtml'))
        ));
        		
		if($request->isPost()) {
			$post = $request->getPost();
			if($form->isValid($post)) { // data in the form are valid so we can register new user
			    $model = new $this->_userModel;
			    $model->setOptions($post);
			    
			    $roleMapper = new Acl_Model_RoleMapper();
			    $role = $roleMapper->findByName($this->_roleName);
			    $model->setRole($role);
			    
			    if(!$role) throw new Exception("There is no role ".$this->_roleName, 500);
			    $db = $this->_helper->getDb();
			    $db->beginTransaction();
			    $modelMapper = $model->getMapper();
			    $modelMapper->save($model);
			    $model->sendWelcomeEmail();
			    $this->_helper->FlashMessenger(array('success' => 'You have registered succesfuly. You can login now.'));
			    $this->_helper->Redirector('index', 'index', 'user');
			    $db->commit();
			} else {
			    $this->_helper->FlashMessenger(array('error' => 'Please fill out the form correctly'));
			}
		}
		
		$this->view->form = $form;
    }

	public function getRegistrationForm()
	{
		return new User_Form_Registration();
	}

}

