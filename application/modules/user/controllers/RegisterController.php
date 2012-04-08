<?php

class User_RegisterController extends Zend_Controller_Action
{
    
    protected $_roleName = 'patient'; // role name that we want to register new user
    protected $_userModel = 'User_Model_User'; // class name of the user model
    

    public function init()
    {
        /* Initialize action controller here */
    }

	/**
	 * This action is resonsible for regestring new user in the system. 
	 *
	 * Action use any model that implemenets << Br_Model_Interface_Registerable >> interface.
	 */
    public function indexAction()
    {
		$request 	= $this->getRequest();
		$form 		= $this->getRegistrationForm();
		
		$log = Zend_Registry::get('log');
		if($request->isPost()) {
			$post = $request->getPost();
			if($form->isValid($post)) { // data in the form are valid so we can register new user
			    $model = new $this->_userModel;
			    $model->setOptions($post);
			    
			    $roleMapper = new Acl_Model_RoleMapper();
			    $role = $roleMapper->findByName($this->_roleName);
			    
			    if(!$role) throw new Exception("There is no role ".$this->_roleName, 500);
			    
			    $mapperClassName = $this->_userModel.'Mapper';
			    $modelMapper = new $mapperClassName;
			    $modelMapper->save($model, $role);
			    $this->_helper->FlashMessenger(array('success' => 'You have registered succesfuly. You can login now.'));
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

